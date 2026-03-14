@props(['label', 'value', 'href' => null, 'icon' => null, 'color' => 'indigo'])

@php
$colorMap = [
    'indigo' => 'from-indigo-500 to-indigo-600 shadow-indigo-200',
    'emerald' => 'from-emerald-500 to-emerald-600 shadow-emerald-200',
    'amber' => 'from-amber-500 to-amber-600 shadow-amber-200',
    'rose' => 'from-rose-500 to-rose-600 shadow-rose-200',
    'violet' => 'from-violet-500 to-violet-600 shadow-violet-200',
    'sky' => 'from-sky-500 to-sky-600 shadow-sky-200',
];
$gradientClass = $colorMap[$color] ?? $colorMap['indigo'];
@endphp

<div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60 hover:shadow-md transition-shadow">
    @if($icon)
        <div class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br {{ $gradientClass }} text-white shadow-lg">
            {!! $icon !!}
        </div>
    @endif
    <dt class="text-sm font-medium text-slate-500">{{ $label }}</dt>
    <dd class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900">
        @if($href)
            <a href="{{ $href }}" class="hover:text-indigo-600 transition-colors">{{ $value }}</a>
        @else
            {{ $value }}
        @endif
    </dd>
</div>
