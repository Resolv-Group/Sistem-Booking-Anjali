@props([
    'active' => 'dashboard',
])

<div
    class="fixed bottom-0 left-1/2 z-20 w-full max-w-sm -translate-x-1/2 border-t border-gray-200 bg-white"
>

    <div class="grid grid-cols-4">

        {{-- DASHBOARD --}}
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

        {{-- REFERRAL --}}
        <button
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'referral'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">🎁</span>
            Referral
        </button>

        {{-- PROFILE --}}
        <button
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'profile'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">👤</span>
            Profil
        </button>

    </div>

</div>