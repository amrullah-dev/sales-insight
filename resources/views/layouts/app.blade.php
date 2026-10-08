<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SalesInsight — Platform analisis data penjualan historis.">

    <title>{{ isset($title) ? $title . ' — SalesInsight' : 'SalesInsight — Analisis Penjualan' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#F8FAFC] text-slate-900">
    <div class="min-h-full flex flex-col md:flex-row">

        {{-- ─── Mobile Header ─── --}}
        <header class="md:hidden flex items-center justify-between px-4 py-3.5 bg-white border-b border-slate-200/80 sticky top-0 z-30">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                    </svg>
                </div>
                <span class="font-bold text-slate-900 text-[15px] tracking-tight">SalesInsight</span>
            </div>
            <button type="button"
                    id="mobile-nav-toggle"
                    aria-label="Buka menu navigasi"
                    aria-expanded="false"
                    class="p-2.5 -mr-1 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </header>

        {{-- ─── Sidebar ─── --}}
        <aside id="sidebar-menu"
               class="hidden md:flex flex-col w-full md:w-60 bg-white border-r border-slate-200/80 shrink-0 md:sticky md:top-0 md:h-screen md:overflow-y-auto z-20">

            {{-- Brand --}}
            <div class="px-5 py-5 hidden md:block">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-600 flex items-center justify-center shadow-sm">
                        <svg class="w-[18px] h-[18px] text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>
                    </div>
                    <div>
                        <span class="font-bold text-slate-900 text-[15px] tracking-tight block leading-tight">SalesInsight</span>
                        <span class="text-[11px] text-slate-400 font-medium block leading-tight">Analisis Penjualan</span>
                    </div>
                </div>
            </div>

            <div class="h-px bg-slate-100 mx-5 hidden md:block"></div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-0.5" aria-label="Menu utama">
                <p class="px-3 pt-2 pb-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                    Analisis
                </p>

                {{-- Dasbor --}}
                <a href="{{ route('dashboard') }}"
                   class="group flex items-center px-3 py-2 text-[13px] font-medium rounded-lg transition-all duration-150
                          {{ request()->routeIs('dashboard')
                              ? 'bg-indigo-50/80 text-indigo-700 font-semibold'
                              : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="mr-3 h-[18px] w-[18px] shrink-0 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                    Dasbor
                </a>

                {{-- Produk --}}
                <a href="{{ route('products.index') }}"
                   class="group flex items-center px-3 py-2 text-[13px] font-medium rounded-lg transition-all duration-150
                          {{ request()->routeIs('products.*')
                              ? 'bg-indigo-50/80 text-indigo-700 font-semibold'
                              : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="mr-3 h-[18px] w-[18px] shrink-0 {{ request()->routeIs('products.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>
                    Analisis Produk
                </a>

                {{-- Tren Penjualan --}}
                <a href="{{ route('trends.index') }}"
                   class="group flex items-center px-3 py-2 text-[13px] font-medium rounded-lg transition-all duration-150
                          {{ request()->routeIs('trends.*')
                              ? 'bg-indigo-50/80 text-indigo-700 font-semibold'
                              : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="mr-3 h-[18px] w-[18px] shrink-0 {{ request()->routeIs('trends.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                    </svg>
                    Tren Penjualan
                </a>
            </nav>

            {{-- Sidebar Footer --}}
            <div class="px-5 py-3 border-t border-slate-100">
                <div class="flex items-center space-x-2 text-[11px] text-slate-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>SalesInsight v1.0</span>
                </div>
            </div>
        </aside>

        {{-- ─── Main Content Area ─── --}}
        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            {{-- Page Header --}}
            <div class="bg-white border-b border-slate-200/80 px-4 sm:px-6 py-5 sticky top-0 md:static z-10">
                <div class="max-w-6xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight">
                            @yield('header-title', 'Dasbor')
                        </h1>
                        <p class="text-sm text-slate-500 mt-1">
                            @yield('header-description', 'Metrik dan analisis data penjualan historis')
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        @yield('header-actions')
                    </div>
                </div>
            </div>

            {{-- Page Content --}}
            <div class="flex-1 px-4 sm:px-6 py-6 md:py-8">
                <div class="max-w-6xl mx-auto">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    {{-- Mobile Navigation Toggle --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('mobile-nav-toggle');
            const sidebar = document.getElementById('sidebar-menu');
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', () => {
                    const isOpen = !sidebar.classList.toggle('hidden');
                    toggleBtn.setAttribute('aria-expanded', String(isOpen));
                });
            }
        });
    </script>
</body>
</html>
