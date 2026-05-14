@extends('components.layouts.app')

@section('title', 'Dashboard Admin Global')

@section('content')

<x-layouts.mobile-app>

    {{-- TOPBAR --}}
    <x-ui.topbar title="Rumah Terapi Anjali">

        <x-slot:left>
            <button class="text-primary text-xl font-bold">
                ☰
            </button>
        </x-slot:left>

        <x-slot:right>
            <img
                src="https://i.pravatar.cc/100"
                class="h-10 w-10 rounded-full object-cover"
            >
        </x-slot:right>

    </x-ui.topbar>

    {{-- CONTENT --}}
    <div class="space-y-6 p-4 pb-24">

        {{-- GREETING --}}
        <div>
            <h2 class="text-xl font-bold text-gray-800">
                Selamat Datang 👋
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Kelola seluruh rumah terapi dengan mudah
            </p>
        </div>

        {{-- STATS --}}
        <div class="grid grid-cols-2 gap-4">

            <x-ui.card>
                <div class="space-y-2">

                    <div class="text-sm text-gray-500">
                        Total Cabang
                    </div>

                    <div class="text-2xl font-bold text-primary">
                        12
                    </div>

                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="space-y-2">

                    <div class="text-sm text-gray-500">
                        Total Terapis
                    </div>

                    <div class="text-2xl font-bold text-primary">
                        38
                    </div>

                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="space-y-2">

                    <div class="text-sm text-gray-500">
                        Booking Hari Ini
                    </div>

                    <div class="text-2xl font-bold text-primary">
                        124
                    </div>

                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="space-y-2">

                    <div class="text-sm text-gray-500">
                        Pasien Aktif
                    </div>

                    <div class="text-2xl font-bold text-primary">
                        542
                    </div>

                </div>
            </x-ui.card>

        </div>

        {{-- SEARCH --}}
        <x-ui.search-input />

        {{-- CABANG SECTION --}}
        <div class="space-y-4">

            <x-ui.section-title title="Cabang Aktif" />

            <x-ui.card>

                <div class="flex items-start justify-between">

                    <div>

                        <h3 class="font-semibold text-gray-800">
                            Cabang Surabaya
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            14 Terapis Aktif
                        </p>

                    </div>

                    <x-ui.badge variant="success">
                        Aktif
                    </x-ui.badge>

                </div>

            </x-ui.card>

            <x-ui.card>

                <div class="flex items-start justify-between">

                    <div>

                        <h3 class="font-semibold text-gray-800">
                            Cabang Jakarta
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            10 Terapis Aktif
                        </p>

                    </div>

                    <x-ui.badge variant="success">
                        Aktif
                    </x-ui.badge>

                </div>

            </x-ui.card>

        </div>

        {{-- THERAPIST SECTION --}}
        <div class="space-y-4">

            <x-ui.section-title title="Terapis Terbaru" />

            <x-domain.therapist.therapist-card
                name="Dr. Sarah"
                specialist="Akupunktur"
            />

            <x-domain.therapist.therapist-card
                name="Dr. Michael"
                specialist="Bekam & Massage"
            />

        </div>

        {{-- QUICK ACTION --}}
        <div class="space-y-4">

            <x-ui.section-title title="Quick Action" />

            <div class="grid grid-cols-2 gap-4">

                <x-ui.button>
                    Tambah Cabang
                </x-ui.button>

                <x-ui.button variant="secondary">
                    Kelola User
                </x-ui.button>

                <form action="{{ route('auth.logout') }}" method="POST">
                    @csrf

                    <x-ui.button type="submit" class="w-full group flex items-center mt-10 p-4 bg-teal-600 text-slate-600 text-sm font-black uppercase tracking-[0.2em] rounded-2xl active:scale-95 transition-all">
                        Keluar
                    </x-ui.button>

                </form>


            </div>

        </div>

    </div>

    {{-- BOTTOM NAVBAR --}}
    <x-ui.navbar-bottom role="admin global" />

</x-layouts.mobile-app>

@endsection