@props([
    'active' => 'dashboard',
])

<div
    class="fixed bottom-0 left-1/2 z-20 w-full max-w-sm -translate-x-1/2 border-t border-gray-200 bg-white"
>

    <div class="grid grid-cols-4">

        {{-- beranda --}}
        <button
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'beranda'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">🏠</span>
            Beranda
        </button>

        {{-- LAYANAN --}}
        <button
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'layanan'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">🪷</span>
            Layanan
        </button>

        {{-- BOOKING --}}
        <button
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'booking'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">📅</span>
            Booking
        </button>

        {{-- PROFILE --}}
        <a href="{{ route('view.auth.login') }}"
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'profile'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">👤</span>
            Login
        </a>

    </div>

</div>