@props([
    'active' => 'dashboard',
])

<div
    class="fixed bottom-0 left-1/2 z-20 w-full max-w-sm -translate-x-1/2 border-t border-gray-200 bg-white"
>

    <div class="grid grid-cols-4">

        {{-- beranda --}}
        <a href="{{ route('landing') }}" 
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'beranda'
                ? 'text-primary'
                : 'text-gray-400'
            }}"
        >
            <span class="text-lg">🏠</span>
            Beranda
    </a>

        {{-- LAYANAN --}}
        <a href="{{route('layanan')}}"
            class="flex flex-col items-center py-3 text-xs
            {{ $active === 'layanan'
                ? 'text-primary'
                : 'text-gray-400'   
            }}"
        >
            <span class="text-lg">🪷</span>
            Layanan
        </a>

        {{-- BOOKING --}}
        <a href="{{ route('view.auth.login') }}" 
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