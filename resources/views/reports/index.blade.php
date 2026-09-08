<x-app-layout>
    <div x-data="{ 
            detailModal: false, 
            currentTrx: null,
            openDetail(trx) {
                this.currentTrx = trx;
                this.detailModal = true;
            },
            formatRupiah(number) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(number || 0);
            }
        }">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Laporan Penjualan</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Rekap riwayat transaksi toko berdasarkan periode tanggal.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button 
                    onclick="window.print()" 
                    type="button" 
                    class="bg-slate-900 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold px-4 py-2.5 rounded-xl transition text-xs flex items-center gap-2 shadow-sm"
                >
                    🖨️ Cetak Laporan
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 p-5 rounded-2xl shadow-sm">
                <span class="text-xs text-slate-400 uppercase font-bold tracking-wider block">Total Pendapatan</span>
                <span class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1 block">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-1 block">Periode terpilih</span>
            </div>

            <div class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 p-5 rounded-2xl shadow-sm">
                <span class="text-xs text-slate-400 uppercase font-bold tracking-wider block">Jumlah Transaksi</span>
                <span class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1 block">
                    {{ $totalTransactions ?? $transactions->total() }} Transaksi
                </span>
                <span class="text-[11px] text-slate-400 mt-1 block">Berhasil tercatat</span>
            </div>

            <div class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 p-5 rounded-2xl shadow-sm">
                <span class="text-xs text-slate-400 uppercase font-bold tracking-wider block">Rata-rata / Transaksi</span>
                <span class="text-2xl font-extrabold text-teal-600 dark:text-teal-400 mt-1 block">
                    Rp {{ number_format(($totalTransactions ?? $transactions->total()) > 0 ? $totalRevenue / ($totalTransactions ?? $transactions->total()) : 0, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-1 block">Nilai belanja rata-rata</span>
            </div>
        </div>

        <!-- Filter Tanggal -->
        <div class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 p-5 rounded-2xl mb-6 shadow-sm no-print">
            <form method="GET" action="{{ route('laporan.index') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                </div>
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold px-5 py-2.5 rounded-xl transition text-sm shadow-md shadow-emerald-500/20">
                    🔍 Terapkan Filter
                </button>
            </form>
        </div>

        <!-- Tabel Laporan -->
        <div class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 p-6 rounded-2xl shadow-sm overflow-x-auto print-area">
            <div class="hidden print-header mb-4 text-center">
                <h2 class="text-xl font-bold uppercase">{{ config('app.name', 'KASIR POS') }} - LAPORAN PENJUALAN</h2>
                <p class="text-xs text-slate-600">Periode: {{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }}</p>
                <p class="text-xs font-bold mt-1">Total Pendapatan: Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>

            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-100 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="p-3 rounded-l-lg">No. Faktur</th>
                        <th class="p-3">Tanggal & Waktu</th>
                        <th class="p-3">Kasir</th>
                        <th class="p-3">Total Belanja</th>
                        <th class="p-3">Bayar</th>
                        <th class="p-3">Kembalian</th>
                        <th class="p-3 text-right rounded-r-lg no-print">Rincian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750/30 transition">
                        <td class="p-3 font-mono text-emerald-600 dark:text-emerald-400 font-bold text-xs">{{ $trx->invoice_number }}</td>
                        <td class="p-3 text-slate-600 dark:text-slate-400 text-xs">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-3 text-slate-900 dark:text-white font-semibold">{{ $trx->user->name ?? 'Kasir' }}</td>
                        <td class="p-3 font-bold text-slate-900 dark:text-white">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                        <td class="p-3 text-slate-600 dark:text-slate-300 text-xs">Rp {{ number_format($trx->pay_amount, 0, ',', '.') }}</td>
                        <td class="p-3 text-slate-500 text-xs">Rp {{ number_format($trx->change_amount, 0, ',', '.') }}</td>
                        <td class="p-3 text-right no-print">
                            <button 
                                @click="openDetail({{ json_encode($trx) }})"
                                type="button"
                                class="px-2.5 py-1 text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20 rounded-lg transition"
                            >
                                👁️ Detail ({{ $trx->details->count() }} item)
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-slate-400 text-sm">Tidak ada riwayat transaksi pada periode tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4 no-print">{{ $transactions->links() }}</div>
        </div>

        <!-- Modal Detail Item Transaksi -->
        <div 
            x-show="detailModal" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm no-print"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div 
                @click.away="detailModal = false" 
                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl w-full max-w-lg p-6 shadow-2xl"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700 mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>🧾</span> Rincian Faktur <span class="font-mono text-emerald-500 text-base" x-text="currentTrx ? currentTrx.invoice_number : ''"></span>
                        </h3>
                        <p class="text-xs text-slate-400" x-text="currentTrx ? 'Kasir: ' + (currentTrx.user ? currentTrx.user.name : '-') : ''"></p>
                    </div>
                    <button @click="detailModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">✕</button>
                </div>

                <div class="max-h-72 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700/60 mb-4 pr-1">
                    <template x-if="currentTrx && currentTrx.details">
                        <template x-for="detail in currentTrx.details" :key="detail.id">
                            <div class="py-2.5 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white" x-text="detail.product ? detail.product.name : 'Produk (Dihapus)'"></p>
                                    <p class="text-xs text-slate-400" x-text="detail.qty + ' x ' + formatRupiah(detail.price)"></p>
                                </div>
                                <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400" x-text="formatRupiah(detail.subtotal)"></p>
                            </div>
                        </template>
                    </template>
                </div>

                <div class="bg-slate-50 dark:bg-slate-900/80 p-3 rounded-xl border border-slate-200 dark:border-slate-700/60 space-y-1 text-xs">
                    <div class="flex justify-between text-slate-500 dark:text-slate-400">
                        <span>Total Tagihan:</span>
                        <span class="font-bold text-slate-900 dark:text-white text-sm" x-text="formatRupiah(currentTrx ? currentTrx.total_amount : 0)"></span>
                    </div>
                    <div class="flex justify-between text-slate-500 dark:text-slate-400">
                        <span>Uang Diterima:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="formatRupiah(currentTrx ? currentTrx.pay_amount : 0)"></span>
                    </div>
                    <div class="flex justify-between text-slate-500 dark:text-slate-400">
                        <span>Kembalian:</span>
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400" x-text="formatRupiah(currentTrx ? currentTrx.change_amount : 0)"></span>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="button" @click="detailModal = false" class="px-5 py-2 rounded-xl text-xs font-bold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:opacity-80">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .print-header {
                display: block !important;
            }
            aside {
                display: none !important;
            }
            main {
                padding: 0 !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
        }
    </style>
</x-app-layout>
