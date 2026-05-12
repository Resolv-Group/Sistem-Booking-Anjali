@props([
    'time' => '10:00',
    'remaining' => 5,
    'active' => false,
])

<div
    class="rounded-2xl border p-4 transition

    {{ $active
        ? 'border-primary bg-primary text-white'
        : 'border-gray-200 bg-white text-gray-800'
    }}"
>

    <div class="text-sm font-semibold">
        {{ $time }}
    </div>

    <div class="mt-1 text-xs opacity-80">
        Sisa {{ $remaining }} Slot
    </div>

</div>