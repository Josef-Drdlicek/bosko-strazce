<!DOCTYPE html>
<html lang="cs" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Bosko Strážce' }} — Bosko Strážce</title>
    <meta name="description" content="{{ $metaDescription ?? 'Antikorupční monitorovací platforma pro město Boskovice — transparentní data z veřejných zdrojů' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 text-slate-900 antialiased" x-data="{ mobileMenu: false }">

<nav class="glass border-b border-white/20 sticky top-0 z-50 shadow-sm shadow-slate-900/5" x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 10" :class="scrolled ? 'shadow-md shadow-slate-900/10' : ''">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-lg shadow-indigo-500/25 group-hover:shadow-indigo-500/40 transition-shadow">
                        <svg class="h-5 w-5" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                    </div>
                    <span class="text-lg font-extrabold tracking-tight text-slate-900 group-hover:text-indigo-600 transition-colors">Bosko <span class="text-gradient">Strážce</span></span>
                </a>

                <div class="hidden lg:flex items-center gap-0.5">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg class="h-4 w-4 mr-1.5 opacity-60" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z"/></svg>
                        Přehled
                    </x-nav-link>
                    <x-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.*')">
                        Dokumenty
                    </x-nav-link>
                    <x-nav-link :href="route('contracts.index')" :active="request()->routeIs('contracts.*')">
                        Smlouvy
                    </x-nav-link>
                    <x-nav-link :href="route('subsidies.index')" :active="request()->routeIs('subsidies.*')">
                        Dotace
                    </x-nav-link>
                    <x-nav-link :href="route('entities.index')" :active="request()->routeIs('entities.*')">
                        Subjekty
                    </x-nav-link>
                    <x-nav-link :href="route('politicians.index')" :active="request()->routeIs('politicians.*')">
                        Politici
                    </x-nav-link>
                    <x-nav-link :href="route('signals.index')" :active="request()->routeIs('signals.*')">
                        Signály
                    </x-nav-link>
                    <x-nav-link :href="route('safety-map.index')" :active="request()->routeIs('safety-map.*')">
                        <svg class="h-4 w-4 mr-1 opacity-60" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        Mapa
                    </x-nav-link>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('search') }}" method="GET" class="hidden sm:block">
                    <div class="relative group">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                        <input
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Hledat..."
                            class="w-52 rounded-xl border-0 bg-slate-100/80 pl-9 pr-4 py-2 text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:shadow-lg focus:shadow-indigo-500/10 transition-all"
                        >
                    </div>
                </form>

                <a href="{{ route('ares.index') }}" class="hidden sm:inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-600/10 hover:bg-indigo-100 transition-colors">
                    ARES
                </a>

                <button
                    @click="mobileMenu = !mobileMenu"
                    class="lg:hidden inline-flex items-center justify-center rounded-xl p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors"
                    aria-label="Menu"
                >
                    <svg x-show="!mobileMenu" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    <svg x-show="mobileMenu" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileMenu" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="lg:hidden border-t border-slate-200/60">
        <div class="space-y-1 px-4 py-3">
            <a href="{{ route('dashboard') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">Přehled</a>
            <a href="{{ route('documents.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('documents.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">Dokumenty</a>
            <a href="{{ route('contracts.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('contracts.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">Smlouvy</a>
            <a href="{{ route('subsidies.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('subsidies.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">Dotace</a>
            <a href="{{ route('entities.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('entities.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">Subjekty</a>
            <a href="{{ route('politicians.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('politicians.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">Politici</a>
            <a href="{{ route('signals.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('signals.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">Signály</a>
            <a href="{{ route('safety-map.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('safety-map.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">Mapa bezpečnosti</a>
            <a href="{{ route('ares.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('ares.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">ARES</a>
            <form action="{{ route('search') }}" method="GET" class="pt-2">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Hledat..." class="w-full rounded-xl border-0 bg-slate-100 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            </form>
        </div>
    </div>
</nav>

<main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    {{ $slot }}
</main>

<footer class="relative overflow-hidden bg-slate-950 text-slate-300 mt-20">
    <div class="absolute inset-0 dot-grid opacity-30"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-1">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                    </div>
                    <span class="text-lg font-extrabold text-white">Bosko Strážce</span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Nezávislá platforma pro transparentní přehled veřejných dat města Boskovice. Nezastupujeme žádnou stranu — jen zobrazujeme veřejně dostupné informace.
                </p>
            </div>
            <div>
                <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Prozkoumejte</h3>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('documents.index') }}" class="text-sm text-slate-400 hover:text-white transition-colors">Dokumenty</a></li>
                    <li><a href="{{ route('contracts.index') }}" class="text-sm text-slate-400 hover:text-white transition-colors">Smlouvy</a></li>
                    <li><a href="{{ route('subsidies.index') }}" class="text-sm text-slate-400 hover:text-white transition-colors">Dotace</a></li>
                    <li><a href="{{ route('entities.index') }}" class="text-sm text-slate-400 hover:text-white transition-colors">Subjekty</a></li>
                    <li><a href="{{ route('politicians.index') }}" class="text-sm text-slate-400 hover:text-white transition-colors">Zastupitelé</a></li>
                    <li><a href="{{ route('signals.index') }}" class="text-sm text-slate-400 hover:text-white transition-colors">Signály</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Pro občany</h3>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('safety-map.index') }}" class="text-sm text-slate-400 hover:text-white transition-colors">Mapa bezpečnosti</a></li>
                    <li><a href="{{ route('search') }}" class="text-sm text-slate-400 hover:text-white transition-colors">Vyhledávání</a></li>
                    <li><a href="{{ route('ares.index') }}" class="text-sm text-slate-400 hover:text-white transition-colors">ARES registr</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Zdroje dat</h3>
                <ul class="space-y-2.5">
                    <li><a href="https://www.boskovice.cz" target="_blank" rel="noopener" class="text-sm text-slate-400 hover:text-white transition-colors">Web města Boskovice</a></li>
                    <li><a href="https://www.hlidacstatu.cz" target="_blank" rel="noopener" class="text-sm text-slate-400 hover:text-white transition-colors">Hlídač státu</a></li>
                    <li><a href="https://ares.gov.cz" target="_blank" rel="noopener" class="text-sm text-slate-400 hover:text-white transition-colors">ARES</a></li>
                    <li><a href="https://www.volby.cz" target="_blank" rel="noopener" class="text-sm text-slate-400 hover:text-white transition-colors">Volby.cz</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-12 border-t border-slate-800 pt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-between">
            <p class="text-xs text-slate-500">Data výhradně z veřejných zdrojů. Platforma neobviňuje — pouze zobrazuje veřejně dostupné informace.</p>
            <p class="text-xs text-slate-500">Boskovice &middot; {{ date('Y') }}</p>
        </div>
    </div>
</footer>

</body>
</html>
