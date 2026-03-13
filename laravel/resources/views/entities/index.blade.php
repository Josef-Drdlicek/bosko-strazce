<x-layouts.app title="Subjekty">

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Subjekty</h1>

            <form action="{{ route('entities.index') }}" method="GET" class="flex items-center gap-3">
                <select name="type" onchange="this.form.submit()" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Všechny typy</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" @selected($currentType === $type)>{{ $type }}</option>
                    @endforeach
                </select>

                <input
                    type="search"
                    name="q"
                    value="{{ $searchQuery }}"
                    placeholder="Hledat subjekty..."
                    class="w-64 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                >
            </form>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 divide-y divide-gray-100">
            @forelse($entities as $entity)
                <a href="{{ route('entities.show', $entity) }}" class="block px-4 py-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ $entity->name }}</p>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                                    {{ $entity->entity_type }}
                                </span>
                                @if($entity->ico)
                                    <span class="text-xs text-gray-500">IČO: {{ $entity->ico }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <p class="px-4 py-12 text-center text-gray-500">Žádné subjekty nenalezeny.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $entities->links() }}
        </div>
    </div>

</x-layouts.app>
