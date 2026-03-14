<x-layouts.app title="Subjekty" metaDescription="Firmy, organizace a osoby propojené s městem Boskovice — dodavatelé, příjemci dotací a účastníci rozhodování.">

    <div class="space-y-6">

        <x-breadcrumb :items="[['label' => 'Subjekty']]" />

        <x-page-header
            title="Subjekty"
            description="Všechny firmy, organizace a osoby, které se ve veřejných datech města objevují — jako dodavatelé, příjemci dotací nebo účastníci rozhodování."
            badge="ARES &middot; Registr smluv &middot; Volby.cz"
        />

        <div class="reveal grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="hover-lift rounded-xl bg-white p-4 ring-1 ring-slate-200/60 flex items-start gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Propojení</p>
                    <p class="text-xs text-slate-500">Každý subjekt má vazby na smlouvy, dotace a dokumenty.</p>
                </div>
            </div>
            <div class="hover-lift rounded-xl bg-white p-4 ring-1 ring-slate-200/60 flex items-start gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Graf vztahů</p>
                    <p class="text-xs text-slate-500">Vizuální mapa propojení mezi subjekty.</p>
                </div>
            </div>
            <div class="hover-lift rounded-xl bg-white p-4 ring-1 ring-slate-200/60 flex items-start gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Data z ARES</p>
                    <p class="text-xs text-slate-500">Automaticky obohaceno o sídlo, vedení a obory.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <form action="{{ route('entities.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <select name="type" onchange="this.form.submit()" class="rounded-xl border-0 bg-slate-100/80 px-3 py-2 text-sm text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all">
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
                        class="w-64 rounded-xl border-0 bg-slate-100/80 pl-9 pr-4 py-2 text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all"
                    >
                </div>
            </form>
            <div class="flex items-center gap-3">
                <a href="{{ route('politicians.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-violet-600 hover:text-violet-800 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                    Zastupitelé
                </a>
            </div>
        </div>

        <div class="reveal rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 divide-y divide-slate-100">
            @forelse($entities as $entity)
                <a href="{{ route('entities.show', $entity) }}" class="hover-lift block px-5 py-4 hover:bg-slate-50/80 transition-colors first:rounded-t-2xl last:rounded-b-2xl">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            @php
                                $typeIcons = [
                                    'organization' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>',
                                    'person' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>',
                                    'city_body' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21V5.625c0-.621-.504-1.125-1.125-1.125h-5.25c-.621 0-1.125.504-1.125 1.125V21m0 0H3.375a1.125 1.125 0 0 1-.79-.325c-.44-.447-.563-1.126-.245-1.646l2.07-3.375c.283-.46.796-.746 1.348-.746h1.992v6.092M15.75 21h4.875a1.125 1.125 0 0 0 .79-.325c.44-.447.563-1.126.245-1.646l-2.07-3.375a1.5 1.5 0 0 0-1.348-.746h-1.992"/></svg>',
                                ];
                                $typeBgs = [
                                    'organization' => 'bg-violet-50 text-violet-600',
                                    'person' => 'bg-blue-50 text-blue-600',
                                    'city_body' => 'bg-purple-50 text-purple-600',
                                ];
                                $typeLabels = [
                                    'organization' => 'Firma',
                                    'person' => 'Osoba',
                                    'city_body' => 'Orgán města',
                                ];
                            @endphp
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $typeBgs[$entity->entity_type] ?? 'bg-slate-50 text-slate-600' }} ring-1 ring-inset ring-slate-200/60">
                                {!! $typeIcons[$entity->entity_type] ?? $typeIcons['organization'] !!}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">{{ $entity->name }}</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-md bg-slate-50 px-2 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                        {{ $typeLabels[$entity->entity_type] ?? $entity->entity_type }}
                                    </span>
                                    @if($entity->ico)
                                        <span class="text-xs text-slate-500 font-mono">IČO {{ $entity->ico }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($entity->links_count ?? false)
                            <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/></svg>
                                {{ $entity->links_count }} {{ $entity->links_count === 1 ? 'vazba' : ($entity->links_count < 5 ? 'vazby' : 'vazeb') }}
                            </span>
                        @endif
                    </div>
                </a>
            @empty
                <x-empty-state
                    title="Žádné subjekty nenalezeny"
                    description="Zkuste změnit typ nebo hledaný výraz."
                    icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>'
                />
            @endforelse
        </div>

        <div class="mt-6">
            {{ $entities->links() }}
        </div>
    </div>

</x-layouts.app>
