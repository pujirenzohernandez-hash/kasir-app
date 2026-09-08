<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran #{{ $transaction->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm; /* Ukuran standar kertas printer thermal */
            margin: 0 auto;
            padding: 5px;
            font-size: 11px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }
        @media print {
            @page { margin: 0; }
            body { margin: 0 auto; }
        }
    </style>
</head>
<body>

    <div class="text-center">
        <h3 style="margin: 0;">KASIR POS</h3>
        <p style="margin: 2px 0;">Jl. Utama No. 123</p>
        <p style="margin: 2px 0;">Telp: 0812-3456-7890</p>
    </div>

    <div class="line"></div>

    <table>
        <tr>
            <td>No. Inv</td>
            <td class="text-right">{{ $transaction->invoice_number }}</td>
        </tr>
        <tr>
            <td>Tgl</td>
            <!-- Jam sudah otomatis terformat sesuai timezone Asia/Jakarta -->
            <td class="text-right">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td class="text-right">{{ $transaction->user->name ?? 'Kasir' }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <!-- Detail Barang -->
    <table>
        @foreach($transaction->details as $detail)
        <tr>
            <td colspan="2"><strong>{{ $detail->product->name }}</strong></td>
        </tr>
        <tr>
            <td>{{ $detail->qty }} x {{ number_format($detail->price, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="line"></div>

    <!-- Total & Pembayaran -->
    <table>
        <tr>
            <td><strong>TOTAL</strong></td>
            <td class="text-right"><strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td>Bayar ({{ strtoupper($transaction->payment_method) }})</td>
            <td class="text-right">Rp {{ number_format($transaction->pay_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-right">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <div class="text-center" style="margin-top: 10px;">
        <p style="margin: 2px 0;">-- Terima Kasih --</p>
        <p style="margin: 2px 0;">Barang yang sudah dibeli</p>
        <p style="margin: 2px 0;">tidak dapat ditukar/dikembalikan</p>
    </div>
<script>
    // Jalankan perintah print hanya jika halaman ini dibuka di tab baru
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            window.print();
        }, 500);
    });
</script>
</body>
</html>