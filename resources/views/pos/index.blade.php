<x-app-layout>
    <div x-data="posSystem()" 
         x-init="initShortcuts()"
        x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Kolom Kiri: Katalog Produk (8 cols) -->
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col justify-between">
            <div>
                <!-- Barcode Scanner & Search Input -->
                <div class="mb-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="relative flex-1">
                        <input 
                            type="text" 
                            x-ref="searchInput"
                            x-model="search" 
                            @keydown.enter.prevent="scanBarcode()"
                            placeholder="🔍 Scan Barcode atau Cari Nama Produk... (Tekan F2)" 
                            class="w-full bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 text-slate-800 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 shadow-sm"
                        >
                    </div>
                </div>

                <!-- Tab Kategori Filter -->
                <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-3 scrollbar-thin">
                    <button 
                        @click="selectedCategory = ''"
                        :class="selectedCategory === '' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                        class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition"
                    >
                        ✨ Semua Produk
                    </button>
                    @foreach($categories as $category)
                    <button 
                        @click="selectedCategory = '{{ $category->id }}'"
                        :class="selectedCategory === '{{ $category->id }}' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                        class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition"
                    >
                        {{ $category->name }}
                    </button>
                    @endforeach
                </div>

                <!-- Grid Produk -->
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5 max-h-[62vh] overflow-y-auto pr-1">
                    @forelse($products as $product)
                    <div 
                        x-show="filterProduct('{{ strtolower($product->name) }}', '{{ strtolower($product->code) }}', '{{ $product->category_id }}')"
                        @click="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->stock }}, '{{ $product->image_url }}')"
                        class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 hover:border-emerald-500/50 p-3.5 rounded-2xl cursor-pointer transition flex flex-col justify-between group shadow-sm hover:shadow-md select-none"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-2">
                                <span class="text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 px-2 py-0.5 rounded-md">
                                    Stok: {{ $product->stock }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $product->code }}</span>
                            </div>

                            <div class="flex items-center gap-3 mb-1">
                                @if($product->image && file_exists(public_path('storage/' . $product->image)))
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-xl border border-slate-200 dark:border-slate-700 flex-shrink-0 shadow-sm">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700/70 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-xl flex-shrink-0 text-slate-400">
                                        🛒
                                    </div>
                                @endif
                                <h4 class="font-bold text-slate-800 dark:text-white text-xs line-clamp-2 group-hover:text-emerald-500 transition">
                                    {{ $product->name }}
                                </h4>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700/50 flex justify-between items-end">
                            <div>
                                <p class="text-[10px] text-slate-400">Harga</p>
                                <p class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>
                            </div>
                            <span class="text-xs bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-500 group-hover:text-slate-950 p-1.5 rounded-lg transition font-bold">
                                +
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-16 text-center text-slate-400 text-sm">
                        Belum ada produk aktif yang memiliki stok. Silakan tambahkan produk di menu Produk.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Petunjuk Shortcut Keyboard -->
            <div class="mt-4 hidden md:flex items-center gap-4 text-xs text-slate-400">
                <span class="bg-slate-200 dark:bg-slate-800 px-2 py-1 rounded font-mono font-bold text-slate-700 dark:text-slate-300">F2</span> Cari / Scan Barcode
                <span class="bg-slate-200 dark:bg-slate-800 px-2 py-1 rounded font-mono font-bold text-slate-700 dark:text-slate-300">F8</span> Bayar & Simpan
            </div>
        </div>

        <!-- Kolom Kanan: Keranjang & Pembayaran (4 cols) -->
        <div class="lg:col-span-5 xl:col-span-4 bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-5 flex flex-col h-[85vh] shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3 mb-3">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>🛒</span> Keranjang Belanja
                    <span x-show="cart.length > 0" class="text-xs font-bold bg-emerald-500 text-slate-950 px-2 py-0.5 rounded-full" x-text="cart.reduce((s, i) => s + i.qty, 0)"></span>
                </h3>
                <button @click="clearCart()" x-show="cart.length > 0" class="text-xs text-rose-500 hover:underline font-semibold">Kosongkan</button>
            </div>

            <!-- Item Keranjang -->
            <div class="flex-1 overflow-y-auto space-y-2.5 pr-1">
                <template x-if="cart.length === 0">
                    <div class="text-center text-slate-400 py-24 text-xs">
                        <span class="text-3xl block mb-2">🛍️</span>
                        Keranjang masih kosong.<br>Klik produk atau scan barcode.
                    </div>
                </template>

                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700/50 flex items-center justify-between gap-2.5">
                        <template x-if="item.image">
                            <img :src="item.image" class="w-9 h-9 object-cover rounded-lg border border-slate-200 dark:border-slate-700 flex-shrink-0">
                        </template>
                        <template x-if="!item.image">
                            <div class="w-9 h-9 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs flex-shrink-0">
                                🛒
                            </div>
                        </template>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-800 dark:text-white truncate" x-text="item.name"></p>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold mt-0.5" x-text="formatRupiah(item.price)"></p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button @click="updateQty(index, -1)" class="w-6 h-6 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-white rounded-md font-bold text-xs flex items-center justify-center">-</button>
                            <span class="text-xs font-bold text-slate-800 dark:text-white w-5 text-center" x-text="item.qty"></span>
                            <button @click="updateQty(index, 1)" class="w-6 h-6 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-white rounded-md font-bold text-xs flex items-center justify-center">+</button>
                        </div>
                        <button @click="removeItem(index)" class="text-rose-500 hover:text-rose-400 text-xs px-1">✕</button>
                    </div>
                </template>
            </div>

            <!-- Input Nomor WA Pelanggan -->
