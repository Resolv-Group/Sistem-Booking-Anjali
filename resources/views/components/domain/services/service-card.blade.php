@props([
    'name',
    'price',
])

<x-ui.card>

    <div class="flex items-center justify-between">

        <div>

            <h3 class="font-semibold text-gray-800">
                {{ $name }}
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                Rp {{ number_format($price, 0, ',', '.') }}
            </p>

        </div>

        <x-ui.toggle-switch />

    </div>

</x-ui.card>