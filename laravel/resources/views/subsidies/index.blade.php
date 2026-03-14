<x-layouts.app title="Dotace">

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">Dotace</h1>
                <p class="mt-1 text-sm text-slate-500">Přehled dotací spojených s městem Boskovice</p>
            </div>

            <form action="{{ route('subsidies.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <select name="year" onchange="this.form.submit()" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 transition-all">
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
                        class="w-64 rounded-lg border border-slate-200 bg-white pl-9 pr-4 py-2 text-sm placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 transition-all"
                    >
                </div>
            </form>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr class="bg-slate-50/50">
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
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
                                    <p class="mt-4 text-sm text-slate-500">Žádné dotace nenalezeny.</p>
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
