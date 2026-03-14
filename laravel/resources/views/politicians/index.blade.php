<x-layouts.app title="Zastupitelé">

    <div class="space-y-8">

        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Zastupitelé města Boskovice</h1>
            <p class="mt-1 text-sm text-slate-500">Přehled všech zvolených zastupitelů a jejich vazeb na firmy se zakázkami města.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-5">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Zastupitelů celkem</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $totalPoliticians }}</p>
            </div>
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-5">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">S vazbou na firmu</p>
                <p class="mt-2 text-2xl font-extrabold text-violet-600">{{ $withConflicts }}</p>
            </div>
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-5">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Politických stran</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $uniqueParties->count() }}</p>
            </div>
        </div>

        <div x-data="{ filter: 'all' }" class="space-y-5">
            <div class="flex flex-wrap gap-2">
                <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50'" class="rounded-full px-4 py-1.5 text-sm font-medium transition-all">
                    Všichni
                </button>
                <button @click="filter = 'conflict'" :class="filter === 'conflict' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50'" class="rounded-full px-4 py-1.5 text-sm font-medium transition-all">
                    S vazbou na firmu
                </button>
                <button @click="filter = 'clean'" :class="filter === 'clean' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50'" class="rounded-full px-4 py-1.5 text-sm font-medium transition-all">
                    Bez vazby
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach($politicians as $politician)
                    <a
                        href="{{ route('politicians.show', $politician->id) }}"
                        x-show="filter === 'all' || (filter === 'conflict' && {{ $politician->has_conflicts ? 'true' : 'false' }}) || (filter === 'clean' && !{{ $politician->has_conflicts ? 'true' : 'false' }})"
                        x-transition
                        class="group block rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 hover:shadow-md hover:ring-slate-300/60 transition-all overflow-hidden"
                    >
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $politician->has_conflicts ? 'bg-gradient-to-br from-violet-500 to-rose-500' : 'bg-gradient-to-br from-slate-400 to-slate-500' }} text-white font-bold text-sm shadow-sm">
                                        {{ mb_substr($politician->name, 0, 1) }}{{ mb_substr(explode(' ', $politician->name)[1] ?? '', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors truncate">
                                            {{ $politician->name }}
                                        </h3>
                                        @if($politician->party)
                                            <p class="text-xs text-slate-500 truncate">{{ $politician->party }}</p>
                                        @endif
                                    </div>
                                </div>

                                @if($politician->has_conflicts)
                                    <span class="shrink-0 inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                        {{ $politician->company_count }} {{ $politician->company_count === 1 ? 'firma' : ($politician->company_count < 5 ? 'firmy' : 'firem') }}
                                    </span>
                                @endif
                            </div>

                            @if($politician->election_year)
                                <div class="mt-3 flex items-center gap-2 text-xs text-slate-400">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                                    Volby {{ $politician->election_year }}
                                    @if($politician->votes)
                                        <span class="text-slate-300">&middot;</span>
                                        {{ number_format($politician->votes, 0, ',', "\u{00a0}") }} hlasů
                                    @endif
                                </div>
                            @endif

                            @if($politician->companies->isNotEmpty())
                                <div class="mt-4 space-y-2">
                                    @foreach($politician->companies->take(3) as $company)
                                        <div class="flex items-center justify-between gap-2 rounded-lg {{ $company->contract_count > 0 ? 'bg-rose-50/60' : 'bg-slate-50' }} px-3 py-2">
                                            <div class="min-w-0">
                                                <p class="text-xs font-medium text-slate-700 truncate">{{ $company->name }}</p>
                                                <p class="text-[10px] text-slate-400">{{ $company->role_label }}</p>
                                            </div>
                                            @if($company->contract_count > 0)
                                                <div class="text-right shrink-0">
                                                    <p class="text-xs font-bold text-rose-700 tabular-nums">{{ number_format($company->total_amount, 0, ',', "\u{00a0}") }}&nbsp;CZK</p>
                                                    <p class="text-[10px] text-rose-500">{{ $company->contract_count }} {{ $company->contract_count === 1 ? 'smlouva' : ($company->contract_count < 5 ? 'smlouvy' : 'smluv') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($politician->companies->count() > 3)
                                        <p class="text-xs text-slate-400 text-center">+ {{ $politician->companies->count() - 3 }} {{ ($politician->companies->count() - 3) === 1 ? 'další' : 'dalších' }}</p>
                                    @endif
                                </div>

                                @if($politician->total_amount > 0)
                                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
                                        <span class="text-xs text-slate-400">Celkem přes vazby</span>
                                        <span class="text-sm font-extrabold text-slate-900 tabular-nums">{{ number_format($politician->total_amount, 0, ',', "\u{00a0}") }}&nbsp;CZK</span>
                                    </div>
                                @endif
                            @else
                                <div class="mt-4 rounded-lg bg-emerald-50/50 px-3 py-2.5 text-center">
                                    <p class="text-xs text-emerald-600 font-medium">Bez detekovaných vazeb na firmy</p>
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl bg-amber-50 ring-1 ring-inset ring-amber-200 p-6">
            <div class="flex gap-3">
                <svg class="h-5 w-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
                <div>
                    <h3 class="text-sm font-bold text-amber-800">O těchto datech</h3>
                    <p class="mt-1 text-sm text-amber-700">
                        Zastupitelé jsou importováni z <strong>volby.cz</strong> (výsledky komunálních voleb 2014, 2018, 2022).
                        Vazby na firmy pochází z <strong>ARES</strong> veřejného rejstříku (statutární orgány).
                        Smlouvy jsou z <strong>Registru smluv</strong> přes Hlídač státu.
                        Vazba na firmu neznamená střet zájmů — slouží pouze jako podnět k ověření.
                    </p>
                </div>
            </div>
        </div>

    </div>

</x-layouts.app>
