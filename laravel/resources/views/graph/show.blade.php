<x-layouts.app :title="'Graf vztahů — ' . $entity->name">

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('entities.show', $entity) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors mb-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                    Zpět na {{ $entity->name }}
                </a>
                <h1 class="text-2xl font-extrabold text-slate-900">Graf vztahů</h1>
                <p class="mt-1 text-sm text-slate-500">Interaktivní vizualizace propojení subjektu <span class="font-semibold text-slate-700">{{ $entity->name }}</span> s dalšími entitami</p>
            </div>

            <div class="flex items-center gap-4 text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="inline-block h-3 w-3 rounded-full bg-violet-500"></span>
                    <span class="text-slate-600">Organizace</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="inline-block h-3 w-3 rounded-full bg-blue-500"></span>
                    <span class="text-slate-600">Osoby</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="inline-block h-3 w-3 rounded-full bg-indigo-700"></span>
                    <span class="text-slate-600">Centrální uzel</span>
                </div>
            </div>
        </div>

        <div x-data="graphFilters()" class="space-y-4">
            <div class="flex flex-wrap items-center gap-3 rounded-xl bg-white shadow-sm ring-1 ring-slate-200/60 px-5 py-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Filtr vztahů:</span>
                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" x-model="showContracts" @change="applyFilter()" class="h-3.5 w-3.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                    <span class="text-sm text-slate-700">Smlouvy</span>
                </label>
                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" x-model="showDocuments" @change="applyFilter()" class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-indigo-500"></span>
                    <span class="text-sm text-slate-700">Dokumenty</span>
                </label>
                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" x-model="showSubsidies" @change="applyFilter()" class="h-3.5 w-3.5 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                    <span class="text-sm text-slate-700">Dotace</span>
                </label>
            </div>

            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 overflow-hidden" style="height: 600px;" id="graph-container">
                <svg id="graph-svg" class="w-full h-full"></svg>
            </div>

            <div class="rounded-xl bg-slate-50 ring-1 ring-inset ring-slate-200 p-4 text-xs text-slate-500">
                <strong class="text-slate-700">Ovládání:</strong>
                Tažení myší = posunutí grafu. Kolečko = zoom. Klik na uzel = přechod na detail. Tažení uzlu = přemístění.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/d3@7/dist/d3.min.js"></script>
    <script>
        function graphFilters() {
            return {
                showContracts: true,
                showDocuments: true,
                showSubsidies: true,
                applyFilter() {
                    if (!window._graphElements) return;
                    const { link, linkLabel, node } = window._graphElements;
                    const visibleTypes = new Set();
                    if (this.showContracts) visibleTypes.add('contract');
                    if (this.showDocuments) visibleTypes.add('document');
                    if (this.showSubsidies) visibleTypes.add('subsidy');

                    const visibleNodeIds = new Set();
                    link.attr('display', d => {
                        const visible = visibleTypes.has(d.type);
                        if (visible) {
                            visibleNodeIds.add(typeof d.source === 'object' ? d.source.id : d.source);
                            visibleNodeIds.add(typeof d.target === 'object' ? d.target.id : d.target);
                        }
                        return visible ? null : 'none';
                    });
                    linkLabel.attr('display', d => visibleTypes.has(d.type) ? null : 'none');
                    node.attr('display', d => (d.central || visibleNodeIds.has(d.id)) ? null : 'none');
                },
            };
        }

        (function () {
            const graphData = @json($graphData);
            const container = document.getElementById('graph-container');
            const svg = d3.select('#graph-svg');
            const width = container.clientWidth;
            const height = container.clientHeight;

            svg.attr('viewBox', [0, 0, width, height]);

            const edgeColors = {
                contract: '#10b981',
                document: '#6366f1',
                subsidy: '#f59e0b',
            };

            const nodeColors = {
                organization: '#8b5cf6',
                person: '#3b82f6',
                city_department: '#ec4899',
                default: '#64748b',
            };

            const simulation = d3.forceSimulation(graphData.nodes)
                .force('link', d3.forceLink(graphData.edges)
                    .id(d => d.id)
                    .distance(d => Math.max(120, 200 - d.weight * 10))
                )
                .force('charge', d3.forceManyBody()
                    .strength(d => d.central ? -400 : -200)
                )
                .force('center', d3.forceCenter(width / 2, height / 2))
                .force('collision', d3.forceCollide().radius(d => d.radius + 10));

            const g = svg.append('g');

            const zoom = d3.zoom()
                .scaleExtent([0.2, 4])
                .on('zoom', (event) => g.attr('transform', event.transform));

            svg.call(zoom);

            const link = g.append('g')
                .selectAll('line')
                .data(graphData.edges)
                .join('line')
                .attr('stroke', d => edgeColors[d.type] || '#cbd5e1')
                .attr('stroke-opacity', 0.6)
                .attr('stroke-width', d => Math.max(1, Math.min(d.weight, 6)));

            const linkLabel = g.append('g')
                .selectAll('text')
                .data(graphData.edges)
                .join('text')
                .attr('font-size', 9)
                .attr('fill', '#94a3b8')
                .attr('text-anchor', 'middle')
                .attr('dy', -4)
                .text(d => d.label);

            const node = g.append('g')
                .selectAll('g')
                .data(graphData.nodes)
                .join('g')
                .attr('cursor', 'pointer')
                .call(d3.drag()
                    .on('start', dragstarted)
                    .on('drag', dragged)
                    .on('end', dragended)
                );

            node.append('circle')
                .attr('r', d => d.radius)
                .attr('fill', d => d.central ? '#4f46e5' : (nodeColors[d.type] || nodeColors.default))
                .attr('stroke', '#fff')
                .attr('stroke-width', d => d.central ? 3 : 1.5)
                .attr('opacity', d => d.central ? 1 : 0.85);

            node.append('text')
                .attr('dy', d => d.radius + 14)
                .attr('text-anchor', 'middle')
                .attr('font-size', d => d.central ? 13 : 11)
                .attr('font-weight', d => d.central ? '700' : '500')
                .attr('fill', '#1e293b')
                .text(d => truncate(d.name, 30));

            if (graphData.nodes.some(d => d.totalAmount > 0 && !d.central)) {
                node.filter(d => d.totalAmount > 0)
                    .append('text')
                    .attr('dy', d => d.radius + 26)
                    .attr('text-anchor', 'middle')
                    .attr('font-size', 9)
                    .attr('fill', '#64748b')
                    .text(d => formatAmount(d.totalAmount));
            }

            node.on('click', (event, d) => {
                window.location.href = '/entities/' + d.id;
            });

            node.append('title')
                .text(d => d.name + (d.ico ? ' (IČO: ' + d.ico + ')' : '') + (d.totalAmount ? '\n' + formatAmount(d.totalAmount) : ''));

            window._graphElements = { link, linkLabel, node };

            simulation.on('tick', () => {
                link
                    .attr('x1', d => d.source.x)
                    .attr('y1', d => d.source.y)
                    .attr('x2', d => d.target.x)
                    .attr('y2', d => d.target.y);

                linkLabel
                    .attr('x', d => (d.source.x + d.target.x) / 2)
                    .attr('y', d => (d.source.y + d.target.y) / 2);

                node.attr('transform', d => `translate(${d.x},${d.y})`);
            });

            function dragstarted(event, d) {
                if (!event.active) simulation.alphaTarget(0.3).restart();
                d.fx = d.x;
                d.fy = d.y;
            }

            function dragged(event, d) {
                d.fx = event.x;
                d.fy = event.y;
            }

            function dragended(event, d) {
                if (!event.active) simulation.alphaTarget(0);
                d.fx = null;
                d.fy = null;
            }

            function truncate(str, max) {
                return str.length > max ? str.substring(0, max) + '…' : str;
            }

            function formatAmount(amount) {
                if (amount >= 1e9) return (amount / 1e9).toFixed(1) + ' mld.';
                if (amount >= 1e6) return (amount / 1e6).toFixed(1) + ' mil.';
                if (amount >= 1e3) return (amount / 1e3).toFixed(0) + ' tis.';
                return amount.toFixed(0) + ' CZK';
            }
        })();
    </script>

</x-layouts.app>
