<x-layouts.app :title="$document->title">

    <div class="space-y-6">
        <div>
            <a href="{{ route('documents.index') }}" class="text-sm text-blue-600 hover:text-blue-800">&larr; Zpět na dokumenty</a>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-6">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <x-section-badge :section="$document->section" />
                @if($document->published_date)
                    <span class="text-sm text-gray-500">{{ $document->published_date->format('j. n. Y') }}</span>
                @endif
                @if($document->department)
                    <span class="text-sm text-gray-400">{{ $document->department }}</span>
                @endif
            </div>

            <h1 class="text-2xl font-bold text-gray-900">{{ $document->title }}</h1>

            @if($document->source_url)
                <a href="{{ $document->source_url }}" target="_blank" rel="noopener" class="mt-2 inline-block text-sm text-blue-600 hover:underline">
                    Zdroj na webu města &nearr;
                </a>
            @endif
        </div>

        @if($document->attachments->isNotEmpty())
            <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Přílohy ({{ $document->attachments->count() }})</h2>
                <ul class="divide-y divide-gray-100">
                    @foreach($document->attachments as $attachment)
                        <li class="py-2 flex items-center justify-between">
                            <a href="{{ $attachment->url }}" target="_blank" rel="noopener" class="text-sm text-blue-600 hover:underline truncate">
                                {{ $attachment->filename ?: $attachment->url }}
                            </a>
                            @if($attachment->size_bytes)
                                <span class="shrink-0 text-xs text-gray-400 ml-3">
                                    {{ number_format($attachment->size_bytes / 1024, 0) }} KB
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if($linkedEntities->isNotEmpty())
            <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Propojené subjekty</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($linkedEntities as $entity)
                        <a href="{{ route('entities.show', $entity) }}" class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700 hover:bg-blue-100 transition-colors">
                            {{ $entity->name }}
                            @if($entity->ico)
                                <span class="ml-1 text-xs text-blue-500">({{ $entity->ico }})</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($document->fulltext)
            <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Fulltext</h2>
                <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-wrap">{{ Str::limit($document->fulltext, 5000) }}</div>
            </section>
        @endif
    </div>

</x-layouts.app>
