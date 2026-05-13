@props([
    'active' => 'dashboard',
])

<div
    class="fixed bottom-0 left-1/2 z-20 w-full max-w-sm -translate-x-1/2 border-t border-gray-200 bg-white"
>

    <div class="grid grid-cols-4">

        <a href="{{ route('therapist.dashboard') }}"
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'dashboard'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">🏠</span>
            Dashboard
        </a>

        <button
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'jadwal'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">🕒</span>
            Jadwal
        </button>

        <button
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'pasien'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">🧑</span>
            Pasien
        </button>

        <a href="{{ route('therapist.profile') }}"
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'profil'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">👤</span>
            Profil
        </a>

    </div>

</div>