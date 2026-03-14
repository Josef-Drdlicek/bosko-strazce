<x-layouts.app title="Zastupitelé" metaDescription="Zastupitelé města Boskovice — komunální volby 2014, 2018, 2022 a jejich vazby na firmy obchodující s městem.">

    <div class="space-y-8">

        <x-breadcrumb :items="[['label' => 'Zastupitelé']]" />

        <x-page-header
            title="Zastupitelé města Boskovice"
            description="Kdo rozhoduje o městě? Zastupitelé zvolení v komunálních volbách a jejich případné vazby na firmy, které s městem obchodují. Vazba na firmu neznamená problém — ale veřejnost by o ní měla vědět."
            badge="Zdroj: Volby.cz &middot; ARES &middot; Registr smluv"
        />

        <div class="reveal grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="hover-lift rounded-xl bg-white p-4 ring-1 ring-slate-200/60 flex items-start gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Kteří zastupitelé jsou ve vedení firem</p>
                    <p class="text-xs text-slate-500">Statutární orgány firem z obchodního rejstříku (ARES).</p>
                </div>
            </div>
            <div class="hover-lift rounded-xl bg-white p-4 ring-1 ring-slate-200/60 flex items-start gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Které firmy zastupitelů mají zakázky</p>
                    <p class="text-xs text-slate-500">Křížová kontrola se smlouvami z Registru smluv.</p>
                </div>
            </div>
            <div class="hover-lift rounded-xl bg-white p-4 ring-1 ring-slate-200/60 flex items-start gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Kolik peněz proudí přes vazby</p>
                    <p class="text-xs text-slate-500">Celkový objem smluv firem propojených se zastupiteli.</p>
                </div>
            </div>
        </div>

        <div class="reveal grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-5">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Zastupitelů celkem</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $totalPoliticians }}</p>
                <p class="mt-1 text-xs text-slate-400">Volby 2014, 2018, 2022</p>
            </div>
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-5">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">S vazbou na firmu</p>
                <p class="mt-2 text-2xl font-extrabold text-violet-600">{{ $withConflicts }}</p>
                <p class="mt-1 text-xs text-slate-400">Zastupitelé ve vedení firem</p>
            </div>
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-5">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Politických stran</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $uniqueParties->count() }}</p>
                <p class="mt-1 text-xs text-slate-400">Zastoupených ve volbách</p>
            </div>
        </div>

        <div x-data="{ filter: 'all' }" class="space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
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
                <a href="{{ route('signals.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-rose-600 hover:text-rose-800 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                    Signály střetů zájmů
                </a>
            </div>

            <div class="reveal grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach($politicians as $politician)
                    <a
                        href="{{ route('politicians.show', $politician->id) }}"
                        x-show="filter === 'all' || (filter === 'conflict' && {{ $politician->has_conflicts ? 'true' : 'false' }}) || (filter === 'clean' && !{{ $politician->has_conflicts ? 'true' : 'false' }})"
                        x-transition
                        class="group hover-lift block rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 hover:shadow-md hover:ring-slate-300/60 transition-all overflow-hidden"
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

        <x-info-box variant="warning" title="O těchto datech">
            Zastupitelé jsou importováni z <strong>volby.cz</strong> (výsledky komunálních voleb 2014, 2018, 2022).
            Vazby na firmy pochází z <strong>ARES</strong> veřejného rejstříku (statutární orgány).
            Smlouvy jsou z <strong>Registru smluv</strong> přes Hlídač státu.
            Vazba na firmu neznamená střet zájmů — slouží pouze jako podnět k ověření z veřejně dostupných dat.
        </x-info-box>

    </div>

</x-layouts.app>
