@props([
    'active' => 'dashboard',
])

<div
    class="fixed bottom-0 left-1/2 z-20 w-full max-w-sm -translate-x-1/2 border-t border-gray-200 bg-white"
>

    <div class="grid grid-cols-4">

        <button
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'dashboard'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">🏠</span>
            Dashboard
        </button>

        <button
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'cabang'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">🏢</span>
            Cabang
        </button>

        <button
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'pengguna'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">👥</span>
            Pengguna
        </button>

        <button
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'lainnya'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">⚙️</span>
            Lainnya
        </button>

    </div>

</div>