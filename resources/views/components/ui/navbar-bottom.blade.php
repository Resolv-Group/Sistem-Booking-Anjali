@props([
    'role' => 'patient'
])

@php
    $menus = [];

    switch ($role) {
        case 'admin global':
            $menus = [
                ['icon' => '🏠', 'label' => 'Home', 'active' => true],
                ['icon' => '🏢', 'label' => 'Cabang', 'active' => false],
                ['icon' => '👤', 'label' => 'Pengguna', 'active' => false],
                ['icon' => '⚙️', 'label' => 'Lainnya', 'active' => false],
            ];
            break;
        case 'admin rumah terapi':
            $menus = [
                ['icon' => '🏠', 'label' => 'Home', 'active' => true],
                ['icon' => '📋', 'label' => 'Booking', 'active' => false],
                ['icon' => '📆', 'label' => 'Jadwal', 'active' => false],
                ['icon' => '👥', 'label' => 'Pasien', 'active' => false],
            ];
            break;
        case 'terapis':
            $menus = [
                ['icon' => '🏠', 'label' => 'Home', 'active' => true],
                ['icon' => '📋', 'label' => 'Booking', 'active' => false],
                ['icon' => '📆', 'label' => 'Jadwal', 'active' => false],
                ['icon' => '👤', 'label' => 'Profil', 'active' => false],
            ];
            break;
        case 'patient':
        default:
            $menus = [
                ['icon' => '🏠', 'label' => 'Home', 'active' => true],
                ['icon' => '💆', 'label' => 'Layanan', 'active' => false],
                ['icon' => '🧑‍⚕️', 'label' => 'Terapis', 'active' => false],
                ['icon' => '📋', 'label' => 'Booking', 'active' => false],
            ];
            break;
    }
@endphp

<div
    class="fixed bottom-0 left-1/2 w-full max-w-sm -translate-x-1/2 border-t border-gray-200 bg-white"
>

    <div class="grid grid-cols-4">

        @foreach ($menus as $menu)
            <button class="flex flex-col items-center py-3 {{ $menu['active'] ? 'text-primary' : 'text-gray-400 hover:text-primary' }} transition">
                <span class="text-lg">{{ $menu['icon'] }}</span>
                <span class="text-[10px] font-medium mt-1">{{ $menu['label'] }}</span>
            </button>
        @endforeach

    </div>

</div>