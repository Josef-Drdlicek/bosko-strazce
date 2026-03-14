<x-layouts.app title="Přehled" metaDescription="Bosko Strážce sleduje veřejná data města Boskovice — smlouvy, dotace, dokumenty a firmy — a hledá v nich zajímavé souvislosti.">

    <div class="space-y-16">

        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-3xl bg-slate-950 px-6 py-16 text-white sm:px-12 sm:py-20 animate-fade-up">
            <div class="absolute inset-0 dot-grid opacity-20"></div>
            <div class="absolute -right-32 -top-32 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute -left-20 -bottom-20 h-64 w-64 rounded-full bg-violet-500/20 blur-3xl"></div>
            <div class="absolute right-1/4 top-1/3 h-48 w-48 rounded-full bg-sky-500/10 blur-3xl animate-float"></div>
            <div class="relative max-w-3xl">
                <span class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm px-4 py-1.5 text-xs font-semibold text-white/90 ring-1 ring-white/20">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                    </span>
                    Veřejná data &middot; Boskovice
                </span>
                <h1 class="text-4xl font-black tracking-tight sm:text-5xl lg:text-6xl">
                    Hlídáme <span class="text-gradient">veřejné peníze</span><br>
                    vašeho města
                </h1>
                <p class="mt-6 max-w-2xl text-lg text-slate-300 leading-relaxed">
                    Sledujeme smlouvy, dotace, dokumenty a firmy města Boskovice.
                    Automaticky hledáme neobvyklé vzorce a propojení &mdash; abyste vy nemuseli.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('signals.index') }}" class="group inline-flex items-center gap-2 rounded-xl bg-indigo-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 hover:bg-indigo-400 hover:shadow-indigo-400/30 transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        Zobrazit signály
                        <svg class="h-3.5 w-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                    <a href="{{ route('search') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-6 py-3 text-sm font-bold text-white ring-1 ring-white/20 hover:bg-white/20 transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                        Hledat firmu nebo osobu
                    </a>
                    <a href="{{ route('safety-map.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500/20 backdrop-blur-sm px-6 py-3 text-sm font-bold text-emerald-300 ring-1 ring-emerald-400/30 hover:bg-emerald-500/30 transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        Mapa bezpečnosti
                    </a>
                </div>
            </div>
        </div>

        {{-- ANIMATED STATS --}}
        <section class="reveal">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="group rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60 hover-lift text-center" x-data="counter()" data-target="{{ $stats['documents'] }}">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 mb-3 group-hover:scale-110 transition-transform">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                    </div>
                    <p class="text-3xl font-black text-slate-900 tabular-nums" x-text="formatted">0</p>
                    <p class="mt-1 text-sm font-medium text-slate-500">Dokumentů</p>
                </div>
                <div class="group rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60 hover-lift text-center" x-data="counter()" data-target="{{ $stats['contracts'] }}">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 mb-3 group-hover:scale-110 transition-transform">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/></svg>
                    </div>
                    <p class="text-3xl font-black text-slate-900 tabular-nums" x-text="formatted">0</p>
                    <p class="mt-1 text-sm font-medium text-slate-500">Smluv</p>
                </div>
                <div class="group rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60 hover-lift text-center" x-data="counter()" data-target="{{ $stats['entities'] }}">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-600 mb-3 group-hover:scale-110 transition-transform">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                    </div>
                    <p class="text-3xl font-black text-slate-900 tabular-nums" x-text="formatted">0</p>
                    <p class="mt-1 text-sm font-medium text-slate-500">Subjektů</p>
                </div>
                <div class="group rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60 hover-lift text-center" x-data="counter()" data-target="{{ $stats['subsidies'] }}">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 mb-3 group-hover:scale-110 transition-transform">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
                    </div>
                    <p class="text-3xl font-black text-slate-900 tabular-nums" x-text="formatted">0</p>
                    <p class="mt-1 text-sm font-medium text-slate-500">Dotací</p>
                </div>
            </div>
        </section>

        {{-- WHAT WE MONITOR --}}
        <section class="reveal">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900">Co monitorujeme</h2>
                    <p class="mt-1 text-sm text-slate-500">Klikněte na kategorii a prozkoumejte veřejná data města Boskovice.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('documents.index') }}" class="group relative rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60 hover-lift card-shine">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-400 to-sky-600 text-white shadow-lg shadow-sky-500/25 mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 group-hover:text-sky-600 transition-colors">Dokumenty</h3>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed">Úřední deska, zápisy zastupitelstva, vyhlášky, rozpočty a informace.</p>
                    <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-sky-600 opacity-0 group-hover:opacity-100 transition-opacity">
                        Prozkoumat
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </div>
                </a>
                <a href="{{ route('contracts.index') }}" class="group relative rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60 hover-lift card-shine">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-lg shadow-emerald-500/25 mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Smlouvy</h3>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed">Každá smlouva z Registru smluv &mdash; kdo, kolik, kdy.</p>
                    <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-emerald-600 opacity-0 group-hover:opacity-100 transition-opacity">
                        Prozkoumat
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </div>
                </a>
                <a href="{{ route('entities.index') }}" class="group relative rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60 hover-lift card-shine">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-400 to-violet-600 text-white shadow-lg shadow-violet-500/25 mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 group-hover:text-violet-600 transition-colors">Subjekty</h3>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed">Firmy, organizace a osoby, které obchodují s městem.</p>
                    <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-violet-600 opacity-0 group-hover:opacity-100 transition-opacity">
                        Prozkoumat
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </div>
                </a>
                <a href="{{ route('subsidies.index') }}" class="group relative rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60 hover-lift card-shine">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-white shadow-lg shadow-amber-500/25 mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 group-hover:text-amber-600 transition-colors">Dotace</h3>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed">Peníze z ministerstev, EU fondů a vlastních programů.</p>
                    <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-amber-600 opacity-0 group-hover:opacity-100 transition-opacity">
                        Prozkoumat
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </div>
                </a>
            </div>
        </section>

        {{-- SIGNALS --}}
        <section class="reveal">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900">Co stojí za pozornost</h2>
                    <p class="mt-1 text-sm text-slate-500">Automaticky nalezené zajímavosti &mdash; ne obvinění, ale vodítka k bližšímu zkoumání.</p>
                </div>
                <a href="{{ route('signals.index') }}" class="hidden sm:inline-flex items-center gap-1.5 rounded-xl bg-rose-500 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-rose-500/25 hover:bg-rose-400 transition-all">
                    Všechny signály
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <a href="{{ route('signals.index') }}#koncentrace" class="group relative rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60 hover-lift card-shine overflow-hidden">
                    <div class="absolute top-0 right-0 h-24 w-24 bg-gradient-to-bl from-rose-100 to-transparent rounded-bl-3xl"></div>
                    <div class="relative">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-rose-400 to-rose-600 text-white shadow-lg shadow-rose-500/25 mb-4">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-rose-600 transition-colors">Neobvyklé zakázky</h3>
                        <p class="mt-2 text-sm text-slate-500 leading-relaxed">Firmy, které od města dostaly výrazně víc peněz, než je běžné.</p>
                    </div>
                </a>
                <a href="{{ route('politicians.index') }}" class="group relative rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60 hover-lift card-shine overflow-hidden">
                    <div class="absolute top-0 right-0 h-24 w-24 bg-gradient-to-bl from-violet-100 to-transparent rounded-bl-3xl"></div>
                    <div class="relative">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-400 to-violet-600 text-white shadow-lg shadow-violet-500/25 mb-4">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-violet-600 transition-colors">Zastupitelé a firmy</h3>
                        <p class="mt-2 text-sm text-slate-500 leading-relaxed">Kteří zastupitelé jsou ve vedení firem, co obchodují s městem?</p>
                    </div>
                </a>
                <a href="{{ route('signals.index') }}#sekvence" class="group relative rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60 hover-lift card-shine overflow-hidden">
                    <div class="absolute top-0 right-0 h-24 w-24 bg-gradient-to-bl from-cyan-100 to-transparent rounded-bl-3xl"></div>
                    <div class="relative">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400 to-cyan-600 text-white shadow-lg shadow-cyan-500/25 mb-4">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-cyan-600 transition-colors">Časové návaznosti</h3>
                        <p class="mt-2 text-sm text-slate-500 leading-relaxed">Smlouvy a dotace, které na sebe podezřele časově navazují.</p>
                    </div>
                </a>
            </div>
        </section>

        {{-- RECENT DATA --}}
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <section class="reveal">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-extrabold text-slate-900">Poslední dokumenty</h2>
                    <a href="{{ route('documents.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">Zobrazit vše &rarr;</a>
                </div>
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 divide-y divide-slate-100">
                    @forelse($recentDocuments as $doc)
                        <a href="{{ route('documents.show', $doc) }}" class="group block px-5 py-4 hover:bg-slate-50/80 transition-colors first:rounded-t-2xl last:rounded-b-2xl">
                            <p class="text-sm font-semibold text-slate-900 truncate group-hover:text-indigo-600 transition-colors">{{ $doc->title }}</p>
                            <div class="mt-1.5 flex items-center gap-2">
                                <x-section-badge :section="$doc->section" />
                                @if($doc->published_date)
                                    <span class="text-xs text-slate-500">{{ $doc->published_date->format('j. n. Y') }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <x-empty-state title="Zatím žádné dokumenty" description="Data se importují z webu města Boskovice." />
                    @endforelse
                </div>
            </section>

            <section class="reveal">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-extrabold text-slate-900">Poslední smlouvy</h2>
                    <a href="{{ route('contracts.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">Zobrazit vše &rarr;</a>
                </div>
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 divide-y divide-slate-100">
                    @forelse($recentContracts as $contract)
                        <a href="{{ route('contracts.show', $contract) }}" class="group block px-5 py-4 hover:bg-slate-50/80 transition-colors first:rounded-t-2xl last:rounded-b-2xl">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate group-hover:text-indigo-600 transition-colors">{{ $contract->subject ?: 'Bez předmětu' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $contract->counterparty_name }}</p>
                                </div>
                                @if($contract->amount)
                                    <span class="shrink-0 inline-flex items-center rounded-xl bg-emerald-50 px-2.5 py-1 text-sm font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/10">
                                        {{ number_format($contract->amount, 0, ',', "\u{00a0}") }}&nbsp;{{ $contract->currency }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <x-empty-state title="Zatím žádné smlouvy" description="Data se importují z Registru smluv." />
                    @endforelse
                </div>
            </section>
        </div>

        {{-- TOP COUNTERPARTIES --}}
        @if($topCounterparties->isNotEmpty())
            <section class="reveal">
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-900">Největší dodavatelé města</h2>
                        <p class="mt-1 text-sm text-slate-500">Firmy seřazené dle objemu smluv. Vysoký objem nemusí znamenat problém &mdash; ale stojí za pozornost.</p>
                    </div>
                    <a href="{{ route('entities.index') }}?type=organization" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                        Všechny subjekty &rarr;
                    </a>
                </div>
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 overflow-hidden">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr class="bg-slate-50/80">
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Dodavatel</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Smluv</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Celková částka</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($topCounterparties as $index => $cp)
                                <tr class="group hover:bg-indigo-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-xs font-bold text-slate-600 group-hover:bg-indigo-100 group-hover:text-indigo-700 transition-colors">{{ $index + 1 }}</span>
                                            <span class="text-sm font-semibold text-slate-900">{{ $cp->counterparty_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 text-right tabular-nums font-medium">{{ $cp->contract_count }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-bold text-slate-900 tabular-nums">{{ number_format($cp->total_amount, 0, ',', "\u{00a0}") }}&nbsp;CZK</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        {{-- SAFETY MAP CTA --}}
        <section class="reveal">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700 px-6 py-12 text-white shadow-xl sm:px-12">
                <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -left-10 -bottom-16 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>
                <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                    <div>
                        <h2 class="text-2xl font-black tracking-tight sm:text-3xl">Kde se necítíte bezpečně?</h2>
                        <p class="mt-2 max-w-xl text-emerald-100 leading-relaxed">Označte místo na mapě a napište, co vás trápí. Pomůžete nám i městu identifikovat problematická místa.</p>
                    </div>
                    <a href="{{ route('safety-map.index') }}" class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-bold text-emerald-700 shadow-lg hover:bg-emerald-50 transition-all">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        Otevřít mapu
                    </a>
                </div>
            </div>
        </section>

        {{-- DATA SOURCES --}}
        <section class="reveal">
            <div class="rounded-3xl bg-slate-900 p-8 sm:p-12 text-white">
                <h2 class="text-2xl font-extrabold mb-2">Odkud data pocházejí</h2>
                <p class="text-sm text-slate-400 mb-8">Všechna data jsou z veřejných zdrojů. Platforma je nezávislá &mdash; nevytváří vlastní hodnocení.</p>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-5 text-center hover:bg-white/10 transition-colors">
                        <p class="text-sm font-bold text-white">Web města</p>
                        <p class="mt-1 text-xs text-slate-400">Úřední deska, zápisy, vyhlášky</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-5 text-center hover:bg-white/10 transition-colors">
                        <p class="text-sm font-bold text-white">Registr smluv</p>
                        <p class="mt-1 text-xs text-slate-400">Smlouvy přes Hlídač státu</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-5 text-center hover:bg-white/10 transition-colors">
                        <p class="text-sm font-bold text-white">ARES</p>
                        <p class="mt-1 text-xs text-slate-400">Firemní údaje, statutáři</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-5 text-center hover:bg-white/10 transition-colors">
                        <p class="text-sm font-bold text-white">CEDR / EU fondy</p>
                        <p class="mt-1 text-xs text-slate-400">Dotace z veřejných rozpočtů</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-5 text-center hover:bg-white/10 transition-colors">
                        <p class="text-sm font-bold text-white">Volby.cz</p>
                        <p class="mt-1 text-xs text-slate-400">Zastupitelé od 2014</p>
                    </div>
                </div>
            </div>
        </section>

    </div>

</x-layouts.app>
