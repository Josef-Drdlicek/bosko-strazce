@props(['title', 'description', 'href', 'icon', 'color' => 'indigo', 'count' => null])

@php
$borderColors = [
    'indigo' => 'border-l-indigo-500',
    'emerald' => 'border-l-emerald-500',
    'amber' => 'border-l-amber-500',
    'violet' => 'border-l-violet-500',
    'rose' => 'border-l-rose-500',
    'sky' => 'border-l-sky-500',
];
$iconBgs = [
    'indigo' => 'bg-indigo-50 text-indigo-600',
    'emerald' => 'bg-emerald-50 text-emerald-600',
    'amber' => 'bg-amber-50 text-amber-600',
    'violet' => 'bg-violet-50 text-violet-600',
    'rose' => 'bg-rose-50 text-rose-600',
    'sky' => 'bg-sky-50 text-sky-600',
];
@endphp

<a href="{{ $href }}" class="group block rounded-2xl border-l-4 {{ $borderColors[$color] ?? $borderColors['indigo'] }} bg-white p-5 shadow-sm ring-1 ring-slate-200/60 hover:shadow-md hover:ring-slate-300/60 transition-all">
    <div class="flex items-start gap-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $iconBgs[$color] ?? $iconBgs['indigo'] }}">
            {!! $icon !!}
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between gap-2">
                <h3 class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $title }}</h3>
                @if($count !== null)
                    <span class="shrink-0 text-lg font-extrabold text-slate-900 tabular-nums">{{ is_numeric($count) ? number_format($count) : $count }}</span>
                @endif
            </div>
            <p class="mt-1 text-sm text-slate-500 leading-relaxed">{{ $description }}</p>
        </div>
    </div>
</a>
