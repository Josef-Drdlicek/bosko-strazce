<x-layouts.app :title="$entity->name">

    <div class="space-y-6">
        <a href="{{ route('entities.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Zpět na subjekty
        </a>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 text-white shadow-lg shadow-violet-200">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                        </div>
                        <div>
                            <span class="inline-flex items-center rounded-md bg-slate-50 px-2 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                {{ $entity->entity_type }}
                            </span>
                            @if($entity->source)
                                <span class="text-xs text-slate-400 ml-2">Zdroj: {{ $entity->source }}</span>
                            @endif
                        </div>
                    </div>

                    <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $entity->name }}</h1>

                    @if($entity->ico)
                        <p class="mt-1.5 text-sm text-slate-500">IČO: <span class="font-mono font-medium text-slate-700">{{ $entity->ico }}</span></p>
                    @endif
                </div>

                @if($entity->ico)
                    <a href="https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty/{{ $entity->ico }}" target="_blank" rel="noopener" class="shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 ring-1 ring-inset ring-indigo-600/20 hover:bg-indigo-100 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                        ARES
                    </a>
                @endif
            </div>
        </div>

        @if($entity->hasAresData())
            <section class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-6 sm:p-8">
                <h2 class="flex items-center gap-2 text-lg font-bold text-slate-900 mb-5">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                    Data z ARES
                </h2>
                @php $ares = $entity->metadata_json; @endphp
                <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @if(!empty($ares['address']))
                        <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-inset ring-slate-200">
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Sídlo</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $ares['address'] }}</dd>
                        </div>
                    @endif
                    @if(!empty($ares['legal_form']))
                        <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-inset ring-slate-200">
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Právní forma</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $ares['legal_form'] }}</dd>
                        </div>
                    @endif
                    @if(!empty($ares['date_created']))
                        <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-inset ring-slate-200">
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Datum vzniku</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $ares['date_created'] }}</dd>
                        </div>
                    @endif
                    @if(!empty($ares['date_terminated']))
                        <div class="rounded-xl bg-rose-50 p-4 ring-1 ring-inset ring-rose-200">
                            <dt class="text-xs font-medium text-rose-500 uppercase tracking-wider">Datum zániku</dt>
                            <dd class="mt-1 text-sm font-bold text-rose-900">{{ $ares['date_terminated'] }}</dd>
                        </div>
                    @endif
                    @if(!empty($ares['financial_office']))
                        <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-inset ring-slate-200">
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Finanční úřad</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $ares['financial_office'] }}</dd>
                        </div>
                    @endif
                    @if(!empty($ares['cz_nace']))
                        <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-inset ring-slate-200 sm:col-span-2 lg:col-span-1">
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">CZ-NACE</dt>
                            <dd class="mt-1 flex flex-wrap gap-1">
                                @foreach(array_slice($ares['cz_nace'], 0, 5) as $nace)
                                    <span class="inline-flex items-center rounded bg-white px-1.5 py-0.5 text-xs font-medium text-slate-700 ring-1 ring-slate-300">{{ $nace }}</span>
                                @endforeach
                            </dd>
                        </div>
                    @endif
                </dl>
            </section>
        @endif

        @if($contracts->isNotEmpty())
            <section class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-6 sm:p-8">
                <h2 class="flex items-center gap-2 text-lg font-bold text-slate-900 mb-1">
                    <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/></svg>
                    Smlouvy
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">{{ $contracts->count() }}</span>
                </h2>
                @if($contracts->sum('amount') > 0)
                    <p class="text-sm text-slate-500 mb-4">Celkem: <span class="font-bold text-slate-900">{{ number_format($contracts->sum('amount'), 0, ',', "\u{00a0}") }} CZK</span></p>
                @endif
                <div class="divide-y divide-slate-100">
                    @foreach($contracts as $contract)
                        <a href="{{ route('contracts.show', $contract) }}" class="block py-3 hover:bg-slate-50/80 transition-colors -mx-2 px-2 rounded-lg">
                            <div class="flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $contract->subject ?: 'Bez předmětu' }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $contract->date_signed?->format('j. n. Y') }}</p>
                                </div>
                                @if($contract->amount)
                                    <span class="shrink-0 inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-sm font-bold text-emerald-700">
                                        {{ number_format($contract->amount, 0, ',', "\u{00a0}") }}&nbsp;CZK
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($documents->isNotEmpty())
            <section class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-6 sm:p-8">
                <h2 class="flex items-center gap-2 text-lg font-bold text-slate-900 mb-4">
                    <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                    Dokumenty
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">{{ $documents->count() }}</span>
                </h2>
                <div class="divide-y divide-slate-100">
                    @foreach($documents as $doc)
                        <a href="{{ route('documents.show', $doc) }}" class="block py-3 hover:bg-slate-50/80 transition-colors -mx-2 px-2 rounded-lg">
                            <p class="text-sm font-semibold text-slate-900">{{ $doc->title }}</p>
                            <div class="mt-1 flex items-center gap-2">
                                <x-section-badge :section="$doc->section" />
                                <span class="text-xs text-slate-500">{{ $doc->published_date?->format('j. n. Y') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($subsidies->isNotEmpty())
            <section class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-6 sm:p-8">
                <h2 class="flex items-center gap-2 text-lg font-bold text-slate-900 mb-4">
                    <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
                    Dotace
                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">{{ $subsidies->count() }}</span>
                </h2>
                <div class="divide-y divide-slate-100">
                    @foreach($subsidies as $subsidy)
                        <a href="{{ route('subsidies.show', $subsidy) }}" class="block py-3 hover:bg-slate-50/80 transition-colors -mx-2 px-2 rounded-lg">
                            <p class="text-sm font-semibold text-slate-900">{{ $subsidy->title }}</p>
                            <div class="mt-1 flex items-center gap-3 text-xs text-slate-500">
                                @if($subsidy->program)
                                    <span>{{ $subsidy->program }}</span>
                                @endif
                                @if($subsidy->amount)
                                    <span class="font-bold text-amber-700">{{ number_format($subsidy->amount, 0, ',', "\u{00a0}") }} CZK</span>
                                @endif
                                @if($subsidy->year)
                                    <span>{{ $subsidy->year }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

</x-layouts.app>
