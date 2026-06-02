@extends('components.layouts.app')

@section('title', 'Profile')

@section('content')



    <x-layouts.mobile-app class="bg-gradient-to-b from-[#e8f4f2] via-white to-white min-h-screen">

        {{-- TOPBAR: Transparan & Floating --}}
        <x-ui.topbar title="Profile" class="bg-transparent border-none">
            <x-slot:left>
                <button class="p-2 text-teal-800 active:scale-90 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            </x-slot:left>
            <x-slot:right>
                <button class="p-2 text-teal-700 active:rotate-90 transition-transform duration-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </x-slot:right>
        </x-ui.topbar>

        <div class="p-5 pb-32 flex flex-col items-center">

            {{-- AVATAR SECTION: Enhanced Glow --}}
            <div class="relative mb-6 mt-4">
                <div class="absolute inset-0 bg-teal-300 rounded-full blur-3xl opacity-20 animate-pulse"></div>
                <div
                    class="relative h-32 w-32 rounded-[2.8rem] p-1.5 bg-white shadow-2xl border border-white/50 overflow-hidden">
                    <img src="{{ asset(Auth::user()->profile_picture ?? 'assets/images/default-profile.png') }}"
                        class="h-full w-full rounded-[2.4rem] object-cover hover:scale-110 transition-transform duration-700">
                </div>
            </div>

            <div class="text-center mb-8">
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">{{ Auth::user()->name ?? 'User' }}</h2>
                <div class="mt-2 flex items-center justify-center gap-2">
                    <span
                        class="px-4 py-1 bg-teal-100/80 text-teal-700 text-[10px] font-black uppercase tracking-[0.1em] rounded-full border border-teal-200/50">
                        {{ Auth::user()->role ?? 'User' }}
                    </span>
                    <span class="text-[11px] font-bold text-slate-400">#{{ Auth::user()->id ?? '00' }}</span>
                </div>
            </div>

            {{-- TODAY STATS: More Glassy --}}
            <div class="grid grid-cols-2 gap-4 w-full mb-8">
                <div
                    class="bg-white/40 backdrop-blur-xl border border-white/60 p-5 rounded-[2rem] shadow-sm text-center group hover:bg-white/60 transition-colors duration-300">
                    <p
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-teal-600 transition-colors">
                        Jadwal Hari Ini</p>
                    <h3 class="mt-1 text-3xl font-black text-slate-800">8</h3>
                </div>
                <div
                    class="bg-white/40 backdrop-blur-xl border border-white/60 p-5 rounded-[2rem] shadow-sm text-center group hover:bg-white/60 transition-colors duration-300">
                    <p
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-teal-600 transition-colors">
                        Pasien Hari Ini</p>
                    <h3 class="mt-1 text-3xl font-black text-slate-800">14</h3>
                </div>
            </div>

            {{-- MENU LIST: Refined Interactions --}}
            <div class="w-full space-y-3">
                @php
                    $menus = [
                        ['icon' => 'user', 'title' => 'Personal Information', 'sub' => 'Name, Contact, Address'],
                        [
                            'icon' => 'clipboard-list',
                            'title' => 'Medical Records',
                            'sub' => 'History, Notes from Dr. Thorne',
                        ],
                        ['icon' => 'shield-check', 'title' => 'Insurance & Benefits', 'sub' => 'Coverage details'],
                        ['icon' => 'credit-card', 'title' => 'Payment Methods', 'sub' => 'Saved cards, BCA info'],
                    ];
                @endphp

                @foreach ($menus as $menu)
                    <button
                        class="w-full group flex items-center justify-between p-4 bg-white/60 backdrop-blur-md border border-white/80 rounded-[1.8rem] shadow-sm hover:bg-white hover:shadow-md active:scale-[0.97] transition-all duration-300">
                        <div class="flex items-center gap-4 text-left">
                            <div
                                class="h-12 w-12 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center group-hover:bg-teal-50 group-hover:text-teal-600 transition-colors duration-300">
                                <i class="lucide-{{ $menu['icon'] }} w-5 h-5"></i>
                            </div>
                            <div>
                                <p
                                    class="text-sm font-bold text-slate-700 leading-none group-hover:text-slate-900 transition-colors">
                                    {{ $menu['title'] }}</p>
                                <p class="text-[11px] font-medium text-slate-400 mt-1.5">{{ $menu['sub'] }}</p>
                            </div>
                        </div>
                        <div
                            class="flex items-center text-slate-300 group-hover:text-teal-500 group-hover:translate-x-1 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </button>
                @endforeach
            </div>

            {{-- SIGN OUT BUTTON: Neutral but distinct --}}
            <form action="{{ route('auth.logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center mt-10 p-4 bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-500 text-xs font-black uppercase tracking-[0.2em] rounded-[1.5rem] active:scale-95 transition-all duration-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar Aplikasi
                </button>
            </form>

            {{-- BRANDING --}}
            <div class="mt-8 text-center opacity-40">
                <p class="text-[10px] font-black text-teal-800 uppercase tracking-[0.3em]">Anjali Rumah Terapi</p>
                <p class="text-[8px] font-bold text-slate-400 uppercase mt-1 tracking-tighter">v1.0.0</p>
            </div>

        </div>

        <x-navigation.patient-navbar active="profile" />

    </x-layouts.mobile-app>

    <style>
        /* Mengoptimalkan render font di perangkat mobile */
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        /* Custom scrollbar untuk menu list jika konten meluap */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>

@endsection
