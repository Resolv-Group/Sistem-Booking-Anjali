@props([
    'variant' => 'success',
])

@php
$variants = [
    'success' => 'bg-green-50 text-green-700',
    'danger' => 'bg-red-50 text-red-700',
    'warning' => 'bg-yellow-50 text-yellow-700',
    'info' => 'bg-blue-50 text-blue-700',
];
@endphp

<span
    class="rounded-full px-3 py-1 text-xs font-medium {{ $variants[$variant] }}"
>
    {{ $slot }}
</span>