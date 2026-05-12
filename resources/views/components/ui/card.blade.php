<div
    {{ $attributes->merge([
        'class' =>
            'rounded-2xl border border-gray-100 bg-white p-4 shadow-sm'
    ]) }}
>
    {{ $slot }}
</div>