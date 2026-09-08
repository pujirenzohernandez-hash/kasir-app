<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();
            
        $categories = Category::has('products')->orderBy('name')->get();

        return view('pos.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart'           => 'required|array|min:1',
            'cart.*.id'      => 'required|exists:products,id',
            'cart.*.qty'     => 'required|integer|min:1',
            'pay_amount'     => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,qris,transfer',
        ]);

        $cart = collect($request->input('cart'))
            ->groupBy('id')
            ->map(fn ($items, $productId) => [
                'id'  => (int) $productId,
                'qty' => $items->sum('qty'),
            ])
            ->values();

        try {
            $result = DB::transaction(function () use ($cart, $request) {
                // Kunci semua produk sampai transaksi selesai agar stok tidak terjual dua kali.
                $products = Product::whereIn('id', $cart->pluck('id'))
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $totalAmount = 0;
                foreach ($cart as $item) {
                    $product = $products->get($item['id']);
                    if (!$product || $product->stock < $item['qty']) {
                        $productName = $product?->name ?? 'Produk';
                        $stock = $product?->stock ?? 0;
                        throw new \DomainException("Stok untuk produk {$productName} tidak mencukupi (Tersisa: {$stock}).", 422);
                    }

                    $totalAmount += (float) $product->price * $item['qty'];
                }

                $totalAmount = round($totalAmount, 2);
                $payAmount = (float) $request->input('pay_amount');
                if ($payAmount < $totalAmount) {
                    throw new \DomainException('Uang pembayaran kurang dari total tagihan!', 422);
                }

                $transaction = Transaction::create([
                    'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)),
                    'user_id'        => Auth::id(),
                    'total_amount'   => $totalAmount,
                    'pay_amount'     => $payAmount,
                    'change_amount'  => round($payAmount - $totalAmount, 2),
                    'payment_method' => $request->input('payment_method'),
                ]);

                $detailItems = [];
                foreach ($cart as $item) {
                    $product = $products->get($item['id']);
                    $price = (float) $product->price;
                    $subtotal = round($price * $item['qty'], 2);

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id'     => $product->id,
                        'qty'            => $item['qty'],
                        'price'          => $price,
                        'subtotal'       => $subtotal,
                    ]);

                    $product->decrement('stock', $item['qty']);
                    $detailItems[] = [
                        'name'     => $product->name,
                        'qty'      => $item['qty'],
                        'price'    => $price,
                        'subtotal' => $subtotal,
                    ];
                }

                return compact('transaction', 'totalAmount', 'payAmount', 'detailItems');
            });

            $transaction = $result['transaction'];
            return response()->json([
                'status'         => 'success',
                'message'        => 'Transaksi berhasil disimpan!',
                'invoice'        => $transaction->invoice_number,
                'cashier'        => Auth::user()->name,
                'date'           => $transaction->created_at->format('d/m/Y H:i'),
                'total_amount'   => $result['totalAmount'],
                'pay_amount'     => $result['payAmount'],
                'change_amount'  => $transaction->change_amount,
                'payment_method' => $transaction->payment_method,
                'items'          => $result['detailItems'],
            ]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Gagal memproses transaksi.'], 500);
        }
        
    }
    public function sendWhatsappReceipt(Request $request)
{
    $request->validate([
        'phone' => 'required',
        'transaction_id' => 'required'
    ]);

    $transaction = Transaction::with(['details.product', 'user'])->find($request->transaction_id);

    if (!$transaction) {
        return response()->json(['message' => 'Transaksi tidak ditemukan!'], 444);
    }

    // Format Pesan Struk WhatsApp
    $msg = "🧾 *STRUK PEMBAYARAN KASIR POS*\n";
    $msg .= "----------------------------------------\n";
    $msg .= "No Invoice : " . $transaction->invoice_number . "\n";
    $msg .= "Tanggal    : " . $transaction->created_at->format('d/m/Y H:i') . "\n";
    $msg .= "Kasir      : " . ($transaction->user->name ?? 'Kasir') . "\n";
    $msg .= "----------------------------------------\n\n";

    $msg .= "*DETAIL BELANJA:*\n";
    foreach ($transaction->details as $detail) {
        $msg .= "• " . $detail->product->name . "\n";
        $msg .= "  " . $detail->qty . " x Rp " . number_format($detail->price, 0, ',', '.') . " = Rp " . number_format($detail->subtotal, 0, ',', '.') . "\n";
    }

    $msg .= "\n----------------------------------------\n";
    $msg .= "*Total Tagihan : Rp " . number_format($transaction->total_amount, 0, ',', '.') . "*\n";
    $msg .= "Bayar         : Rp " . number_format($transaction->pay_amount, 0, ',', '.') . "\n";
    $msg .= "Kembalian     : Rp " . number_format($transaction->change_amount, 0, ',', '.') . "\n";
    $msg .= "Metode        : " . strtoupper($transaction->payment_method) . "\n";
    $msg .= "----------------------------------------\n";
    $msg .= "Terima kasih telah berbelanja! 🙏";

    try {
        // Kirim request ke Fonnte API
        $response = Http::withHeaders([
            'Authorization' => 'TOKEN_FONNTE_ANDA', // Ganti dengan Token dari fonnte.com
        ])->post('https://api.fonnte.com/send', [
            'target' => $request->phone,
            'message' => $msg,
        ]);

        return response()->json(['message' => 'Struk WA berhasil dikirim!']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Gagal mengirim WA: ' . $e->getMessage()], 500);
    }
}
}