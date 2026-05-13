@extends('components.layouts.app')

@section('title', 'Dashboard Pasien')

@section('content')

<x-layouts.mobile-app>

    {{-- TOPBAR --}}
    <x-ui.topbar title="Rumah Terapi">

        <x-slot:right>
            <img
                src="https://i.pravatar.cc/100"
                class="h-10 w-10 rounded-full object-cover"
            >
        </x-slot:right>

    </x-ui.topbar>

    <div class="space-y-6 p-4 pb-24">

        {{-- GREETING --}}
        <div>

            <h2 class="text-xl font-bold text-gray-800">
                Halo, Budi 👋
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Semoga sehat selalu hari ini
            </p>

        </div>

        {{-- QUICK BOOKING --}}
        <x-ui.card>

            <div class="space-y-4">

                <div>

                    <h3 class="font-semibold text-gray-800">
                        Booking Terapi
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Buat janji terapi dengan mudah
                    </p>

                </div>

                <x-ui.button>
                    Booking Sekarang
                </x-ui.button>

            </div>

        </x-ui.card>

        {{-- REFERRAL --}}
        <x-ui.card>

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-500">
                        Referral Point
                    </p>

                    <h3 class="mt-1 text-2xl font-bold text-primary">
                        250 Point
                    </h3>

                </div>

                <div class="text-4xl">
                    🎁
                </div>

            </div>

        </x-ui.card>

        {{-- UPCOMING BOOKING --}}
        <div class="space-y-4">

            <x-ui.section-title title="Booking Mendatang" />

            <x-domain.appointment.appointment-card
                patient="Budi"
                service="Akupunktur"
                time="10 Mei 2026 - 10:30"
                status="approved"
            />

        </div>

    </div>

    {{-- NAVBAR --}}
    <x-navigation.guest-navbar
        active="beranda"
    />

</x-layouts.mobile-app>

@endsection