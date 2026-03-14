<x-layouts.app title="Signály">

    <div class="space-y-8">

        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Signály</h1>
            <p class="mt-1 text-sm text-slate-500">Automaticky detekované vzory a nesrovnalosti ve veřejných datech. Signály nejsou obvinění — pouze upozornění k ruční analýze.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-5">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Smlouvy celkem</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($summary['total_contracts']) }}</p>
            </div>
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-5">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Celková částka</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($summary['total_amount'], 0, ',', "\u{00a0}") }}&nbsp;CZK</p>
            </div>
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-5">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Unikátních dodavatelů</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($summary['unique_counterparties']) }}</p>
            </div>
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-5">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Medián celk. částky dodavatele</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($summary['median_contract_total'], 0, ',', "\u{00a0}") }}&nbsp;CZK</p>
            </div>
        </div>

        <section>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-rose-600 text-white shadow-lg shadow-rose-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Koncentrace zakázek</h2>
                    <p class="text-sm text-slate-500">Dodavatelé s nadprůměrným objemem smluv vzhledem k mediánu</p>
                </div>
            </div>

            @if($contractConcentration->isNotEmpty())
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 overflow-hidden">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Subjekt</th>
                                <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Smluv</th>
                                <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Celková částka</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Poměr k mediánu</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Období</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Závažnost</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($contractConcentration as $signal)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('entities.show', $signal->entity_id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                            {{ $signal->entity_name }}
                                        </a>
                                        @if($signal->entity_ico)
                                            <p class="text-xs text-slate-400 font-mono">{{ $signal->entity_ico }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-slate-600 text-right tabular-nums">{{ $signal->contract_count }}</td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="text-sm font-bold text-slate-900 tabular-nums">{{ number_format($signal->total_amount, 0, ',', "\u{00a0}") }}&nbsp;CZK</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="text-sm font-bold tabular-nums {{ $signal->amount_ratio >= 10 ? 'text-rose-600' : ($signal->amount_ratio >= 4 ? 'text-amber-600' : 'text-slate-600') }}">
                                            {{ $signal->amount_ratio }}×
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-slate-500">
                                        @if($signal->first_contract && $signal->last_contract)
                                            {{ \Illuminate\Support\Str::limit($signal->first_contract, 10, '') }} — {{ \Illuminate\Support\Str::limit($signal->last_contract, 10, '') }}
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @if($signal->severity === 'high')
                                            <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/20">Vysoká</span>
                                        @elseif($signal->severity === 'medium')
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Střední</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-slate-50 px-2.5 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">Nízká</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 px-5 py-16 text-center">
                    <p class="text-sm text-slate-400">Žádné signály koncentrace zakázek.</p>
                </div>
            @endif
        </section>

        <section>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Nejvyšší smlouvy</h2>
                    <p class="text-sm text-slate-500">Smlouvy s nejvyšší hodnotou pro přehled</p>
                </div>
            </div>

            @if($highValueContracts->isNotEmpty())
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 overflow-hidden">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Předmět</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Dodavatel</th>
                                <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Částka</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Datum</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($highValueContracts as $contract)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('contracts.show', $contract) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 line-clamp-2">
                                            {{ $contract->subject ?: 'Bez předmětu' }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-slate-600">{{ $contract->counterparty_name }}</td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="text-sm font-bold text-slate-900 tabular-nums">{{ number_format($contract->amount, 0, ',', "\u{00a0}") }}&nbsp;CZK</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-slate-500">{{ $contract->date_signed?->format('j. n. Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        @if($subsidyConcentration->isNotEmpty())
            <section>
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-lg shadow-amber-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Příjemci dotací</h2>
                        <p class="text-sm text-slate-500">Subjekty přijímající dotace z veřejných zdrojů</p>
                    </div>
                </div>

                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 overflow-hidden">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Příjemce</th>
                                <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Dotací</th>
                                <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Celková částka</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Období</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($subsidyConcentration as $signal)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('entities.show', $signal->id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                            {{ $signal->name }}
                                        </a>
                                        @if($signal->ico)
                                            <p class="text-xs text-slate-400 font-mono">{{ $signal->ico }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-slate-600 text-right tabular-nums">{{ $signal->subsidy_count }}</td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="text-sm font-bold text-slate-900 tabular-nums">{{ number_format($signal->total_amount, 0, ',', "\u{00a0}") }}&nbsp;CZK</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-slate-500">
                                        @if($signal->first_year && $signal->last_year)
                                            {{ $signal->first_year }}–{{ $signal->last_year }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <div class="rounded-2xl bg-amber-50 ring-1 ring-inset ring-amber-200 p-6">
            <div class="flex gap-3">
                <svg class="h-5 w-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
                <div>
                    <h3 class="text-sm font-bold text-amber-800">Upozornění k interpretaci</h3>
                    <p class="mt-1 text-sm text-amber-700">
                        Signály jsou automaticky generované heuristiky. Vysoký objem smluv může mít zcela legitimní důvody
                        (např. dlouhodobý rámcový dodavatel, specializovaná služba). Každý signál je třeba ověřit
                        proti zdrojovým dokumentům a zvážit kontext.
                    </p>
                </div>
            </div>
        </div>

    </div>

</x-layouts.app>
