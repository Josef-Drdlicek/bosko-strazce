<x-layouts.app :title="$entity->name">

    <div class="space-y-6">
        <div>
            <a href="{{ route('entities.index') }}" class="text-sm text-blue-600 hover:text-blue-800">&larr; Zpět na subjekty</a>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                    {{ $entity->entity_type }}
                </span>
                @if($entity->source)
                    <span class="text-xs text-gray-400">Zdroj: {{ $entity->source }}</span>
                @endif
            </div>

            <h1 class="text-2xl font-bold text-gray-900">{{ $entity->name }}</h1>

            @if($entity->ico)
                <p class="mt-1 text-sm text-gray-500">IČO: {{ $entity->ico }}</p>
            @endif

            @if($entity->metadata_json)
                <details class="mt-4">
                    <summary class="cursor-pointer text-sm text-blue-600 hover:underline">Metadata z ARES</summary>
                    <pre class="mt-2 rounded-lg bg-gray-50 p-4 text-xs text-gray-700 overflow-x-auto">{{ json_encode($entity->metadata_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </details>
            @endif
        </div>

        @if($contracts->isNotEmpty())
            <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">
                    Smlouvy ({{ $contracts->count() }})
                    @if($contracts->sum('amount') > 0)
                        <span class="text-base font-normal text-gray-500">
                            &mdash; celkem {{ number_format($contracts->sum('amount'), 0, ',', "\u{00a0}") }} CZK
                        </span>
                    @endif
                </h2>
                <div class="divide-y divide-gray-100">
                    @foreach($contracts as $contract)
                        <a href="{{ route('contracts.show', $contract) }}" class="block py-2 hover:bg-gray-50 transition-colors -mx-2 px-2 rounded">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $contract->subject ?: 'Bez předmětu' }}</p>
                                    <p class="text-xs text-gray-500">{{ $contract->date_signed?->format('j. n. Y') }}</p>
                                </div>
                                @if($contract->amount)
                                    <span class="shrink-0 text-sm font-semibold text-gray-900">
                                        {{ number_format($contract->amount, 0, ',', "\u{00a0}") }} CZK
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($documents->isNotEmpty())
            <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Dokumenty ({{ $documents->count() }})</h2>
                <div class="divide-y divide-gray-100">
                    @foreach($documents as $doc)
                        <a href="{{ route('documents.show', $doc) }}" class="block py-2 hover:bg-gray-50 transition-colors -mx-2 px-2 rounded">
                            <p class="text-sm font-medium text-gray-900">{{ $doc->title }}</p>
                            <div class="mt-0.5 flex items-center gap-2">
                                <x-section-badge :section="$doc->section" />
                                <span class="text-xs text-gray-500">{{ $doc->published_date?->format('j. n. Y') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($subsidies->isNotEmpty())
            <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Dotace ({{ $subsidies->count() }})</h2>
                <div class="divide-y divide-gray-100">
                    @foreach($subsidies as $subsidy)
                        <div class="py-2">
                            <p class="text-sm font-medium text-gray-900">{{ $subsidy->title }}</p>
                            <div class="mt-0.5 flex items-center gap-3 text-xs text-gray-500">
                                @if($subsidy->program)
                                    <span>{{ $subsidy->program }}</span>
                                @endif
                                @if($subsidy->amount)
                                    <span class="font-medium text-gray-700">{{ number_format($subsidy->amount, 0, ',', "\u{00a0}") }} CZK</span>
                                @endif
                                @if($subsidy->year)
                                    <span>{{ $subsidy->year }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

</x-layouts.app>
