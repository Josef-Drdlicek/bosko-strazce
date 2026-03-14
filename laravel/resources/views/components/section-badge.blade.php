@props(['section'])

@php
$colors = [
    'uredni_deska' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
    'zapisy_zm' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
    'zapisy_rm' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
    'vyhlasky' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
    'rozpocty' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    'poskytnute_informace' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
    'archiv_uredni_desky' => 'bg-slate-50 text-slate-700 ring-slate-600/20',
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

<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $colors[$section] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20' }}">
    {{ $labels[$section] ?? $section }}
</span>
