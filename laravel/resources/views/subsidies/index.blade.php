<x-layouts.app title="Dotace" metaDescription="Dotace spojené s městem Boskovice — z ministerstev, EU fondů i vlastních programů.">

    <div class="space-y-6">

        <x-breadcrumb :items="[['label' => 'Dotace']]" />

        <x-page-header
            title="Dotace města Boskovice"
            description="Peníze, které město Boskovice dostalo nebo rozdalo — z ministerstev, EU fondů i vlastních programů. U každé dotace vidíte částku, poskytovatele i příjemce."
            badge="Zdroj: CEDR / EU fondy (Hlídač státu)"
        />

        <div class="reveal grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="hover-lift rounded-2xl bg-white p-5 ring-1 ring-slate-200/60 flex items-start gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Kolik peněz proudí</p>
                    <p class="text-xs text-slate-500">Celkové částky dotací za jednotlivé roky.</p>
                </div>
            </div>
            <div class="hover-lift rounded-2xl bg-white p-5 ring-1 ring-slate-200/60 flex items-start gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Kdo peníze dostává</p>
                    <p class="text-xs text-slate-500">Příjemci jsou propojení se subjekty v databázi.</p>
                </div>
            </div>
            <div class="hover-lift rounded-2xl bg-white p-5 ring-1 ring-slate-200/60 flex items-start gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Časové návaznosti</p>
                    <p class="text-xs text-slate-500">Dotace blízké smlouvám jsou hlášeny jako signály.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <form action="{{ route('subsidies.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <select name="year" onchange="this.form.submit()" class="rounded-xl border-0 bg-slate-100/80 px-3 py-2 text-sm text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all">
                    <option value="">Všechny roky</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" @selected($currentYear == $year)>{{ $year }}</option>
                    @endforeach
                </select>

                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input
                        type="search"
                        name="q"
                        value="{{ $searchQuery }}"
                        placeholder="Hledat v dotacích..."
                        class="w-64 rounded-xl border-0 bg-slate-100/80 pl-9 pr-4 py-2 text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all"
                    >
                </div>
            </form>
            <a href="{{ route('signals.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-amber-600 hover:text-amber-800 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
                Kdo dostává nejvíce dotací?
            </a>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr class="bg-slate-50/80">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Název</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Příjemce</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Program</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Částka</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Rok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($subsidies as $subsidy)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-4">
                                    <a href="{{ route('subsidies.show', $subsidy) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                        {{ Str::limit($subsidy->title, 60) }}
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $subsidy->recipient_name }}</td>
                                <td class="px-5 py-4 text-sm text-slate-500">{{ Str::limit($subsidy->program, 40) }}</td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    @if($subsidy->amount)
                                        <span class="inline-flex items-center rounded-lg bg-amber-50 px-2.5 py-1 text-sm font-bold text-amber-700">
                                            {{ number_format($subsidy->amount, 0, ',', "\u{00a0}") }}&nbsp;CZK
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-500 text-right">{{ $subsidy->year }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-empty-state
                                        title="Žádné dotace nenalezeny"
                                        description="Zkuste změnit rok nebo hledaný výraz."
                                        icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9"/></svg>'
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $subsidies->links() }}
        </div>
    </div>

</x-layouts.app>
