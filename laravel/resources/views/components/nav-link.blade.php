@props(['active' => false])

<a {{ $attributes->merge([
    'class' => 'inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 ' .
        ($active
            ? 'bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100'
            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
]) }}>
    {{ $slot }}
</a>
