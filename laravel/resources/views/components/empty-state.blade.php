@props(['icon' => null, 'title', 'description' => null, 'actionLabel' => null, 'actionHref' => null])

<div class="px-5 py-16 text-center">
    @if($icon)
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
            {!! $icon !!}
        </div>
    @endif
    <h3 class="mt-4 text-sm font-semibold text-slate-900">{{ $title }}</h3>
    @if($description)
        <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
    @endif
    @if($actionLabel && $actionHref)
        <a href="{{ $actionHref }}" class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 ring-1 ring-inset ring-indigo-600/20 hover:bg-indigo-100 transition-colors">
            {{ $actionLabel }}
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
        </a>
    @endif
</div>
