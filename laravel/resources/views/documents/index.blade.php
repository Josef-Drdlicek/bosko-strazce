<x-layouts.app title="Dokumenty">

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Dokumenty</h1>

            <form action="{{ route('documents.index') }}" method="GET" class="flex items-center gap-3">
                <select name="section" onchange="this.form.submit()" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Všechny sekce</option>
                    @foreach($sections as $section)
                        <option value="{{ $section }}" @selected($currentSection === $section)>{{ $section }}</option>
                    @endforeach
                </select>

                <input
                    type="search"
                    name="q"
                    value="{{ $searchQuery }}"
                    placeholder="Hledat v dokumentech..."
                    class="w-64 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                >
            </form>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 divide-y divide-gray-100">
            @forelse($documents as $doc)
                <a href="{{ route('documents.show', $doc) }}" class="block px-4 py-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900">{{ $doc->title }}</p>
                            <div class="mt-1.5 flex flex-wrap items-center gap-2">
                                <x-section-badge :section="$doc->section" />
                                @if($doc->published_date)
                                    <span class="text-xs text-gray-500">{{ $doc->published_date->format('j. n. Y') }}</span>
                                @endif
                                @if($doc->department)
                                    <span class="text-xs text-gray-400">&middot; {{ $doc->department }}</span>
                                @endif
                                @if($doc->attachments_count ?? $doc->attachments->count())
                                    <span class="text-xs text-gray-400">&middot; {{ $doc->attachments->count() }} příloh</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <p class="px-4 py-12 text-center text-gray-500">Žádné dokumenty nenalezeny.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $documents->links() }}
        </div>
    </div>

</x-layouts.app>
