@extends('components.layouts.app')

@section('title', 'Dashboard Terapis')

@section('content')

<x-layouts.mobile-app>

    <x-ui.topbar title="Dashboard Terapis">

        <x-slot:right>
            <img
                src="https://i.pravatar.cc/100"
                class="h-10 w-10 rounded-full object-cover"
            >
        </x-slot:right>

    </x-ui.topbar>

    <div class="space-y-6 p-4 pb-24">

        {{-- TODAY STATS --}}
        <div class="grid grid-cols-2 gap-4">

            <x-ui.card>

                <div>

                    <p class="text-sm text-gray-500">
                        Jadwal Hari Ini
                    </p>

                    <h3 class="mt-2 text-2xl font-bold text-primary">
                        8
                    </h3>

                </div>

            </x-ui.card>

            <x-ui.card>

                <div>

                    <p class="text-sm text-gray-500">
                        Pasien Hari Ini
                    </p>

                    <h3 class="mt-2 text-2xl font-bold text-primary">
                        14
                    </h3>

                </div>

            </x-ui.card>

        </div>

        {{-- NEXT SESSION --}}
        <div class="space-y-4">

            <x-ui.section-title title="Sesi Berikutnya" />

            <x-domain.appointment.appointment-card
                patient="Budi"
                service="Bekam"
                time="10:30 AM"
                status="approved"
            />

        </div>

    </div>

    <x-navigation.therapist-navbar
        active="dashboard"
    />

</x-layouts.mobile-app>

@endsection