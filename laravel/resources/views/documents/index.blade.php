<x-layouts.app title="Dokumenty" metaDescription="Úřední dokumenty města Boskovice — úřední deska, zápisy zastupitelstva a rady, vyhlášky, rozpočty a poskytnuté informace.">

    <div class="space-y-6">

        <x-breadcrumb :items="[['label' => 'Dokumenty']]" />

        <x-page-header
            title="Dokumenty města Boskovice"
            description="Všechny úřední dokumenty, které město zveřejňuje — od zápisů ze zastupitelstva po vyhlášky a rozpočty. Můžete v nich hledat fulltextem."
            badge="Zdroj: web města Boskovice"
        />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 reveal">
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200/60 flex items-start gap-3 hover-lift">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Fulltextové vyhledávání</p>
                    <p class="text-xs text-slate-500">Hledejte klíčová slova v textech dokumentů i příloh.</p>
                </div>
            </div>
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200/60 flex items-start gap-3 hover-lift">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">7 typů dokumentů</p>
                    <p class="text-xs text-slate-500">Úřední deska, zápisy ZM/RM, vyhlášky, rozpočty a další.</p>
                </div>
            </div>
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200/60 flex items-start gap-3 hover-lift">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">PDF přílohy staženy</p>
                    <p class="text-xs text-slate-500">Text extrahován z příloh pro snadné prohledávání.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <form action="{{ route('documents.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <select name="section" onchange="this.form.submit()" class="rounded-xl border-0 bg-slate-100/80 px-3 py-2.5 text-sm text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all">
                    <option value="">Všechny sekce</option>
                    @foreach($sections as $section)
                        <option value="{{ $section }}" @selected($currentSection === $section)>{{ $section }}</option>
                    @endforeach
                </select>

                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input
                        type="search"
                        name="q"
                        value="{{ $searchQuery }}"
                        placeholder="Hledat v dokumentech..."
                        class="w-64 rounded-xl border-0 bg-slate-100/80 pl-9 pr-4 py-2.5 text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:shadow-lg focus:shadow-indigo-500/10 transition-all"
                    >
                </div>
            </form>
        </div>

        <div class="reveal">
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 divide-y divide-slate-100">
            @forelse($documents as $doc)
                <a href="{{ route('documents.show', $doc) }}" class="block px-5 py-4 hover:bg-slate-50/80 transition-colors first:rounded-t-2xl last:rounded-b-2xl">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-900">{{ $doc->title }}</p>
                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                <x-section-badge :section="$doc->section" />
                                @if($doc->published_date)
                                    <span class="text-xs text-slate-500">{{ $doc->published_date->format('j. n. Y') }}</span>
                                @endif
                                @if($doc->department)
                                    <span class="text-xs text-slate-400">&middot; {{ $doc->department }}</span>
                                @endif
                                @if($doc->attachments->count() > 0)
                                    <span class="inline-flex items-center gap-1 text-xs text-slate-400">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                                        {{ $doc->attachments->count() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <svg class="shrink-0 h-5 w-5 text-slate-300 mt-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </div>
                </a>
            @empty
                <x-empty-state
                    title="Žádné dokumenty nenalezeny"
                    description="Zkuste změnit filtry nebo hledaný výraz."
                    icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>'
                />
            @endforelse
            </div>

            <div class="mt-6">
                {{ $documents->links() }}
            </div>
        </div>
    </div>

</x-layouts.app>
