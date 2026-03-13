@props(['active' => false])

<a {{ $attributes->merge([
    'class' => 'inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors ' .
        ($active
            ? 'bg-blue-50 text-blue-700'
            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900')
]) }}>
    {{ $slot }}
</a>
