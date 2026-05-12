@extends('components.layouts.app')

@section('title', 'Lainnya')

@section('content')

<x-layouts.mobile-app>

    {{-- TOPBAR --}}
    <x-ui.topbar title="Lainnya">

        <x-slot:right>
            <img
                src="https://i.pravatar.cc/100"
                class="h-10 w-10 rounded-full object-cover"
            >
        </x-slot:right>

    </x-ui.topbar>

    {{-- CONTENT --}}
    <div class="space-y-6 p-4 pb-24">

        {{-- OPERASIONAL --}}
        <div class="space-y-4">

            <x-ui.section-title title="Operasional" />

            <x-domain.menu.menu-item
                icon="🕒"
                title="Jam Operasional"
                subtitle="Atur jadwal operasional cabang"
            />

            <x-domain.menu.menu-item
                icon="📅"
                title="Hari Libur"
                subtitle="Kelola hari libur dan tanggal merah"
            />

        </div>

        {{-- MANAGEMENT --}}
        <div class="space-y-4">

            <x-ui.section-title title="Management" />

            <x-domain.menu.menu-item
                icon="💆"
                title="Layanan"
                subtitle="Kelola layanan dan harga"
            />

            <x-domain.menu.menu-item
                icon="🎁"
                title="Referral"
                subtitle="Kelola referral dan point"
            />

            <x-domain.menu.menu-item
                icon="📢"
                title="Promo"
                subtitle="Kelola promo dan diskon"
            />

        </div>

        {{-- SYSTEM --}}
        <div class="space-y-4">

            <x-ui.section-title title="Sistem" />

            <x-domain.menu.menu-item
                icon="👥"
                title="Role & Permission"
                subtitle="Kelola hak akses pengguna"
            />

            <x-domain.menu.menu-item
                icon="📄"
                title="Laporan"
                subtitle="Lihat laporan sistem"
            />

        </div>

        {{-- ACCOUNT --}}
        <div class="space-y-4">

            <x-ui.section-title title="Akun" />

            <x-domain.menu.menu-item
                icon="⚙️"
                title="Pengaturan"
                subtitle="Pengaturan aplikasi"
            />

            <x-domain.menu.menu-item
                icon="🚪"
                title="Logout"
                subtitle="Keluar dari akun"
            />

        </div>

    </div>

    {{-- NAVBAR --}}
    <x-navigation.admin-global-navbar
        active="lainnya"
    />

</x-layouts.mobile-app>

@endsection