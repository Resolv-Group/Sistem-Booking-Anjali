@props([
    'name',
    'specialist' => '',
    'status' => 'Aktif',
])

<x-ui.card>

    <div class="flex items-start justify-between">

        <div>

            <h3 class="font-semibold text-gray-800">
                {{ $name }}
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                {{ $specialist }}
            </p>

        </div>

        <x-ui.badge variant="success">
            {{ $status }}
        </x-ui.badge>

    </div>

</x-ui.card>