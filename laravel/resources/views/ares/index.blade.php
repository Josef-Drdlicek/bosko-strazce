<x-layouts.app title="ARES - Vyhledávání subjektů">

    <div class="space-y-8">
        <div class="text-center">
            <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-lg shadow-indigo-200 mb-4">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/></svg>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900">ARES</h1>
            <p class="mt-2 text-slate-500">Administrativní registr ekonomických subjektů — vyhledávání firem a organizací</p>
        </div>

        <form action="{{ route('ares.search') }}" method="GET" class="max-w-2xl mx-auto" x-data="{ type: '{{ $searchType }}' }">
            <div class="flex gap-3 mb-4 justify-center">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="type" value="name" x-model="type" class="text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-medium text-slate-700">Podle názvu</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="type" value="ico" x-model="type" class="text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-medium text-slate-700">Podle IČO</span>
                </label>
            </div>

            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                <input
                    type="search"
                    name="q"
                    value="{{ $query }}"
                    :placeholder="type === 'ico' ? 'Zadejte IČO (např. 00279978)...' : 'Zadejte název firmy...'"
                    autofocus
                    class="w-full rounded-2xl border border-slate-200 bg-white pl-12 pr-5 py-4 text-lg placeholder:text-slate-400 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-4 focus:ring-indigo-100 transition-all"
                >
            </div>

            <div class="mt-3 text-center">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    Vyhledat v ARES
                </button>
            </div>
        </form>

        @if(filled($query))
            @if(empty($results))
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                    <p class="mt-4 text-lg text-slate-500">Žádné výsledky pro &bdquo;{{ $query }}&ldquo;</p>
                </div>
            @else
                <div class="space-y-4">
                    <h2 class="text-lg font-bold text-slate-900">
                        Nalezeno {{ count($results) }} {{ count($results) === 1 ? 'subjekt' : (count($results) < 5 ? 'subjekty' : 'subjektů') }}
                    </h2>

                    @foreach($results as $result)
                        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 p-6 hover:shadow-md transition-shadow">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900">{{ $result['name'] }}</h3>
                                    <div class="mt-2 flex flex-wrap items-center gap-3">
                                        @if($result['ico'])
                                            <span class="inline-flex items-center gap-1 rounded-md bg-slate-50 px-2 py-1 text-xs font-mono font-medium text-slate-700 ring-1 ring-inset ring-slate-200">
                                                IČO: {{ $result['ico'] }}
                                            </span>
                                        @endif
                                        @if($result['legal_form'])
                                            <span class="inline-flex items-center rounded-md bg-violet-50 px-2 py-1 text-xs font-medium text-violet-700 ring-1 ring-inset ring-violet-600/20">
                                                {{ $result['legal_form'] }}
                                            </span>
                                        @endif
                                        @if($result['date_terminated'])
                                            <span class="inline-flex items-center rounded-md bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                                Zaniklý
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <dl class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @if(!empty($result['address']))
                                    <div>
                                        <dt class="text-xs font-medium text-slate-500">Sídlo</dt>
                                        <dd class="mt-0.5 text-sm text-slate-900">{{ $result['address'] }}</dd>
                                    </div>
                                @endif
                                @if($result['date_created'])
                                    <div>
                                        <dt class="text-xs font-medium text-slate-500">Datum vzniku</dt>
                                        <dd class="mt-0.5 text-sm text-slate-900">{{ $result['date_created'] }}</dd>
                                    </div>
                                @endif
                                @if(!empty($result['cz_nace']))
                                    <div>
                                        <dt class="text-xs font-medium text-slate-500">CZ-NACE</dt>
                                        <dd class="mt-0.5 flex flex-wrap gap-1">
                                            @foreach(array_slice($result['cz_nace'], 0, 3) as $nace)
                                                <span class="inline-flex items-center rounded bg-slate-100 px-1.5 py-0.5 text-xs text-slate-600">{{ $nace }}</span>
                                            @endforeach
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="max-w-2xl mx-auto">
                <div class="rounded-2xl bg-indigo-50/50 ring-1 ring-inset ring-indigo-100 p-6">
                    <h3 class="text-sm font-semibold text-indigo-900">O registru ARES</h3>
                    <p class="mt-2 text-sm text-indigo-700 leading-relaxed">
                        ARES (Administrativní registr ekonomických subjektů) je veřejný registr spravovaný Ministerstvem financí ČR.
                        Obsahuje údaje o ekonomických subjektech registrovaných v České republice — obchodní název, IČO, sídlo, právní formu a další.
                    </p>
                    <a href="https://ares.gov.cz" target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        ares.gov.cz
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                    </a>
                </div>
            </div>
        @endif
    </div>

</x-layouts.app>
