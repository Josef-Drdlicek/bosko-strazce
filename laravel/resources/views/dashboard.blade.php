<x-layouts.app title="Přehled">

    <div class="space-y-10">

        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 px-6 py-10 text-white shadow-xl shadow-indigo-200/50 sm:px-10 sm:py-14">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/5"></div>
            <div class="absolute -right-8 -bottom-20 h-48 w-48 rounded-full bg-white/5"></div>
            <div class="relative">
                <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Bosko Strážce</h1>
                <p class="mt-2 max-w-xl text-indigo-100 text-lg">
                    Antikorupční monitorovací platforma pro město Boskovice.
                    Transparentní přehled veřejných dat.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card
                label="Dokumenty"
                :value="number_format($stats['documents'])"
                :href="route('documents.index')"
                color="indigo"
                icon='<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>'
            />
            <x-stat-card
                label="Smlouvy"
                :value="number_format($stats['contracts'])"
                :href="route('contracts.index')"
                color="emerald"
                icon='<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/></svg>'
            />
            <x-stat-card
                label="Subjekty"
                :value="number_format($stats['entities'])"
                :href="route('entities.index')"
                color="violet"
                icon='<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>'
            />
            <x-stat-card
                label="Dotace"
                :value="number_format($stats['subsidies'])"
                :href="route('subsidies.index')"
                color="amber"
                icon='<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>'
            />
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Poslední dokumenty</h2>
                    <a href="{{ route('documents.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">Zobrazit vše &rarr;</a>
                </div>
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 divide-y divide-slate-100">
                    @forelse($recentDocuments as $doc)
                        <a href="{{ route('documents.show', $doc) }}" class="block px-5 py-4 hover:bg-slate-50/80 transition-colors first:rounded-t-2xl last:rounded-b-2xl">
                            <p class="text-sm font-semibold text-slate-900 truncate">{{ $doc->title }}</p>
                            <div class="mt-1.5 flex items-center gap-2">
                                <x-section-badge :section="$doc->section" />
                                @if($doc->published_date)
                                    <span class="text-xs text-slate-500">{{ $doc->published_date->format('j. n. Y') }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <p class="px-5 py-10 text-center text-sm text-slate-400">Žádné dokumenty</p>
                    @endforelse
                </div>
            </section>

            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Poslední smlouvy</h2>
                    <a href="{{ route('contracts.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">Zobrazit vše &rarr;</a>
                </div>
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 divide-y divide-slate-100">
                    @forelse($recentContracts as $contract)
                        <a href="{{ route('contracts.show', $contract) }}" class="block px-5 py-4 hover:bg-slate-50/80 transition-colors first:rounded-t-2xl last:rounded-b-2xl">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $contract->subject ?: 'Bez předmětu' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $contract->counterparty_name }}</p>
                                </div>
                                @if($contract->amount)
                                    <span class="shrink-0 inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-sm font-bold text-emerald-700">
                                        {{ number_format($contract->amount, 0, ',', "\u{00a0}") }}&nbsp;{{ $contract->currency }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <p class="px-5 py-10 text-center text-sm text-slate-400">Žádné smlouvy</p>
                    @endforelse
                </div>
            </section>
        </div>

        @if($topCounterparties->isNotEmpty())
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Top dodavatelé</h2>
                </div>
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 overflow-hidden">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Dodavatel</th>
                                <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Smluv</th>
                                <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Celková částka</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($topCounterparties as $index => $cp)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-500">{{ $index + 1 }}</span>
                                            <span class="text-sm font-medium text-slate-900">{{ $cp->counterparty_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-slate-600 text-right">{{ $cp->contract_count }}</td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="text-sm font-bold text-slate-900">{{ number_format($cp->total_amount, 0, ',', "\u{00a0}") }}&nbsp;CZK</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

    </div>

</x-layouts.app>
