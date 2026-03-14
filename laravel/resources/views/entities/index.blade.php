<x-layouts.app title="Subjekty">

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">Subjekty</h1>
                <p class="mt-1 text-sm text-slate-500">Firmy, organizace a osoby propojené s městem Boskovice</p>
            </div>

            <form action="{{ route('entities.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <select name="type" onchange="this.form.submit()" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 transition-all">
                    <option value="">Všechny typy</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" @selected($currentType === $type)>{{ $type }}</option>
                    @endforeach
                </select>

                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input
                        type="search"
                        name="q"
                        value="{{ $searchQuery }}"
                        placeholder="Hledat subjekty..."
                        class="w-64 rounded-lg border border-slate-200 bg-white pl-9 pr-4 py-2 text-sm placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 transition-all"
                    >
                </div>
            </form>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 divide-y divide-slate-100">
            @forelse($entities as $entity)
                <a href="{{ route('entities.show', $entity) }}" class="block px-5 py-4 hover:bg-slate-50/80 transition-colors first:rounded-t-2xl last:rounded-b-2xl">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600 ring-1 ring-inset ring-violet-600/10">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">{{ $entity->name }}</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-md bg-slate-50 px-2 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                        {{ $entity->entity_type }}
                                    </span>
                                    @if($entity->ico)
                                        <span class="text-xs text-slate-500">IČO: {{ $entity->ico }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($entity->links_count ?? false)
                            <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/></svg>
                                {{ $entity->links_count }}
                            </span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="px-5 py-16 text-center">
                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                    <p class="mt-4 text-sm text-slate-500">Žádné subjekty nenalezeny.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $entities->links() }}
        </div>
    </div>

</x-layouts.app>
