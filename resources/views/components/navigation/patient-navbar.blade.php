@props([
    'active' => 'dashboard',
])

<div
    class="fixed bottom-0 left-1/2 z-20 w-full max-w-sm -translate-x-1/2 border-t border-gray-200 bg-white"
>

    <div class="grid grid-cols-4">

        {{-- DASHBOARD --}}
        <a href="{{ route('patient.dashboard') }}"
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'dashboard'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">🏠</span>
            Dashboard
        </a>

        {{-- TERAPIS --}}
        <a href="{{ route('patient.therapist') }}"
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'therapists'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">💆‍♀️</span>
            Terapis
        </a>

        {{-- BOOKING --}}
        <a href="{{ route('patient.booking.index') }}"
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'booking'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">📅</span>
            Booking
        </a>

        {{-- PROFILE --}}
        <a href="{{ route('patient.profile') }}"
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'profile'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">👤</span>
            Profil
        </a>

    </div>

</div>