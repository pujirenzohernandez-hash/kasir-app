<x-app-layout>
    <div class="space-y-6">

        <!-- Header Dashboard -->
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Dashboard Utama</h1>
            <p class="text-sm text-slate-500">Ringkasan aktivitas kasir, statistik performa, dan stok barang.</p>
        </div>

        <!-- GRID WIDGET UTAMA (4 Kartu Ringkasan Top) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- Widget 1: Penjualan Hari Ini -->
            <a href="{{ route('laporan.index') }}" class="block bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all group">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Penjualan Hari Ini</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">💰</div>
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white">Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                <p class="text-xs mt-1.5 {{ $salesGrowth >= 0 ? 'text-emerald-500' : 'text-rose-500' }} font-bold flex items-center gap-1">
                    <span>{{ $salesGrowth >= 0 ? '↑' : '↓' }} {{ number_format(abs($salesGrowth), 1) }}%</span>
                    <span class="text-slate-400 font-normal">dibanding kemarin</span>
                </p>
            </a>

            <!-- Widget 2: Total Transaksi -->
            <a href="{{ route('laporan.index') }}" class="block bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm hover:shadow-md hover:border-blue-200 transition-all group">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Transaksi</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">🧾</div>
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white">{{ $todayTransactionsCount }}</h3>
                <p class="text-xs text-slate-400 mt-1.5">Transaksi berhasil hari ini</p>
            </a>

            <!-- Widget 3: Total Produk -->
            <a href="{{ route('products.index') }}" class="block bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm hover:shadow-md hover:border-purple-200 transition-all group">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Produk</span>
                    <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-500/10 text-purple-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">📦</div>
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white">{{ $totalProducts }}</h3>
                <p class="text-xs text-slate-400 mt-1.5">Tersedia dalam katalog</p>
            </a>

            <!-- Widget 4: Stok Menipis -->
            <a href="{{ route('products.index') }}" class="block bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm hover:shadow-md hover:border-amber-200 transition-all group">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stok Menipis</span>
                    <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">⚠️</div>
                </div>
                <h3 class="text-xl font-black text-amber-600 dark:text-amber-500">{{ $lowStockProducts->count() }}</h3>
                <p class="text-xs text-amber-500 font-semibold mt-1.5">Perlu restock segera (<= 5)</p>
            </a>

        </div>

        <!-- SECTION TENGAH: GRAFIK & PRODUK TERLARIS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Grafik Tren Penjualan -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm">
                <h3 class="font-bold text-slate-800 dark:text-white text-sm mb-1">Grafik Tren Penjualan</h3>
                <p class="text-xs text-slate-400 mb-4">Pendapatan harian selama 7 hari terakhir</p>
                <div class="h-64">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Produk Terlaris -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800 dark:text-white text-sm flex items-center gap-2">
                        <span>🔥</span> Produk Terlaris
                    </h3>
                </div>
                
                <div class="space-y-2.5">
                    @forelse($topProducts as $index => $item)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-700/40">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-lg bg-emerald-500 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                                {{ $index + 1 }}
                            </span>
                            @if(isset($item->product->image) && file_exists(public_path('storage/' . $item->product->image)))
                                <img src="{{ asset('storage/' . $item->product->image) }}" class="w-7 h-7 object-cover rounded-md">
                            @endif
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200">
                                {{ $item->product->name ?? 'Produk Dihapus' }}
                            </span>
                        </div>
                        <span class="text-xs font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400 px-2.5 py-1 rounded-lg">
                            {{ $item->total_qty }} pcs
                        </span>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 text-center py-6">Belum ada data penjualan.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- SECTION BAWAH: PERLU RESTOCK SEGERA & AKTIVITAS TRANSAKSI HARI INI -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- 1. Perlu Restock Segera -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm">
                <h3 class="font-bold text-slate-800 dark:text-white text-sm mb-4 flex items-center gap-2">
                    <span>⚠️</span> Perlu Restock Segera
                </h3>

                <div class="space-y-3">
                    @forelse($lowStockProducts as $prod)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50/60 dark:bg-amber-500/10 border border-amber-100/80 dark:border-amber-500/20">
                        <div class="flex items-center gap-3">
                            @if($prod->image && file_exists(public_path('storage/' . $prod->image)))
                                <img src="{{ asset('storage/' . $prod->image) }}" class="w-8 h-8 object-cover rounded-lg border border-amber-200/60">
                            @else
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-xs">📦</div>
                            @endif
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $prod->name }}</span>
                        </div>
                        <span class="text-xs font-bold bg-amber-500 text-white px-2.5 py-1 rounded-md shadow-sm">
                            Sisa: {{ $prod->stock }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-6 text-slate-400 text-xs">
                        Semua stok barang dalam kondisi aman.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- 2. Aktivitas Transaksi Hari Ini -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm">
                <h3 class="font-bold text-slate-800 dark:text-white text-sm mb-4">Aktivitas Transaksi Hari Ini</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            @forelse($recentTransactions as $trx)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition">
                                <td class="py-3 font-semibold text-emerald-600 dark:text-emerald-400">{{ $trx->invoice_number }}</td>
                                <td class="py-3 text-slate-400 dark:text-slate-400">{{ $trx->user->name ?? 'Administrator' }}</td>
                                <td class="py-3 font-bold text-emerald-600 dark:text-emerald-400 text-right">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                                <td class="py-3 text-slate-400 text-right pl-3">{{ $trx->created_at->format('H:i') }} WIB</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400">Belum ada transaksi hari ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    <!-- Script Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Penjualan (Rp)',
                        data: @json($chartData),
                        borderColor: '#10b981',
                        borderWidth: 3,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10b981',
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: 'rgba(226, 232, 240, 0.5)' } }
                    }
                }
            });
        });
    </script>
</x-app-layout>