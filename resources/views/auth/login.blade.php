<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Kasir POS</title>
    
    <!-- Tailwind CSS & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4 md:p-8">

    <!-- Container Frame Desktop Window -->
    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-12 min-h-[600px]">
        
        <!-- Sisi Kiri: Soft Teal Gradient (Sesuai Header UI Gambar) -->
        <div class="md:col-span-5 bg-gradient-to-br from-teal-200 via-emerald-100 to-teal-50 p-8 md:p-12 flex flex-col justify-between relative overflow-hidden">
            <!-- Elemen Dekorasi Aura Blur -->
            <div class="absolute -top-20 -left-20 w-60 h-60 bg-teal-300/40 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -right-20 w-60 h-60 bg-emerald-300/40 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3">
                    <span class="bg-gradient-to-tr from-emerald-500 via-teal-400 to-indigo-500 text-slate-950 p-2.5 rounded-2xl text-lg font-black shadow-lg shadow-emerald-500/25">🛒</span>
                    <span class="font-bold text-slate-900 text-lg tracking-wide">Kasir POS</span>
                </div>
            </div>

            <div class="relative z-10 my-auto py-12">
                <h1 class="text-3xl font-extrabold text-slate-900 leading-tight mb-3">Selamat Datang Kembali!</h1>
                <p class="text-slate-600 text-sm leading-relaxed">Kelola transaksi toko dan persediaan barang Anda dengan lebih cepat, aman, dan efisien.</p>
            </div>

            <div class="relative z-10 text-xs text-slate-500 font-medium">
                &copy; {{ date('Y') }} Minimarket POS System.
            </div>
        </div>

        <!-- Sisi Kanan: Form Login (Diadaptasi dari Gambar) -->
        <div class="md:col-span-7 bg-white p-8 md:p-14 flex flex-col justify-center" x-data="{ showPassword: false }">
            
            <div class="max-w-md w-full mx-auto">
                
                <!-- Title Header -->
                <div class="text-center md:text-left mb-8">
                    <h2 class="text-2xl font-black text-slate-900 tracking-wider">MASUK</h2>
                    <p class="text-slate-400 text-xs mt-1.5 font-medium">Silahkan Masukan Email dan Kata Sandi Anda</p>
                </div>

                <!-- Session Status / Error Alert -->
                @if (session('status'))
                    <div class="mb-4 text-xs font-bold text-emerald-600 bg-emerald-50 p-3 rounded-xl border border-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 text-xs font-bold text-rose-600 bg-rose-50 p-3 rounded-xl border border-rose-200">
                        Email atau Kata Sandi yang dimasukkan salah.
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Field Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-800 mb-2">
                            Email <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            placeholder="Masukkan Email" 
                            class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition placeholder:text-slate-300"
                        >
                    </div>

                    <!-- Field Kata Sandi -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-800 mb-2">
                            Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                id="password" 
                                :type="showPassword ? 'text' : 'password'" 
                                name="password" 
                                required 
                                placeholder="Masukkan Kata Sandi" 
                                class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl px-4 py-3 pr-11 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition placeholder:text-slate-300"
                            >
                            <!-- Toggle Visibility Icon -->
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword" 
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition p-1"
                            >
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21f-3-3m-3.95-3.95l-4.24-4.24" />
                                </svg>
                            </button>
                        </div>

                        <!-- Lupa Kata Sandi -->
                        @if (Route::has('password.request'))
                            <div class="text-right mt-2">
                                <a href="{{ route('password.request') }}" class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 transition">
                                    Lupa Kata Sandi?
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Login -->
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            class="w-full bg-slate-400 hover:bg-slate-800 text-white font-extrabold text-xs tracking-wider py-3.5 rounded-xl transition duration-200 uppercase shadow-lg shadow-slate-300"
                        >
                            LOGIN
                        </button>
                    </div>

                    <!-- Footer Link Daftar -->
                    <div class="text-center pt-4">
                        <p class="text-xs text-slate-400 font-medium">
                            Belum Punya Akun? 
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-teal-500 font-bold hover:underline">Daftar</a>
                            @else
                                <span class="text-teal-500 font-bold">Hubungi Admin</span>
                            @endif
                        </p>
                    </div>

                </form>

            </div>

        </div>

    </div>

</body>
</html>