<x-layouts.app :title="$contract->subject ?: 'Detail smlouvy'">

    <div class="space-y-6">

        <x-breadcrumb :items="[['label' => 'Smlouvy', 'href' => route('contracts.index')], ['label' => Str::limit($contract->subject ?: 'Detail smlouvy', 50)]]" />

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-6 sm:p-8">
            <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $contract->subject ?: 'Bez předmětu' }}</h1>

            <dl class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @if($contract->amount)
                    <div class="rounded-xl bg-emerald-50 p-5 ring-1 ring-inset ring-emerald-600/10">
                        <dt class="text-sm font-medium text-emerald-700">Částka</dt>
                        <dd class="mt-1 text-2xl font-extrabold text-emerald-900">
                            {{ number_format($contract->amount, 0, ',', "\u{00a0}") }}&nbsp;{{ $contract->currency }}
                        </dd>
                    </div>
                @endif

                @if($contract->date_signed)
                    <div class="rounded-xl bg-slate-50 p-5 ring-1 ring-inset ring-slate-200">
                        <dt class="text-sm font-medium text-slate-500">Datum podpisu</dt>
                        <dd class="mt-1 text-lg font-bold text-slate-900">{{ $contract->date_signed->format('j. n. Y') }}</dd>
                    </div>
                @endif

                @if($contract->publisher_name)
                    <div class="rounded-xl bg-slate-50 p-5 ring-1 ring-inset ring-slate-200">
                        <dt class="text-sm font-medium text-slate-500">Objednatel</dt>
                        <dd class="mt-1 text-lg font-bold text-slate-900">
                            {{ $contract->publisher_name }}
                            @if($contract->publisher_ico)
                                <span class="block text-sm font-normal text-slate-400 mt-0.5">IČO: {{ $contract->publisher_ico }}</span>
                            @endif
                        </dd>
                    </div>
                @endif

                @if($contract->counterparty_name)
                    <div class="rounded-xl bg-slate-50 p-5 ring-1 ring-inset ring-slate-200">
                        <dt class="text-sm font-medium text-slate-500">Dodavatel</dt>
                        <dd class="mt-1 text-lg font-bold text-slate-900">
                            {{ $contract->counterparty_name }}
                            @if($contract->counterparty_ico)
                                <span class="block text-sm font-normal text-slate-400 mt-0.5">IČO: {{ $contract->counterparty_ico }}</span>
                            @endif
                        </dd>
                    </div>
                @endif
            </dl>

            @if($contract->source_url)
                <a href="{{ $contract->source_url }}" target="_blank" rel="noopener" class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                    Zobrazit na Hlídači státu
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                </a>
            @endif
        </div>

        @if($linkedEntities->isNotEmpty())
            <section class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-6 sm:p-8">
                <h2 class="flex items-center gap-2 text-lg font-bold text-slate-900 mb-4">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/></svg>
                    Propojené subjekty
                </h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($linkedEntities as $entity)
                        <a href="{{ route('entities.show', $entity) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-sm font-medium text-indigo-700 ring-1 ring-inset ring-indigo-600/20 hover:bg-indigo-100 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                            {{ $entity->name }}
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($contract->fulltext)
            <section class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-6 sm:p-8">
                <h2 class="flex items-center gap-2 text-lg font-bold text-slate-900 mb-4">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>
                    Fulltext
                </h2>
                <div class="prose prose-slate prose-sm max-w-none whitespace-pre-wrap leading-relaxed">{{ Str::limit($contract->fulltext, 5000) }}</div>
            </section>
        @endif

        <x-info-box variant="neutral">
            Smlouva pochází z celostátního <strong>Registru smluv</strong> (přes Hlídač státu). Částka a další údaje jsou přebírány automaticky — pro ověření klikněte na odkaz „Zobrazit na Hlídači státu" výše.
        </x-info-box>
    </div>

</x-layouts.app>
