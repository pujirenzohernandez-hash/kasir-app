<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }" 
      x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Kasir App') }}</title>
    <!-- SCRIPT ANTI-FLICKER (Wajib di dalam <head>) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
    <!-- 2. Alpine.js dengan atribut defer -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-800 dark:bg-slate-950 dark:text-slate-100 min-h-screen transition-colors duration-300 relative overflow-hidden">
    
    <!-- Background Gradient Glow Orbs (Elemen Dekoratif Latar Belakang) -->
    <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden">
        <!-- Glow Atas Kanan -->
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-emerald-500/10 dark:bg-emerald-500/15 rounded-full blur-3xl"></div>
        <!-- Glow Tengah Kiri -->
        <div class="absolute top-1/3 -left-32 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-500/15 rounded-full blur-3xl"></div>
        <!-- Glow Bawah Kanan -->
        <div class="absolute -bottom-32 right-1/3 w-96 h-96 bg-teal-500/10 dark:bg-teal-500/15 rounded-full blur-3xl"></div>
    </div>

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-emerald/80 dark:bg-slate-900/80 backdrop-blur-xl border-r border-slate-200/80 dark:border-slate-800/80 flex flex-col flex-shrink-0 shadow-2xl transition-colors duration-300 z-20">
            <!-- Logo Brand -->
            <div class="p-5 border-b border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between">
                <span class="flex items-center gap-3">
                    <span class="bg-gradient-to-tr from-emerald-500 via-teal-400 to-indigo-500 text-slate-950 p-2.5 rounded-2xl text-lg font-black shadow-lg shadow-emerald-500/25">🛒</span>
                    <span class="text-xl font-extrabold tracking-wide text-slate-900 dark:text-white">App<span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-400">Kasir</span></span>
                </span>
            </div>

            <!-- Navigasi Menu -->
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl font-medium transition duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-950 font-bold shadow-lg shadow-emerald-500/25' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 dark:hover:text-white' }}">
                    <span>📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('pos.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl font-medium transition duration-200 {{ request()->routeIs('pos.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-950 font-bold shadow-lg shadow-emerald-500/25' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 dark:hover:text-white' }}">
                    <span>💻</span>
                    <span>Point of Sale</span>
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl font-medium transition duration-200 {{ request()->routeIs('products.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-950 font-bold shadow-lg shadow-emerald-500/25' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 dark:hover:text-white' }}">
                    <span>📦</span>
                    <span>Produk</span>
                </a>
                <a href="{{ route('categories.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl font-medium transition duration-200 {{ request()->routeIs('categories.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-950 font-bold shadow-lg shadow-emerald-500/25' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 dark:hover:text-white' }}">
                    <span>🏷️</span>
                    <span>Kategori</span>
                </a>
                <a href="{{ route('laporan.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl font-medium transition duration-200 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-950 font-bold shadow-lg shadow-emerald-500/25' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 dark:hover:text-white' }}">
                    <span>📈</span>
                    <span>Laporan</span>
                </a>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('users.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl font-medium transition duration-200 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-950 font-bold shadow-lg shadow-emerald-500/25' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 dark:hover:text-white' }}">
                        <span>⚙️</span>
                        <span>User Setting</span>
                    </a>
                @endif
            </nav>

            <!-- Profil User & Dark Mode Switcher -->
            <div class="p-4 border-t border-slate-200/80 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-950/40">
                <!-- Switcher Theme -->
                <button @click="darkMode = !darkMode" type="button" class="w-full flex items-center justify-between p-2.5 mb-3 rounded-xl bg-slate-200/70 dark:bg-slate-800/70 text-slate-700 dark:text-slate-300 font-semibold text-xs transition hover:opacity-90">
                    <span class="flex items-center gap-2">
                        <span x-show="!darkMode">☀️ Light Mode</span>
                        <span x-show="darkMode">🌙 Dark Mode</span>
                    </span>
                    <span class="w-8 h-4 bg-slate-300 dark:bg-emerald-500 rounded-full relative transition">
                        <span class="w-3 h-3 bg-white rounded-full absolute top-0.5 left-0.5 transition-transform" :class="{ 'translate-x-4': darkMode }"></span>
                    </span>
                </button>

                <div class="flex items-center gap-3 mb-3 px-1">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-slate-950 flex items-center justify-center font-bold text-sm shadow-md">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="text-sm font-bold text-slate-800 dark:text-white leading-tight">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">{{ ucfirst(Auth::user()->role ?? 'kasir') }}</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-center text-xs font-semibold text-rose-500 hover:bg-rose-500/10 py-2 rounded-xl transition">
                        🚪 Keluar Aplikasi
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Area -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <main class="p-8">
                {{ $slot }}
            </main>
        </div>

    </div>
    <!-- Component Notifikasi Toast Global -->
    <div x-data="{ 
            show: false, 
            message: '', 
            type: 'success',
            trigger(msg, t = 'success') {
                this.message = msg;
                this.type = t;
                this.show = true;
                setTimeout(() => { this.show = false; }, 3500);
            }
         }"
         x-init="
            @if(session('success')) trigger('{{ addslashes(session('success')) }}', 'success'); @endif
            @if(session('error')) trigger('{{ addslashes(session('error')) }}', 'error'); @endif
            @if(session('warning')) trigger('{{ addslashes(session('warning')) }}', 'warning'); @endif
         "
         @notify.window="trigger($event.detail.message, $event.detail.type)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-[-20px]"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-[-20px]"
         class="fixed top-5 right-5 z-50 flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl border text-sm font-bold backdrop-blur-xl"
         :class="{
            'bg-emerald-500/90 border-emerald-400 text-slate-950 shadow-emerald-500/20': type === 'success',
            'bg-rose-500/90 border-rose-400 text-white shadow-rose-500/20': type === 'error',
            'bg-amber-500/90 border-amber-400 text-slate-950 shadow-amber-500/20': type === 'warning'
         }"
         style="display: none;">
        
        <span x-show="type === 'success'" class="text-base">✅</span>
        <span x-show="type === 'error'" class="text-base">❌</span>
        <span x-show="type === 'warning'" class="text-base">⚠️</span>
        
        <span x-text="message"></span>
        
        <button @click="show = false" class="ml-3 text-xs opacity-70 hover:opacity-100">✕</button>
    </div>

</body>
</html>