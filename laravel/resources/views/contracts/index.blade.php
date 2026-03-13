<x-layouts.app title="Smlouvy">

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Smlouvy</h1>

            <form action="{{ route('contracts.index') }}" method="GET">
                <input
                    type="search"
                    name="q"
                    value="{{ $searchQuery }}"
                    placeholder="Hledat smlouvy..."
                    class="w-64 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                >
            </form>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Předmět</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Protistrana</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Částka</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Datum podpisu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contracts as $contract)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('contracts.show', $contract) }}" class="text-sm font-medium text-blue-600 hover:underline">
                                    {{ Str::limit($contract->subject ?: 'Bez předmětu', 80) }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $contract->counterparty_name }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right whitespace-nowrap">
                                @if($contract->amount)
                                    {{ number_format($contract->amount, 0, ',', "\u{00a0}") }} {{ $contract->currency }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 text-right whitespace-nowrap">
                                {{ $contract->date_signed?->format('j. n. Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-gray-500">Žádné smlouvy nenalezeny.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $contracts->links() }}
        </div>
    </div>

</x-layouts.app>
