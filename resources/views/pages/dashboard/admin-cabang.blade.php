@extends('components.layouts.app')

@section('title', 'Dashboard Admin Cabang')

@section('content')

<x-layouts.mobile-app>

    <x-ui.topbar title="Admin Cabang">

        <x-slot:right>
            <img
                src="https://i.pravatar.cc/100"
                class="h-10 w-10 rounded-full object-cover"
            >
        </x-slot:right>

    </x-ui.topbar>

    <div class="space-y-6 p-4 pb-24">

        {{-- STATS --}}
        <div class="grid grid-cols-2 gap-4">

            <x-ui.card>

                <div>

                    <p class="text-sm text-gray-500">
                        Booking Hari Ini
                    </p>

                    <h3 class="mt-2 text-2xl font-bold text-primary">
                        32
                    </h3>

                </div>

            </x-ui.card>

            <x-ui.card>

                <div>

                    <p class="text-sm text-gray-500">
                        Terapis Aktif
                    </p>

                    <h3 class="mt-2 text-2xl font-bold text-primary">
                        7
                    </h3>

                </div>

            </x-ui.card>

        </div>

        {{-- PENDING APPROVAL --}}
        <div class="space-y-4">

            <x-ui.section-title title="Menunggu Approval" />

            <x-domain.appointment.appointment-card
                patient="Michael"
                service="Massage"
                time="13:00"
                status="pending"
            />

            <x-domain.appointment.appointment-card
                patient="Sarah"
                service="Akupunktur"
                time="15:00"
                status="pending"
            />

        </div>

    </div>

    <x-navigation.admin-cabang-navbar
        active="dashboard"
    />

</x-layouts.mobile-app>

@endsection