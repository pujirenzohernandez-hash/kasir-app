<x-app-layout>
    <div x-data="{ 
            editModal: false, 
            editId: null, 
            editCode: '', 
            editName: '', 
            editCategoryId: '', 
            editPrice: 0, 
            editStock: 0,
            editImageUrl: null,
            imagePreview: null,
            editPreview: null,
            openEdit(id, code, name, categoryId, price, stock, imageUrl) {
                this.editId = id;
                this.editCode = code;
                this.editName = name;
                this.editCategoryId = categoryId;
                this.editPrice = price;
                this.editStock = stock;
                this.editImageUrl = imageUrl;
                this.editPreview = imageUrl;
                this.editModal = true;
            },
            previewFile(event, target) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        if (target === 'create') this.imagePreview = e.target.result;
                        if (target === 'edit') this.editPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }
        }">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Kelola Produk</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Daftar stok, harga, dan foto seluruh barang minimarket.</p>
            </div>

            <!-- Filter & Search Form -->
            <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap items-center gap-2">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="🔍 Cari nama/kode..." 
                    class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-3 py-2 text-xs focus:border-emerald-500 focus:outline-none"
                >
                <select 
                    name="category_id" 
                    onchange="this.form.submit()"
                    class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-3 py-2 text-xs focus:border-emerald-500 focus:outline-none"
                >
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @if(request('search') || request('category_id'))
                    <a href="{{ route('products.index') }}" class="px-2.5 py-2 text-xs bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:opacity-80">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 rounded-xl text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Input -->
            <div class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 p-6 rounded-2xl h-fit shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span>➕</span> Tambah Produk Baru
                </h3>
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Kode / Barcode</label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="Contoh: BRG-001" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-3.5 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Nama Produk</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Kopi Bubuk 200g" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-3.5 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Kategori</label>
                        <select name="category_id" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-3.5 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Harga (Rp)</label>
                            <input type="number" name="price" value="{{ old('price') }}" placeholder="0" min="0" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Stok Awal</label>
                            <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Foto Produk (Opsional)</label>
                        <input type="file" name="image" accept="image/*" @change="previewFile($event, 'create')" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-300 rounded-xl px-3 py-2 text-xs focus:border-emerald-500 focus:outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-500/20 file:text-emerald-600 dark:file:text-emerald-400">
                        <template x-if="imagePreview">
                            <div class="mt-2.5 flex items-center gap-3 p-2 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700">
                                <img :src="imagePreview" class="w-14 h-14 object-cover rounded-lg border border-slate-300 dark:border-slate-700 shadow-sm">
                                <span class="text-xs text-slate-500 dark:text-slate-400">Preview foto baru</span>
                            </div>
                        </template>
                    </div>
                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold py-2.5 rounded-xl transition shadow-md shadow-emerald-500/20 text-sm">
                        + Simpan Produk
                    </button>
                </form>
            </div>

            <!-- Tabel Produk -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 p-6 rounded-2xl shadow-sm overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-100 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 uppercase text-xs">
                        <tr>
                            <th class="p-3 rounded-l-lg">Foto</th>
                            <th class="p-3">Kode</th>
                            <th class="p-3">Nama Produk</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3">Harga</th>
                            <th class="p-3">Stok</th>
                            <th class="p-3 text-right rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @forelse($products as $product)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750/30 transition">
                            <td class="p-3">
                                @if($product->image && file_exists(public_path('storage/' . $product->image)))
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-11 h-11 object-cover rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                                @else
                                    <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center text-lg text-slate-400">
                                        📦
                                    </div>
                                @endif
                            </td>
                            <td class="p-3 font-mono text-emerald-600 dark:text-emerald-400 text-xs font-bold">{{ $product->code }}</td>
                            <td class="p-3 font-semibold text-slate-900 dark:text-white">{{ $product->name }}</td>
                            <td class="p-3">
                                <span class="bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 px-2.5 py-0.5 rounded-lg text-xs">
                                    {{ $product->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3 font-bold text-slate-900 dark:text-white">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="p-3">
                                <span class="font-bold px-2 py-0.5 rounded-md text-xs {{ $product->stock <= 5 ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : 'text-slate-700 dark:text-slate-300' }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="p-3 text-right space-x-2">
                                <button 
                                    @click="openEdit({{ $product->id }}, '{{ addslashes($product->code) }}', '{{ addslashes($product->name) }}', '{{ $product->category_id }}', {{ $product->price }}, {{ $product->stock }}, '{{ $product->image ? asset('storage/' . $product->image) : '' }}')"
                                    type="button" 
                                    class="px-2.5 py-1 text-xs font-semibold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500/20 rounded-lg transition"
                                >
                                    ✏️ Edit
                                </button>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-xs font-semibold bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 rounded-lg transition">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400 text-sm">Belum ada data produk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $products->links() }}</div>
            </div>
        </div>

        <!-- Modal Edit Produk -->
        <div 
            x-show="editModal" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div 
                @click.away="editModal = false" 
                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl w-full max-w-lg p-6 shadow-2xl"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700 mb-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">✏️ Edit Produk</h3>
                    <button @click="editModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">✕</button>
                </div>

                <form :action="'{{ url('products') }}/' + editId" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Kode / Barcode</label>
                            <input type="text" name="code" x-model="editCode" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-3.5 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Kategori</label>
                            <select name="category_id" x-model="editCategoryId" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-3.5 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Nama Produk</label>
                        <input type="text" name="name" x-model="editName" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-3.5 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Harga (Rp)</label>
                            <input type="number" name="price" x-model.number="editPrice" min="0" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Stok Saat Ini</label>
                            <input type="number" name="stock" x-model.number="editStock" min="0" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Ganti Foto Produk (Opsional)</label>
                        <input type="file" name="image" accept="image/*" @change="previewFile($event, 'edit')" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-300 rounded-xl px-3 py-2 text-xs focus:border-emerald-500 focus:outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-500/20 file:text-emerald-600 dark:file:text-emerald-400">
                        <template x-if="editPreview">
                            <div class="mt-2.5 flex items-center gap-3 p-2 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700">
                                <img :src="editPreview" class="w-14 h-14 object-cover rounded-lg border border-slate-300 dark:border-slate-700 shadow-sm">
                                <span class="text-xs text-slate-500 dark:text-slate-400">Foto produk saat ini / preview baru</span>
                            </div>
                        </template>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-md">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