<div class="mb-2">
    <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">No. WhatsApp Pelanggan (Opsional)</label>
    <input 
        type="text" 
        id="customerPhone" 
        placeholder="Contoh: 081234567890" 
        class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500"
    >
</div>

            <!-- Total & Pembayaran -->
            <div class="border-t border-slate-200 dark:border-slate-700 pt-3 mt-auto space-y-3">
                <!-- Total Tagihan -->
                <div class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/80 p-3 rounded-xl border border-slate-200 dark:border-slate-700/60">
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Tagihan</span>
                    <span class="text-xl font-black text-emerald-600 dark:text-emerald-400" x-text="formatRupiah(totalAmount)"></span>
                </div>

                <!-- Metode Pembayaran -->
                <div>
                    <label class="text-[10px] text-slate-400 block mb-1 font-semibold uppercase tracking-wider">Metode Pembayaran</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button @click="paymentMethod = 'cash'" :class="paymentMethod === 'cash' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400'" class="py-1.5 rounded-lg text-xs transition">💵 Tunai</button>
                        <button @click="paymentMethod = 'qris'; payAmount = totalAmount" :class="paymentMethod === 'qris' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400'" class="py-1.5 rounded-lg text-xs transition">📱 QRIS</button>
                        <button @click="paymentMethod = 'transfer'; payAmount = totalAmount" :class="paymentMethod === 'transfer' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400'" class="py-1.5 rounded-lg text-xs transition">🏦 Transfer</button>
                    </div>
                </div>

                <!-- Input Jumlah Bayar & Quick Amounts -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Bayar (Rp)</label>
                        <!-- Tombol Uang Pas -->
                        <button @click="payAmount = totalAmount" class="text-[10px] font-bold text-emerald-500 hover:underline">Uang Pas</button>
                    </div>
                    <input 
                        type="number" 
                        x-ref="payInput"
                        x-model.number="payAmount" 
                        placeholder="0" 
                        class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-3 py-2 font-bold text-sm focus:outline-none focus:border-emerald-500"
                    >
                    <!-- Quick Nominal Buttons -->
                    <div class="grid grid-cols-4 gap-1.5 mt-2" x-show="paymentMethod === 'cash'">
                        <button @click="payAmount = (payAmount || 0) + 10000" type="button" class="py-1 text-[10px] font-semibold bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg">+10k</button>
                        <button @click="payAmount = (payAmount || 0) + 20000" type="button" class="py-1 text-[10px] font-semibold bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg">+20k</button>
                        <button @click="payAmount = (payAmount || 0) + 50000" type="button" class="py-1 text-[10px] font-semibold bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg">+50k</button>
                        <button @click="payAmount = (payAmount || 0) + 100000" type="button" class="py-1 text-[10px] font-semibold bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg">+100k</button>
                    </div>
                </div>

                <!-- Nominal Kembalian -->
                <div class="flex justify-between items-center text-xs py-1 border-t border-slate-100 dark:border-slate-700/50">
                    <span class="text-slate-400">Kembalian:</span>
                    <span class="font-extrabold" :class="changeAmount >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500'" x-text="formatRupiah(changeAmount)"></span>
                </div>

                <!-- Tombol Submit -->
                <button 
                    @click="submitTransaction()"
                    :disabled="cart.length === 0 || payAmount < totalAmount || loading"
                    class="w-full bg-emerald-500 hover:bg-emerald-400 disabled:bg-slate-200 dark:disabled:bg-slate-700 disabled:text-slate-400 text-slate-950 font-bold py-3 rounded-xl transition shadow-lg shadow-emerald-500/20 text-sm"
                >
                    <span x-show="!loading">Proses & Selesai (F8)</span>
                    <span x-show="loading">Memproses...</span>
                </button>
            </div>
        </div>

        <!-- Modal Struk Pembayaran -->
        <div 
            x-show="receiptModal" 
            x-cloak
            style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div 
                @click.away="editModal = false" 
                class="bg-white text-slate-900 border border-slate-200 rounded-2xl w-full max-w-sm p-6 shadow-2xl flex flex-col justify-between"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
            >
                <!-- Struk Fisik (Print Target) -->
                <div id="printable-receipt" class="font-mono text-xs text-slate-900 p-2">
                    <div class="text-center pb-3 border-b border-dashed border-slate-300 mb-3">
                        <h2 class="text-base font-black tracking-wider uppercase">{{ config('RENZO', 'RENZO') }}</h2>
                        <p class="text-[11px] text-slate-600">Minimarket & Retail System</p>
                        <p class="text-[10px] text-slate-500 mt-1" x-text="receiptData.date"></p>
                    </div>

                    <div class="space-y-1 mb-3 text-[11px]">
                        <div class="flex justify-between">
                            <span class="text-slate-500">No. Faktur:</span>
                            <span class="font-bold" x-text="receiptData.invoice"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Kasir:</span>
                            <span x-text="receiptData.cashier"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Metode:</span>
                            <span class="uppercase font-semibold" x-text="receiptData.payment_method"></span>
                        </div>
                    </div>

                    <div class="border-t border-b border-dashed border-slate-300 py-2.5 my-2 space-y-1.5">
                        <template x-for="item in receiptData.items" :key="item.name">
                            <div>
                                <div class="font-bold text-slate-900" x-text="item.name"></div>
                                <div class="flex justify-between text-slate-600 text-[11px]">
                                    <span x-text="item.qty + ' x ' + formatRupiah(item.price)"></span>
                                    <span class="font-semibold text-slate-900" x-text="formatRupiah(item.subtotal)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="space-y-1 pt-1 text-[11px]">
                        <div class="flex justify-between font-extrabold text-sm text-slate-900">
                            <span>TOTAL:</span>
                            <span x-text="formatRupiah(receiptData.total_amount)"></span>
                        </div>
                        <div class="flex justify-between text-slate-700">
                            <span>BAYAR:</span>
                            <span x-text="formatRupiah(receiptData.pay_amount)"></span>
                        </div>
                        <div class="flex justify-between font-bold text-slate-900">
                            <span>KEMBALI:</span>
                            <span x-text="formatRupiah(receiptData.change_amount)"></span>
                        </div>
                    </div>

                    <div class="text-center pt-4 border-t border-dashed border-slate-300 mt-4 text-[10px] text-slate-500">
                        Terima Kasih atas Kunjungan Anda!<br>Barang yang sudah dibeli tidak dapat ditukar.
                    </div>
                </div>

                <!-- Tombol Aksi Modal -->
                <div class="flex gap-2 pt-4 border-t border-slate-100 mt-4">
                    <button 
                        @click="printReceipt()" 
                        type="button" 
                        class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-md"
                    >
                        🖨️ Cetak Struk
                    </button>
                    <button 
                        @click="newTransaction()" 
                        type="button" 
                        class="flex-1 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold py-2.5 rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-md"
                    >
                        ➕ Transaksi Baru
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Print Stylesheet -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #printable-receipt, #printable-receipt * {
                visibility: visible;
            }
            #printable-receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 80mm;
                padding: 0;
                margin: 0;
            }
        }
    </style>

    <!-- Script Alpine.js POS System -->
    <script>
        function posSystem() {
            return {
                search: '',
                selectedCategory: '',
                cart: [],
                payAmount: 0,
                paymentMethod: 'cash',
                loading: false,
                receiptModal: false,
                receiptData: {
                    invoice: '',
                    cashier: '',
                    date: '',
                    total_amount: 0,
                    pay_amount: 0,
                    change_amount: 0,
                    payment_method: 'cash',
                    items: []
                },
                productsList: @json($products),

                initShortcuts() {
                    window.addEventListener('keydown', (e) => {
                        if (e.key === 'F2') {
                            e.preventDefault();
                            this.$refs.searchInput.focus();
                        } else if (e.key === 'F8') {
                            e.preventDefault();
                            if (this.cart.length > 0 && this.payAmount >= this.totalAmount && !this.loading) {
                                this.submitTransaction();
                            }
                        }
                    });
                },

                filterProduct(name, code, categoryId) {
                    let matchesCategory = !this.selectedCategory || categoryId == this.selectedCategory;
                    if (!matchesCategory) return false;
                    if (!this.search) return true;
                    return name.includes(this.search.toLowerCase()) || code.includes(this.search.toLowerCase());
                },

                scanBarcode() {
                    let term = this.search.trim().toLowerCase();
                    if (!term) return;
                    let match = this.productsList.find(p => p.code.toLowerCase() === term || p.name.toLowerCase() === term);
                    if (match) {
                        this.addToCart(match.id, match.name, match.price, match.stock, match.image_url);
                        this.search = '';
                    } else {
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Produk dengan kode tersebut tidak ditemukan!', type: 'warning' } }));
                    }
                },

                addToCart(id, name, price, stock, image = null) {
                    let existing = this.cart.find(item => item.id === id);
                    if (existing) {
                        if (existing.qty < stock) {
                            existing.qty++;
                            existing.subtotal = existing.qty * price;
                            window.dispatchEvent(new CustomEvent('notify', { detail: { message: name + ' (+1)', type: 'success' } }));
                        } else {
                            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Stok telah mencapai batas maksimum (' + stock + ')!', type: 'warning' } }));
                        }
                    } else {
                        this.cart.push({ id, name, price, qty: 1, subtotal: price, maxStock: stock, image: image });
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: name + ' masuk keranjang', type: 'success' } }));
                    }
                },


                updateQty(index, change) {
                    let item = this.cart[index];
                    if (change === 1 && item.qty >= item.maxStock) {
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Stok tidak mencukupi (Maks: ' + item.maxStock + ')!', type: 'warning' } }));
                        return;
                    }
                    item.qty += change;
                    if (item.qty <= 0) {
                        this.cart.splice(index, 1);
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Item dihapus dari keranjang', type: 'error' } }));
                    } else {
                        item.subtotal = item.qty * item.price;
                    }
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                },

                clearCart() {
                    if (confirm('Kosongkan keranjang belanja?')) {
                        this.cart = [];
                        this.payAmount = 0;
                    }
                },

                get totalAmount() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },

                get changeAmount() {
                    return this.payAmount ? this.payAmount - this.totalAmount : 0;
                },

                formatRupiah(number) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(number || 0);
                },

                async submitTransaction() {
                    if (this.cart.length === 0) return;
                    if (this.payAmount < this.totalAmount) {
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Nominal pembayaran kurang!', type: 'warning' } }));
                        return;
                    }

                    this.loading = true;
                    try {
                        let response = await fetch("{{ route('pos.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                cart: this.cart,
                                pay_amount: this.payAmount,
                                payment_method: this.paymentMethod
                            })
                        });

                        let data = await response.json();

                        if (response.ok && data.status === 'success') {
                            this.receiptData = {
                                invoice: data.invoice,
                                cashier: data.cashier,
                                date: data.date,
                                total_amount: data.total_amount,
                                pay_amount: data.pay_amount,
                                change_amount: data.change_amount,
                                payment_method: data.payment_method,
                                items: data.items
                            };

                            this.receiptModal = true;
                            window.dispatchEvent(new CustomEvent('notify', { 
                                detail: { message: 'Transaksi Berhasil! Faktur: ' + data.invoice, type: 'success' } 
                            }));
                        } else {
                            window.dispatchEvent(new CustomEvent('notify', { 
                                detail: { message: data.message || 'Gagal memproses transaksi.', type: 'error' } 
                            }));
                        }
                        // Di dalam fungsi submitTransaction() setelah response.ok:
let phone = document.getElementById('customerPhone').value;

if (phone && data.transaction_id) {
    // Kirim WA secara background
    fetch("{{ route('pos.send-wa') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            phone: phone,
            transaction_id: data.transaction_id
        })
    });
}
                    } catch (e) {
                        window.dispatchEvent(new CustomEvent('notify', { 
                            detail: { message: 'Terjadi kesalahan jaringan saat memproses transaksi.', type: 'error' } 
                        }));
                    } finally {
                        this.loading = false;
                    }
                },

                printReceipt() {
                    window.print();
                },

                newTransaction() {
                    this.receiptModal = false;
                    this.cart = [];
                    this.payAmount = 0;
                    window.location.reload();
                }
            }
        }
    </script>
</x-app-layout>
