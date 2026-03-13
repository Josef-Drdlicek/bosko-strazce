<x-layouts.app title="Vyhledávání">

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Vyhledávání</h1>
        </div>

        <form action="{{ route('search') }}" method="GET">
            <input
                type="search"
                name="q"
                value="{{ $query }}"
                placeholder="Zadejte hledaný výraz..."
                autofocus
                class="w-full rounded-xl border border-gray-300 bg-white px-5 py-3 text-lg placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </form>

        @if(filled($query))
            @if($documents->isEmpty() && $contracts->isEmpty() && $entities->isEmpty() && $subsidies->isEmpty())
                <p class="text-center text-gray-500 py-8">Žádné výsledky pro &bdquo;{{ $query }}&ldquo;</p>
            @endif

            @if($documents->isNotEmpty())
                <section>
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Dokumenty ({{ $documents->count() }})</h2>
                    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 divide-y divide-gray-100">
                        @foreach($documents as $doc)
                            <a href="{{ route('documents.show', $doc) }}" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                <p class="text-sm font-medium text-gray-900">{{ $doc->title }}</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <x-section-badge :section="$doc->section" />
                                    @if($doc->published_date)
                                        <span class="text-xs text-gray-500">{{ $doc->published_date->format('j. n. Y') }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($contracts->isNotEmpty())
                <section>
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Smlouvy ({{ $contracts->count() }})</h2>
                    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 divide-y divide-gray-100">
                        @foreach($contracts as $contract)
                            <a href="{{ route('contracts.show', $contract) }}" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $contract->subject ?: 'Bez předmětu' }}</p>
                                        <p class="text-xs text-gray-500">{{ $contract->counterparty_name }}</p>
                                    </div>
                                    @if($contract->amount)
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ number_format($contract->amount, 0, ',', "\u{00a0}") }} CZK
                                        </span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($entities->isNotEmpty())
                <section>
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Subjekty ({{ $entities->count() }})</h2>
                    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 divide-y divide-gray-100">
                        @foreach($entities as $entity)
                            <a href="{{ route('entities.show', $entity) }}" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                <p class="text-sm font-medium text-gray-900">{{ $entity->name }}</p>
                                <div class="mt-0.5 flex items-center gap-2">
                                    <span class="text-xs text-gray-500">{{ $entity->entity_type }}</span>
                                    @if($entity->ico)
                                        <span class="text-xs text-gray-400">IČO: {{ $entity->ico }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($subsidies->isNotEmpty())
                <section>
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Dotace ({{ $subsidies->count() }})</h2>
                    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 divide-y divide-gray-100">
                        @foreach($subsidies as $subsidy)
                            <div class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ $subsidy->title }}</p>
                                <div class="mt-0.5 flex items-center gap-3 text-xs text-gray-500">
                                    @if($subsidy->recipient_name)
                                        <span>{{ $subsidy->recipient_name }}</span>
                                    @endif
                                    @if($subsidy->amount)
                                        <span class="font-medium">{{ number_format($subsidy->amount, 0, ',', "\u{00a0}") }} CZK</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @endif
    </div>

</x-layouts.app>
