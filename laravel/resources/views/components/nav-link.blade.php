@props(['active' => false])

<a {{ $attributes->merge([
    'class' => 'inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 ' .
        ($active
            ? 'bg-indigo-50 text-indigo-700 shadow-sm'
            : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900')
]) }}>
    {{ $slot }}
</a>
