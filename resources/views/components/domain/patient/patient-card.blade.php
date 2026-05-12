@props([
    'name',
    'phone',
])

<x-ui.card>

    <div class="space-y-1">

        <h3 class="font-semibold text-gray-800">
            {{ $name }}
        </h3>

        <p class="text-sm text-gray-500">
            {{ $phone }}
        </p>

    </div>

</x-ui.card>