@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
$base =
    'w-full rounded-2xl py-3 text-sm font-semibold transition';

$variants = [
    'primary' => 'bg-primary text-white',
    'secondary' => 'bg-gray-100 text-gray-700',
    'danger' => 'bg-red-500 text-white',
    'outline' => 'border border-gray-300 bg-white text-gray-700',
];

$class = $variants[$variant] ?? $variants['primary'];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => "$base $class"
    ]) }}
>
    {{ $slot }}
</button>