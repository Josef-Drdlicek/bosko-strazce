@props(['variant' => 'info', 'title' => null])

@php
$styles = [
    'info' => 'bg-sky-50 ring-sky-200 text-sky-800',
    'warning' => 'bg-amber-50 ring-amber-200 text-amber-800',
    'tip' => 'bg-emerald-50 ring-emerald-200 text-emerald-800',
    'neutral' => 'bg-slate-50 ring-slate-200 text-slate-700',
];
$icons = [
    'info' => '<path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>',
    'warning' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>',
    'tip' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/>',
    'neutral' => '<path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>',
];
$iconColors = [
    'info' => 'text-sky-500',
    'warning' => 'text-amber-500',
    'tip' => 'text-emerald-500',
    'neutral' => 'text-slate-400',
];
@endphp

<div class="rounded-2xl ring-1 ring-inset {{ $styles[$variant] ?? $styles['info'] }} p-5 sm:p-6">
    <div class="flex gap-3">
        <svg class="h-5 w-5 {{ $iconColors[$variant] ?? $iconColors['info'] }} shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $icons[$variant] ?? $icons['info'] !!}</svg>
        <div class="min-w-0">
            @if($title)
                <h3 class="text-sm font-bold">{{ $title }}</h3>
                <div class="mt-1 text-sm leading-relaxed">{{ $slot }}</div>
            @else
                <div class="text-sm leading-relaxed">{{ $slot }}</div>
            @endif
        </div>
    </div>
</div>
