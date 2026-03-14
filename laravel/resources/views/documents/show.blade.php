<x-layouts.app :title="$document->title">

    <div class="space-y-6">
        <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Zpět na dokumenty
        </a>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <x-section-badge :section="$document->section" />
                @if($document->published_date)
                    <span class="inline-flex items-center gap-1 text-sm text-slate-500">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                        {{ $document->published_date->format('j. n. Y') }}
                    </span>
                @endif
                @if($document->department)
                    <span class="text-sm text-slate-400">{{ $document->department }}</span>
                @endif
            </div>

            <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $document->title }}</h1>

            @if($document->source_url)
                <a href="{{ $document->source_url }}" target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                    Zdroj na webu města
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                </a>
            @endif
        </div>

        @if($document->attachments->isNotEmpty())
            <section class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-6 sm:p-8">
                <h2 class="flex items-center gap-2 text-lg font-bold text-slate-900 mb-4">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                    Přílohy
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ $document->attachments->count() }}</span>
                </h2>
                <ul class="divide-y divide-slate-100">
                    @foreach($document->attachments as $attachment)
                        <li class="py-3 flex items-center justify-between gap-3">
                            <a href="{{ $attachment->url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors truncate">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                {{ $attachment->filename ?: $attachment->url }}
                            </a>
                            @if($attachment->size_bytes)
                                <span class="shrink-0 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">
                                    {{ number_format($attachment->size_bytes / 1024, 0) }} KB
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

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
                            @if($entity->ico)
                                <span class="text-xs text-indigo-500">({{ $entity->ico }})</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($document->fulltext)
            <section class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-6 sm:p-8">
                <h2 class="flex items-center gap-2 text-lg font-bold text-slate-900 mb-4">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>
                    Fulltext
                </h2>
                <div class="prose prose-slate prose-sm max-w-none whitespace-pre-wrap leading-relaxed">{{ Str::limit($document->fulltext, 5000) }}</div>
            </section>
        @endif
    </div>

</x-layouts.app>
