<x-layouts.app :title="$person->name . ' — Zastupitel'">

    <div class="space-y-6">

        <a href="{{ route('politicians.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Zpět na zastupitele
        </a>

        {{-- Hero card --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 px-6 py-8 sm:px-8 sm:py-10">
                <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm text-white font-extrabold text-2xl shadow-lg">
                        {{ mb_substr($person->name, 0, 1) }}{{ mb_substr(explode(' ', $person->name)[1] ?? '', 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-white sm:text-3xl">{{ $person->name }}</h1>
                        <div class="mt-2 flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 backdrop-blur-sm px-3 py-1 text-sm font-medium text-white/90">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                                Zastupitel
                            </span>
                            @if($party)
                                <span class="inline-flex items-center rounded-full bg-white/15 backdrop-blur-sm px-3 py-1 text-sm font-medium text-white/90">
                                    {{ $party }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($total_contracts > 0 || $companies->isNotEmpty())
                <div class="grid grid-cols-2 gap-px bg-slate-100 sm:grid-cols-4">
                    <div class="bg-white p-5">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Firem ve vazbě</p>
                        <p class="mt-1 text-xl font-extrabold text-slate-900">{{ $companies->count() }}</p>
                    </div>
                    <div class="bg-white p-5">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Smluv přes firmy</p>
                        <p class="mt-1 text-xl font-extrabold {{ $total_contracts > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $total_contracts }}</p>
                    </div>
                    <div class="bg-white p-5">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Celkem přes vazby</p>
                        <p class="mt-1 text-xl font-extrabold {{ $total_amount > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ number_format($total_amount, 0, ',', "\u{00a0}") }}&nbsp;CZK</p>
                    </div>
                    <div class="bg-white p-5">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Volby</p>
                        <p class="mt-1 text-xl font-extrabold text-slate-900">{{ $elections->pluck('year')->implode(', ') }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Elections timeline --}}
        @if($elections->isNotEmpty())
            <section class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-6 sm:p-8">
                <h2 class="flex items-center gap-2 text-lg font-bold text-slate-900 mb-5">
                    <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                    Volební historie
                </h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach($elections as $election)
                        <div class="rounded-xl bg-indigo-50/50 p-4 ring-1 ring-inset ring-indigo-200/50">
                            <p class="text-2xl font-extrabold text-indigo-700">{{ $election->year }}</p>
                            @if($election->party)
                                <p class="mt-1 text-sm font-medium text-indigo-600">{{ $election->party }}</p>
                            @endif
                            @if($election->votes)
                                <p class="mt-1 text-xs text-indigo-500">{{ number_format($election->votes, 0, ',', "\u{00a0}") }} hlasů</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Company connections --}}
        @if($companies->isNotEmpty())
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 text-white shadow-lg shadow-violet-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Vazby na firmy</h2>
                        <p class="text-sm text-slate-500">Firmy, kde {{ $person->name }} zastává funkci ve statutárním či kontrolním orgánu</p>
                    </div>
                </div>

                @foreach($companies as $company)
                    <div class="rounded-2xl bg-white shadow-sm ring-1 {{ $company->contract_count > 0 ? 'ring-rose-200/80' : 'ring-slate-200/60' }} overflow-hidden">
                        <div class="p-5 sm:p-6">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2.5 flex-wrap">
                                        <a href="{{ route('entities.show', $company->id) }}" class="text-base font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                                            {{ $company->name }}
                                        </a>
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-600/20">
                                            {{ $company->role_label }}
                                        </span>
                                    </div>
                                    @if($company->ico)
                                        <p class="mt-1 text-xs text-slate-400 font-mono">IČO {{ $company->ico }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if($company->contract_count > 0)
                                        <span class="inline-flex items-center rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                            {{ $company->contract_count }} {{ $company->contract_count === 1 ? 'smlouva' : ($company->contract_count < 5 ? 'smlouvy' : 'smluv') }} &middot; {{ number_format($company->total_amount, 0, ',', "\u{00a0}") }}&nbsp;CZK
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                            Bez smluv s městem
                                        </span>
                                    @endif
                                    <a href="{{ route('entities.show', $company->id) }}" class="inline-flex items-center rounded-lg bg-slate-50 p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Detail firmy">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25"/></svg>
                                    </a>
                                    <a href="{{ route('graph.show', $company->id) }}" class="inline-flex items-center rounded-lg bg-slate-50 p-2 text-slate-400 hover:text-violet-600 hover:bg-violet-50 transition-colors" title="Graf vztahů">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                                    </a>
                                </div>
                            </div>

                            @if($company->top_contracts->isNotEmpty())
                                <div class="mt-4 space-y-1">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Největší smlouvy s městem</p>
                                    @foreach($company->top_contracts as $contract)
                                        <a href="{{ route('contracts.show', $contract->id) }}" class="flex items-center justify-between gap-3 rounded-lg px-3 py-2 hover:bg-slate-50 transition-colors -mx-1">
                                            <div class="min-w-0">
                                                <p class="text-sm text-slate-700 truncate">{{ $contract->subject ?: 'Bez předmětu' }}</p>
                                                @if($contract->date_signed)
                                                    <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($contract->date_signed)->format('j. n. Y') }}</p>
                                                @endif
                                            </div>
                                            @if($contract->amount)
                                                <span class="shrink-0 text-sm font-bold text-slate-900 tabular-nums">{{ number_format($contract->amount, 0, ',', "\u{00a0}") }}&nbsp;CZK</span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </section>
        @else
            <div class="rounded-2xl bg-emerald-50 ring-1 ring-inset ring-emerald-200 p-8 text-center">
                <svg class="mx-auto h-10 w-10 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p class="mt-3 text-sm font-medium text-emerald-700">Nebyly nalezeny žádné vazby na firmy v obchodním rejstříku.</p>
            </div>
        @endif

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('entities.show', $person) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-inset ring-slate-200 hover:bg-slate-100 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                Detail entity
            </a>
            <a href="{{ route('graph.show', $person) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-violet-50 px-4 py-2 text-sm font-medium text-violet-700 ring-1 ring-inset ring-violet-600/20 hover:bg-violet-100 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                Graf vztahů
            </a>
        </div>
    </div>

</x-layouts.app>
