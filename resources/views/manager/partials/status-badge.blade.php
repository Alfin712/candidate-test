@props(['status'])
@php
    $map = [
        'Active' => 'bg-emerald-100 text-emerald-700',
        'Inactive' => 'bg-gray-100 text-gray-700',
        'Draft' => 'bg-amber-100 text-amber-700',
        'Archived' => 'bg-gray-100 text-gray-600',
    ];
    $class = $map[$status] ?? 'bg-gray-100 text-gray-700';
@endphp
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $class }}">{{ $status }}</span>
