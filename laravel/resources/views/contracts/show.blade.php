<x-layouts.app :title="$contract->subject ?: 'Detail smlouvy'">

    <div class="space-y-6">
        <div>
            <a href="{{ route('contracts.index') }}" class="text-sm text-blue-600 hover:text-blue-800">&larr; Zpět na smlouvy</a>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $contract->subject ?: 'Bez předmětu' }}</h1>

            <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @if($contract->amount)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Částka</dt>
                        <dd class="mt-1 text-lg font-bold text-gray-900">
                            {{ number_format($contract->amount, 0, ',', "\u{00a0}") }} {{ $contract->currency }}
                        </dd>
                    </div>
                @endif

                @if($contract->date_signed)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Datum podpisu</dt>
                        <dd class="mt-1 text-gray-900">{{ $contract->date_signed->format('j. n. Y') }}</dd>
                    </div>
                @endif

                @if($contract->publisher_name)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Objednatel</dt>
                        <dd class="mt-1 text-gray-900">
                            {{ $contract->publisher_name }}
                            @if($contract->publisher_ico)
                                <span class="text-xs text-gray-400">(IČO: {{ $contract->publisher_ico }})</span>
                            @endif
                        </dd>
                    </div>
                @endif

                @if($contract->counterparty_name)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dodavatel</dt>
                        <dd class="mt-1 text-gray-900">
                            {{ $contract->counterparty_name }}
                            @if($contract->counterparty_ico)
                                <span class="text-xs text-gray-400">(IČO: {{ $contract->counterparty_ico }})</span>
                            @endif
                        </dd>
                    </div>
                @endif
            </dl>

            @if($contract->source_url)
                <a href="{{ $contract->source_url }}" target="_blank" rel="noopener" class="mt-4 inline-block text-sm text-blue-600 hover:underline">
                    Zobrazit na Hlídači státu &nearr;
                </a>
            @endif
        </div>

        @if($linkedEntities->isNotEmpty())
            <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Propojené subjekty</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($linkedEntities as $entity)
                        <a href="{{ route('entities.show', $entity) }}" class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700 hover:bg-blue-100 transition-colors">
                            {{ $entity->name }}
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($contract->fulltext)
            <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Fulltext</h2>
                <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-wrap">{{ Str::limit($contract->fulltext, 5000) }}</div>
            </section>
        @endif
    </div>

</x-layouts.app>
