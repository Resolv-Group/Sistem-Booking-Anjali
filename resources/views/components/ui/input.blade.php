@props([
    'label' => '',
])

<div class="space-y-2">
    @if ($label)
        <label class="text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif

    <input
        {{ $attributes->merge([
            'class' =>
                'w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:outline-none'
        ]) }}
    >
</div>