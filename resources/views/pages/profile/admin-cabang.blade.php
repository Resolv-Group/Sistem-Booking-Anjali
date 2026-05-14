@extends('components.layouts.app')

@section('title', 'Profile')

@section('content')

<x-layouts.mobile-app class="bg-gradient-to-b from-[#e8f4f2] to-white min-h-screen">

    {{-- TOPBAR: Minimalist & Transparent --}}
    <x-ui.topbar title="Profile" class="bg-transparent border-none">
        <x-slot:left>
            <button class="p-2 text-teal-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
        </x-slot:left>
        <x-slot:right>
            <button class="p-2 text-teal-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </button>
        </x-slot:right>
    </x-ui.topbar>

    <div class="p-4 pb-32 flex flex-col items-center">

        {{-- AVATAR SECTION --}}
        <div class="relative mb-6 mt-4">
            <div class="absolute inset-0 bg-teal-200 rounded-full blur-2xl opacity-30 animate-pulse"></div>
            <div class="relative h-32 w-32 rounded-[2.5rem] p-1 bg-white shadow-xl border border-white/50">
                <img
                    src="https://i.pravatar.cc/150?u=therapist1"
                    class="h-full w-full rounded-[2.2rem] object-cover"
                >
            </div>
        </div>

        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Julianne Vance</h2>
            <div class="mt-2 flex items-center justify-center gap-2">
                <span class="px-4 py-1 bg-teal-100 text-teal-700 text-[10px] font-black uppercase tracking-widest rounded-full">
                    Therapist Pro
                </span>
                <span class="text-[11px] font-bold text-slate-400">#8829-TH</span>
            </div>
        </div>

        {{-- TODAY STATS: Glassy Style --}}
        <div class="grid grid-cols-2 gap-4 w-full mb-8">
            <div class="bg-white/60 backdrop-blur-lg border border-white p-4 rounded-3xl shadow-sm text-center">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Jadwal Hari Ini</p>
                <h3 class="mt-1 text-2xl font-black text-teal-600">8</h3>
            </div>
            <div class="bg-white/60 backdrop-blur-lg border border-white p-4 rounded-3xl shadow-sm text-center">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pasien Hari Ini</p>
                <h3 class="mt-1 text-2xl font-black text-teal-600">14</h3>
            </div>
        </div>

        {{-- MENU LIST: Following the screenshot style --}}
        <div class="w-full space-y-3">
            @php
                $menus = [
                    ['icon' => 'user', 'title' => 'Personal Information', 'sub' => 'Name, Contact, Address'],
                    ['icon' => 'clipboard-list', 'title' => 'Medical Records', 'sub' => 'History, Notes from Dr. Thorne'],
                    ['icon' => 'shield-check', 'title' => 'Insurance & Benefits', 'sub' => 'Coverage details'],
                    ['icon' => 'credit-card', 'title' => 'Payment Methods', 'sub' => 'Saved cards, BCA info'],
                ];
            @endphp

            @foreach($menus as $menu)
                <x-ui.button class="w-full group flex items-center justify-between p-4 bg-white/70 backdrop-blur-md border border-white rounded-3xl shadow-sm active:scale-[0.98] transition-all">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center">
                            <i class="lucide-{{ $menu['icon'] }} w-5 h-5"></i> {{-- Assuming you use Lucide icons --}}
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-bold text-slate-800 leading-none">{{ $menu['title'] }}</p>
                            <p class="text-[11px] font-medium text-slate-400 mt-1">{{ $menu['sub'] }}</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-slate-300 group-hover:text-teal-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </x-ui.button>
            @endforeach
        </div>

        {{-- SIGN OUT BUTTON --}}
        <form action="{{ route('auth.logout') }}" method="POST">
            @csrf

            <x-ui.button type="submit" class="w-full group flex items-center mt-10 p-4 bg-teal-600 text-slate-600 text-sm font-black uppercase tracking-[0.2em] rounded-2xl active:scale-95 transition-all">
                Keluar
            </x-ui.button>
            
        </form>

        {{-- BRANDING --}}
        <div class="mt-6 text-center">
            <p class="text-[10px] font-black text-teal-600/40 uppercase tracking-widest">Anjali Rumah Terapi</p>
            <p class="text-[8px] font-bold text-slate-300 uppercase mt-1 tracking-tighter">v1.0.0</p>
        </div>

    </div>

    <x-navigation.therapist-navbar active="profile" />

</x-layouts.mobile-app>

<style>
    /* Custom spacing to match MJA Vibe */
    body {
        -webkit-font-smoothing: antialiased;
    }
</style>

@endsection