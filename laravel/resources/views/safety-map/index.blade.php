<x-layouts.app title="Mapa bezpečnosti" metaDescription="Označte místa v Boskovicích, kde se necítíte bezpečně. Pomůžete městu identifikovat problematická místa.">

    <div class="space-y-10">

        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-teal-700 to-cyan-800 px-6 py-12 text-white shadow-xl sm:px-12 sm:py-16 animate-fade-up">
            <div class="absolute inset-0 dot-grid opacity-15"></div>
            <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -left-12 -bottom-16 h-56 w-56 rounded-full bg-emerald-300/10 blur-3xl"></div>
            <div class="relative max-w-2xl">
                <span class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm px-4 py-1.5 text-xs font-semibold text-white/90 ring-1 ring-white/20">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    Pro občany Boskovic
                </span>
                <h1 class="text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">Kde se necítíte bezpečně?</h1>
                <p class="mt-4 text-lg text-emerald-100 leading-relaxed">
                    Klikněte na místo v mapě a řekněte nám, co vás trápí.
                    Špatné osvětlení, rozbitý chodník, nebezpečná křižovatka &mdash;
                    každý podnět pomáhá zlepšit naše město.
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 ring-1 ring-inset ring-emerald-200 p-5 animate-fade-up">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- STATS --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6 reveal">
            @php
                $categories = [
                    'lighting' => ['label' => 'Osvětlení', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/>', 'color' => 'amber'],
                    'sidewalk' => ['label' => 'Chodníky', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>', 'color' => 'orange'],
                    'traffic' => ['label' => 'Doprava', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H18.75m-7.5-2.25h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605"/>', 'color' => 'red'],
                    'vandalism' => ['label' => 'Vandalismus', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286ZM12 15.75h.007v.008H12v-.008Z"/>', 'color' => 'rose'],
                    'other' => ['label' => 'Ostatní', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>', 'color' => 'slate'],
                ];
            @endphp
            <div class="col-span-2 sm:col-span-3 lg:col-span-1 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/60 text-center hover-lift">
                <p class="text-3xl font-black text-slate-900 tabular-nums">{{ $totalReports }}</p>
                <p class="mt-1 text-sm font-medium text-slate-500">Celkem podnětů</p>
            </div>
            @foreach($categories as $key => $cat)
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/60 text-center hover-lift">
                    <p class="text-2xl font-bold text-slate-900 tabular-nums">{{ $categoryCounts[$key] ?? 0 }}</p>
                    <p class="mt-1 text-xs font-medium text-slate-500">{{ $cat['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- MAP + FORM --}}
        <div x-data="safetyMap()" class="grid grid-cols-1 gap-6 lg:grid-cols-3 reveal">

            {{-- MAP --}}
            <div class="lg:col-span-2">
                <div class="rounded-3xl bg-white shadow-lg ring-1 ring-slate-200/60 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Mapa Boskovic</h2>
                            <p class="text-xs text-slate-500">Klikněte na místo, které chcete nahlásit</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                            </span>
                            <span class="text-xs font-medium text-slate-500">Živá data</span>
                        </div>
                    </div>
                    <div id="safety-map" class="h-[500px] sm:h-[600px]"></div>
                </div>

                {{-- LEGEND --}}
                <div class="mt-4 flex flex-wrap gap-3">
                    @foreach($categories as $key => $cat)
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <span class="inline-block h-3 w-3 rounded-full bg-{{ $cat['color'] }}-500"></span>
                            {{ $cat['label'] }}
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- FORM --}}
            <div>
                <form action="{{ route('safety-map.store') }}" method="POST" class="rounded-3xl bg-white shadow-lg ring-1 ring-slate-200/60 p-6 sticky top-24">
                    @csrf
                    <h2 class="text-lg font-bold text-slate-900 mb-1">Nahlásit místo</h2>
                    <p class="text-sm text-slate-500 mb-6">Klikněte do mapy, vyplňte formulář a odešlete. Jméno je nepovinné.</p>

                    @if($errors->any())
                        <div class="mb-5 rounded-xl bg-rose-50 ring-1 ring-inset ring-rose-200 p-4">
                            <ul class="text-sm text-rose-700 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <input type="hidden" name="latitude" x-model="lat">
                        <input type="hidden" name="longitude" x-model="lng">

                        <div>
                            <div class="rounded-xl bg-slate-50 ring-1 ring-inset ring-slate-200 p-3 text-center" :class="lat ? 'bg-emerald-50 ring-emerald-200' : ''">
                                <template x-if="!lat">
                                    <p class="text-sm text-slate-500">
                                        <svg class="inline h-4 w-4 mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                        Klikněte do mapy pro výběr místa
                                    </p>
                                </template>
                                <template x-if="lat">
                                    <p class="text-sm font-semibold text-emerald-700">
                                        <svg class="inline h-4 w-4 mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                        Místo vybráno
                                    </p>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-semibold text-slate-700 mb-1.5">Kategorie</label>
                            <select name="category" id="category" required class="w-full rounded-xl border-0 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-indigo-500/50 focus:bg-white transition-all">
                                <option value="">Vyberte kategorii</option>
                                <option value="lighting">Špatné osvětlení</option>
                                <option value="sidewalk">Rozbitý chodník / cesta</option>
                                <option value="traffic">Nebezpečná dopravní situace</option>
                                <option value="vandalism">Vandalismus / nepořádek</option>
                                <option value="other">Jiné</option>
                            </select>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-semibold text-slate-700 mb-1.5">Popis problému</label>
                            <textarea name="description" id="description" rows="4" required maxlength="1000" placeholder="Popište, co vás na tomto místě trápí…" class="w-full rounded-xl border-0 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500/50 focus:bg-white transition-all resize-none"></textarea>
                        </div>

                        <div>
                            <label for="reporter_name" class="block text-sm font-semibold text-slate-700 mb-1.5">Vaše jméno <span class="font-normal text-slate-400">(nepovinné)</span></label>
                            <input type="text" name="reporter_name" id="reporter_name" maxlength="100" placeholder="Jan Novák" class="w-full rounded-xl border-0 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500/50 focus:bg-white transition-all">
                        </div>

                        <button type="submit" :disabled="!lat" class="w-full rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-600/25 hover:bg-emerald-500 disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none transition-all">
                            Odeslat podnět
                        </button>
                    </div>
                </form>

                <div class="mt-4 rounded-2xl bg-slate-50 ring-1 ring-inset ring-slate-200 p-5">
                    <h3 class="text-sm font-bold text-slate-900 mb-2">Jak to funguje?</h3>
                    <ol class="space-y-2 text-sm text-slate-600">
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">1</span>
                            Klikněte do mapy na místo, které chcete nahlásit
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">2</span>
                            Vyberte kategorii a popište problém
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">3</span>
                            Odešlete podnět &mdash; zobrazí se na mapě pro ostatní občany
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- INFO --}}
        <div class="reveal">
            <x-info-box variant="neutral" title="O mapě bezpečnosti">
                Mapa slouží občanům města Boskovice k zaznamenávání míst, kde se necítí bezpečně nebo kde vidí problém. Údaje jsou anonymní (jméno je nepovinné). Cílem je poskytnout přehled městu i veřejnosti o problematických lokalitách.
            </x-info-box>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('safetyMap', () => ({
            lat: null,
            lng: null,
            map: null,
            marker: null,
            categoryColors: {
                lighting: '#f59e0b',
                sidewalk: '#f97316',
                traffic: '#ef4444',
                vandalism: '#f43f5e',
                other: '#64748b',
            },
            categoryLabels: {
                lighting: 'Osvětlení',
                sidewalk: 'Chodníky',
                traffic: 'Doprava',
                vandalism: 'Vandalismus',
                other: 'Ostatní',
            },
            init() {
                this.$nextTick(() => {
                    this.map = L.map('safety-map', {
                        zoomControl: true,
                        scrollWheelZoom: true,
                    }).setView([49.4878, 16.6611], 14);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                        maxZoom: 19,
                    }).addTo(this.map);

                    this.map.on('click', (e) => {
                        this.lat = e.latlng.lat.toFixed(7);
                        this.lng = e.latlng.lng.toFixed(7);

                        if (this.marker) {
                            this.marker.setLatLng(e.latlng);
                        } else {
                            this.marker = L.marker(e.latlng, {
                                icon: L.divIcon({
                                    className: '',
                                    html: '<div style="width:24px;height:24px;background:#10b981;border:3px solid white;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,0.3)"></div>',
                                    iconSize: [24, 24],
                                    iconAnchor: [12, 12],
                                }),
                            }).addTo(this.map);
                        }
                    });

                    this.loadReports();
                });
            },
            loadReports() {
                fetch('{{ route("safety-map.geojson") }}')
                    .then(r => r.json())
                    .then(geojson => {
                        L.geoJSON(geojson, {
                            pointToLayer: (feature, latlng) => {
                                const color = this.categoryColors[feature.properties.category] || '#64748b';
                                return L.circleMarker(latlng, {
                                    radius: 8,
                                    fillColor: color,
                                    color: '#fff',
                                    weight: 2,
                                    opacity: 1,
                                    fillOpacity: 0.85,
                                });
                            },
                            onEachFeature: (feature, layer) => {
                                const p = feature.properties;
                                const catLabel = this.categoryLabels[p.category] || p.category;
                                layer.bindPopup(`
                                    <div style="min-width:180px">
                                        <div style="font-weight:700;font-size:13px;margin-bottom:4px">${catLabel}</div>
                                        <div style="font-size:12px;color:#475569;line-height:1.4">${p.description}</div>
                                        <div style="font-size:11px;color:#94a3b8;margin-top:6px">${p.created_at}${p.reporter_name ? ' · ' + p.reporter_name : ''}</div>
                                    </div>
                                `);
                            },
                        }).addTo(this.map);
                    });
            },
        }));
    });
    </script>
</x-layouts.app>
