<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // 1. Penjualan Hari Ini & Kemarin
        $todaySales = Transaction::whereDate('created_at', $today)->sum('total_amount');
        $yesterdaySales = Transaction::whereDate('created_at', $yesterday)->sum('total_amount');

        // 2. Persentase Pertumbuhan Penjualan
        $salesGrowth = 0;
        if ($yesterdaySales > 0) {
            $salesGrowth = (($todaySales - $yesterdaySales) / $yesterdaySales) * 100;
        } elseif ($todaySales > 0) {
            $salesGrowth = 100;
        }

        // 3. Ringkasan Widget
        $todayTransactionsCount = Transaction::whereDate('created_at', $today)->count();
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock', '<=', 5)->get();

        // 4. Produk Terlaris (Top 5)
        $topProducts = TransactionDetail::select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->take(5)
            ->get();

        // 5. Transaksi Terbaru (Top 5)
        $recentTransactions = Transaction::with('user')
            ->latest()
            ->take(5)
            ->get();

        // 6. Data Grafik Tren Penjualan 7 Hari Terakhir
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('d M'); // Contoh: "21 Aug"
            $chartData[] = Transaction::whereDate('created_at', $date->toDateString())->sum('total_amount');
        }

        return view('dashboard', compact(
            'todaySales',
            'salesGrowth',
            'todayTransactionsCount',
            'totalProducts',
            'lowStockProducts',
            'topProducts',
            'recentTransactions',
            'chartLabels',
            'chartData'
        ));
    }
}