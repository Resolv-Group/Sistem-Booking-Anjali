@props([
    'patient',
    'service',
    'time',
    'status' => 'pending',
])

<x-ui.card>

    <div class="space-y-4">

        <div class="flex items-start justify-between">

            <div>

                <h3 class="font-semibold text-gray-800">
                    {{ $patient }}
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $service }}
                </p>

            </div>

            @php
                $variant = match($status) {
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'warning'
                };
            @endphp

            <x-ui.badge :variant="$variant">
                {{ ucfirst($status) }}
            </x-ui.badge>

        </div>

        <div class="text-sm text-gray-500">
            {{ $time }}
        </div>

    </div>

</x-ui.card>