<x-layouts.app title="Dashboard">

    <div class="space-y-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Přehled veřejných dat města Boskovice</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card label="Dokumenty" :value="number_format($stats['documents'])" :href="route('documents.index')" />
            <x-stat-card label="Smlouvy" :value="number_format($stats['contracts'])" :href="route('contracts.index')" />
            <x-stat-card label="Subjekty" :value="number_format($stats['entities'])" :href="route('entities.index')" />
            <x-stat-card label="Dotace" :value="number_format($stats['subsidies'])" />
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <section>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Poslední dokumenty</h2>
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 divide-y divide-gray-100">
                    @forelse($recentDocuments as $doc)
                        <a href="{{ route('documents.show', $doc) }}" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $doc->title }}</p>
                                    <div class="mt-1 flex items-center gap-2">
                                        <x-section-badge :section="$doc->section" />
                                        @if($doc->published_date)
                                            <span class="text-xs text-gray-500">{{ $doc->published_date->format('j. n. Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <p class="px-4 py-8 text-center text-sm text-gray-500">Žádné dokumenty</p>
                    @endforelse
                </div>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Poslední smlouvy</h2>
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 divide-y divide-gray-100">
                    @forelse($recentContracts as $contract)
                        <a href="{{ route('contracts.show', $contract) }}" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $contract->subject ?: 'Bez předmětu' }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500">{{ $contract->counterparty_name }}</p>
                                </div>
                                @if($contract->amount)
                                    <span class="shrink-0 text-sm font-semibold text-gray-900">
                                        {{ number_format($contract->amount, 0, ',', "\u{00a0}") }} {{ $contract->currency }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <p class="px-4 py-8 text-center text-sm text-gray-500">Žádné smlouvy</p>
                    @endforelse
                </div>
            </section>
        </div>

        @if($topCounterparties->isNotEmpty())
            <section>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Top dodavatelé</h2>
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dodavatel</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Smluv</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Celková částka</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($topCounterparties as $cp)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $cp->counterparty_name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 text-right">{{ $cp->contract_count }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right">
                                        {{ number_format($cp->total_amount, 0, ',', "\u{00a0}") }} CZK
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </div>

</x-layouts.app>
