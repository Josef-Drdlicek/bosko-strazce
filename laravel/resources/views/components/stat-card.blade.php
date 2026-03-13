@props(['label', 'value', 'href' => null])

<div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
    <dt class="text-sm font-medium text-gray-500">{{ $label }}</dt>
    <dd class="mt-2 text-3xl font-bold tracking-tight text-gray-900">
        @if($href)
            <a href="{{ $href }}" class="hover:text-blue-600 transition-colors">{{ $value }}</a>
        @else
            {{ $value }}
        @endif
    </dd>
</div>
