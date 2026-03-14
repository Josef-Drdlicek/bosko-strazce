<x-layouts.app title="Vyhledávání" metaDescription="Prohledejte všechna veřejná data města Boskovice — dokumenty, smlouvy, subjekty a dotace najednou.">

    <div class="space-y-8">
        <div class="text-center">
            <h1 class="text-4xl font-black text-slate-900">Vyhledávání</h1>
            <p class="mt-2 text-slate-500 max-w-lg mx-auto">Zadejte cokoliv — jméno firmy, číslo smlouvy, klíčové slovo — a prohledáme všechna data najednou.</p>
        </div>

        <form action="{{ route('search') }}" method="GET" class="max-w-2xl mx-auto">
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                <input
                    type="search"
                    name="q"
                    value="{{ $query }}"
                    placeholder="Název firmy, předmět smlouvy, klíčové slovo..."
                    autofocus
                    class="w-full rounded-xl border-0 bg-slate-100/80 pl-12 pr-5 py-4 text-lg placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:shadow-lg focus:shadow-indigo-500/10 transition-all"
                >
            </div>
        </form>

        @if(filled($query))
            @if($documents->isEmpty() && $contracts->isEmpty() && $entities->isEmpty() && $subsidies->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">Žádné výsledky pro &bdquo;{{ $query }}&ldquo;</h3>
                    <p class="mt-2 text-sm text-slate-500">Zkuste jiné klíčové slovo, název firmy nebo IČO. Můžete také zkusit <a href="{{ route('ares.index') }}" class="text-indigo-600 hover:text-indigo-800">vyhledat firmu v ARES</a>.</p>
                </div>
            @endif

            @if($entities->isNotEmpty())
                <section class="reveal">
                    <h2 class="flex items-center gap-2 text-xl font-extrabold text-slate-900 mb-4">
                        <svg class="h-5 w-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                        Subjekty
                        <span class="inline-flex items-center rounded-full bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700">{{ $entities->count() }}</span>
                    </h2>
                    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 divide-y divide-slate-100">
                        @foreach($entities as $entity)
                            <a href="{{ route('entities.show', $entity) }}" class="block px-5 py-4 hover:bg-slate-50/80 transition-colors first:rounded-t-2xl last:rounded-b-2xl">
                                <p class="text-sm font-semibold text-slate-900">{{ $entity->name }}</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-xs text-slate-500">{{ $entity->entity_type }}</span>
                                    @if($entity->ico)
                                        <span class="text-xs text-slate-400 font-mono">IČO {{ $entity->ico }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($documents->isNotEmpty())
                <section class="reveal">
                    <h2 class="flex items-center gap-2 text-xl font-extrabold text-slate-900 mb-4">
                        <svg class="h-5 w-5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                        Dokumenty
                        <span class="inline-flex items-center rounded-full bg-sky-50 px-2 py-0.5 text-xs font-medium text-sky-700">{{ $documents->count() }}</span>
                    </h2>
                    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 divide-y divide-slate-100">
                        @foreach($documents as $doc)
                            <a href="{{ route('documents.show', $doc) }}" class="block px-5 py-4 hover:bg-slate-50/80 transition-colors first:rounded-t-2xl last:rounded-b-2xl">
                                <p class="text-sm font-semibold text-slate-900">{{ $doc->title }}</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <x-section-badge :section="$doc->section" />
                                    @if($doc->published_date)
                                        <span class="text-xs text-slate-500">{{ $doc->published_date->format('j. n. Y') }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($contracts->isNotEmpty())
                <section class="reveal">
                    <h2 class="flex items-center gap-2 text-xl font-extrabold text-slate-900 mb-4">
                        <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/></svg>
                        Smlouvy
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">{{ $contracts->count() }}</span>
                    </h2>
                    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 divide-y divide-slate-100">
                        @foreach($contracts as $contract)
                            <a href="{{ route('contracts.show', $contract) }}" class="block px-5 py-4 hover:bg-slate-50/80 transition-colors first:rounded-t-2xl last:rounded-b-2xl">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $contract->subject ?: 'Bez předmětu' }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $contract->counterparty_name }}</p>
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

            @if($subsidies->isNotEmpty())
                <section class="reveal">
                    <h2 class="flex items-center gap-2 text-xl font-extrabold text-slate-900 mb-4">
                        <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9"/></svg>
                        Dotace
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">{{ $subsidies->count() }}</span>
                    </h2>
                    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 divide-y divide-slate-100">
                        @foreach($subsidies as $subsidy)
                            <a href="{{ route('subsidies.show', $subsidy) }}" class="block px-5 py-4 hover:bg-slate-50/80 transition-colors first:rounded-t-2xl last:rounded-b-2xl">
                                <p class="text-sm font-semibold text-slate-900">{{ $subsidy->title }}</p>
                                <div class="mt-1 flex items-center gap-3 text-xs text-slate-500">
                                    @if($subsidy->recipient_name)
                                        <span>{{ $subsidy->recipient_name }}</span>
                                    @endif
                                    @if($subsidy->amount)
                                        <span class="font-bold text-amber-700">{{ number_format($subsidy->amount, 0, ',', "\u{00a0}") }} CZK</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        @else
            <div class="max-w-2xl mx-auto">
                <div class="rounded-2xl bg-slate-50 ring-1 ring-inset ring-slate-200 p-6">
                    <h3 class="text-sm font-semibold text-slate-900">Co můžete hledat?</h3>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        <li class="flex items-center gap-2"><span class="inline-block h-1.5 w-1.5 rounded-full bg-violet-500"></span> Název firmy nebo organizace</li>
                        <li class="flex items-center gap-2"><span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Předmět smlouvy nebo klíčová slova</li>
                        <li class="flex items-center gap-2"><span class="inline-block h-1.5 w-1.5 rounded-full bg-sky-500"></span> Text v úředních dokumentech</li>
                        <li class="flex items-center gap-2"><span class="inline-block h-1.5 w-1.5 rounded-full bg-amber-500"></span> Název dotačního programu</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>

</x-layouts.app>
