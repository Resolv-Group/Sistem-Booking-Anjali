@props([
    'icon' => '⚙️',
    'title' => '',
    'subtitle' => '',
])

<x-ui.card>

    <div class="flex items-center justify-between">

        <div class="flex items-center gap-4">

            <div
                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-xl text-primary"
            >
                {{ $icon }}
            </div>

            <div>

                <h3 class="font-semibold text-gray-800">
                    {{ $title }}
                </h3>

                @if ($subtitle)
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $subtitle }}
                    </p>
                @endif

            </div>

        </div>

        <div class="text-gray-400">
            →
        </div>

    </div>

</x-ui.card>