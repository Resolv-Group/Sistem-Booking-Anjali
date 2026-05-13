@props([
    'variant' => 'primary',
    'type' => 'button',
    'name' => null,
    'value' => null,
    'placeholder' => null,
    'label' => null,
])

@php
$base =
    'w-full rounded-2xl py-3 text-sm font-semibold transition flex items-center justify-center';

$variants = [
    'primary' => 'bg-primary text-white hover:bg-primary/90',
    'secondary' => 'bg-gray-100 text-gray-700 hover:bg-gray-200',
    'danger' => 'bg-red-500 text-white hover:bg-red-600',
    'outline' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50',
];

$class = $variants[$variant] ?? $variants['primary'];
$text = $slot->isNotEmpty() ? $slot : ($label ?? $placeholder);
@endphp

<button
    type="{{ $type }}"
    @if($name) name="{{ $name }}" @endif
    @if($value) value="{{ $value }}" @endif
    {{ $attributes->merge([
        'class' => "$base $class"
    ]) }}
>
    {{ $text }}
</button>