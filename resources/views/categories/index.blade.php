<x-app-layout>
    <div x-data="{ 
            editModal: false, 
            editId: null, 
            editName: '', 
            openEdit(id, name) {
                this.editId = id;
                this.editName = name;
                this.editModal = true;
            }
        }">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Manajemen Kategori</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Kelola kelompok jenis produk Anda.</p>
            </div>
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
            <!-- Form Tambah -->
            <div class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 p-6 rounded-2xl h-fit shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span>➕</span> Tambah Kategori
                </h3>
                <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Nama Kategori</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Makanan, Minuman..." required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                    </div>
                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold py-2.5 rounded-xl transition shadow-md shadow-emerald-500/20 text-sm">
                        + Simpan Kategori
                    </button>
                </form>
            </div>

            <!-- Tabel Data -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 p-6 rounded-2xl shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-100 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 uppercase text-xs">
                            <tr>
                                <th class="p-3 rounded-l-lg">#</th>
                                <th class="p-3">Nama Kategori</th>
                                <th class="p-3">Jumlah Produk</th>
                                <th class="p-3 text-right rounded-r-lg">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            @forelse($categories as $category)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750/30 transition">
                                <td class="p-3 font-medium">{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                                <td class="p-3 font-semibold text-slate-900 dark:text-white">{{ $category->name }}</td>
                                <td class="p-3">
                                    <span class="bg-slate-100 dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded-lg text-xs font-bold">
                                        {{ $category->products_count }} produk
                                    </span>
                                </td>
                                <td class="p-3 text-right space-x-2">
                                    <button 
                                        @click="openEdit({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                        type="button" 
                                        class="px-2.5 py-1 text-xs font-semibold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500/20 rounded-lg transition"
                                    >
                                        ✏️ Edit
                                    </button>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Seluruh produk di bawah kategori ini juga akan terhapus.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 text-xs font-semibold bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 rounded-lg transition">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-slate-400 text-sm">Belum ada data kategori.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $categories->links() }}</div>
            </div>
        </div>

        <!-- Modal Edit Kategori -->
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
                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl w-full max-w-md p-6 shadow-2xl"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700 mb-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">✏️ Edit Kategori</h3>
                    <button @click="editModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">✕</button>
                </div>

                <form :action="'{{ url('categories') }}/' + editId" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="text-xs text-slate-500 dark:text-slate-400 block mb-1 font-semibold">Nama Kategori</label>
                        <input type="text" name="name" x-model="editName" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
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
