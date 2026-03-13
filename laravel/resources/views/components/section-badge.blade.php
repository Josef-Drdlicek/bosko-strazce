@props(['section'])

@php
$colors = [
    'uredni_deska' => 'bg-blue-100 text-blue-800',
    'zapisy_zm' => 'bg-purple-100 text-purple-800',
    'zapisy_rm' => 'bg-indigo-100 text-indigo-800',
    'vyhlasky' => 'bg-red-100 text-red-800',
    'rozpocty' => 'bg-green-100 text-green-800',
    'poskytnute_informace' => 'bg-yellow-100 text-yellow-800',
    'archiv_uredni_desky' => 'bg-gray-100 text-gray-800',
];
$labels = [
    'uredni_deska' => 'Úřední deska',
    'zapisy_zm' => 'Zápisy ZM',
    'zapisy_rm' => 'Zápisy RM',
    'vyhlasky' => 'Vyhlášky',
    'rozpocty' => 'Rozpočty',
    'poskytnute_informace' => 'Poskytnuté info',
    'archiv_uredni_desky' => 'Archiv ÚD',
];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colors[$section] ?? 'bg-gray-100 text-gray-800' }}">
    {{ $labels[$section] ?? $section }}
</span>
