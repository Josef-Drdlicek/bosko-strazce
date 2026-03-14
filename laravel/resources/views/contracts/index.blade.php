<x-layouts.app title="Smlouvy" metaDescription="Smlouvy města Boskovice z celostátního Registru smluv — dodavatelé, částky a data podpisu.">

    <div class="space-y-6">

        <x-breadcrumb :items="[['label' => 'Smlouvy']]" />

        <x-page-header
            title="Smlouvy města Boskovice"
            description="Každá smlouva, kterou město uzavřelo a která je evidována v celostátním Registru smluv. Vidíte s kým, za kolik a kdy."
            badge="Zdroj: Registr smluv (Hlídač státu)"
        />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 reveal">
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200/60 flex items-start gap-3 hover-lift">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Kolik město platí</p>
                    <p class="text-xs text-slate-500">U každé smlouvy vidíte částku, měnu a datum podpisu.</p>
                </div>
            </div>
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200/60 flex items-start gap-3 hover-lift">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">S kým město obchoduje</p>
                    <p class="text-xs text-slate-500">Dodavatelé jsou propojení se subjekty v databázi.</p>
                </div>
            </div>
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200/60 flex items-start gap-3 hover-lift">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Signály anomálií</p>
                    <p class="text-xs text-slate-500">Neobvyklé smlouvy jsou automaticky označeny v sekci Signály.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <form action="{{ route('contracts.index') }}" method="GET">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input
                        type="search"
                        name="q"
                        value="{{ $searchQuery }}"
                        placeholder="Hledat smlouvy..."
                        class="w-64 rounded-xl border-0 bg-slate-100/80 pl-9 pr-4 py-2.5 text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:shadow-lg focus:shadow-indigo-500/10 transition-all"
                    >
                </div>
            </form>
            <a href="{{ route('signals.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-rose-500 hover:text-rose-600 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                Která firma dostává nejvíce?
            </a>
        </div>

        <div class="reveal">
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr class="bg-slate-50/80">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Předmět</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Protistrana</th>
                                <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Částka</th>
                                <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Datum podpisu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($contracts as $contract)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-4">
                                    <a href="{{ route('contracts.show', $contract) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                        {{ Str::limit($contract->subject ?: 'Bez předmětu', 80) }}
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $contract->counterparty_name }}</td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    @if($contract->amount)
                                        <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-sm font-bold text-emerald-700">
                                            {{ number_format($contract->amount, 0, ',', "\u{00a0}") }}&nbsp;{{ $contract->currency }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-500 text-right whitespace-nowrap">
                                    {{ $contract->date_signed?->format('j. n. Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state
                                        title="Žádné smlouvy nenalezeny"
                                        description="Zkuste změnit hledaný výraz."
                                        icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/></svg>'
                                    />
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-6">
            {{ $contracts->links() }}
        </div>
    </div>

</x-layouts.app>
