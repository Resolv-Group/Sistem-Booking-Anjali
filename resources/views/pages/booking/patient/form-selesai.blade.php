@extends('components.layouts.app')

@section('title', 'Pendaftaran Berhasil')

@section('content')

<x-layouts.mobile-app class="bg-slate-50 min-h-screen">

    {{-- 1. TOPBAR --}}
    <x-ui.topbar title="Rumah Terapi Anjali">
        <x-slot:left>
            <a href="{{ route('patient.booking.index') }}" class="p-2 -ml-2 text-slate-400 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
        </x-slot:left>
        <x-slot:right>
            <img src="https://i.pravatar.cc/100?u=anjali" class="w-9 h-9 rounded-xl border border-slate-200 object-cover">
        </x-slot:right>
    </x-ui.topbar>

    <div class="px-6 pt-10 pb-32 flex flex-col items-center">

        {{-- 2. SUCCESS ICON --}}
        <div class="relative mb-8">
            <div class="absolute inset-0 bg-teal-200 rounded-full blur-3xl opacity-30 animate-pulse"></div>
            <div class="relative w-24 h-24 bg-teal-600 rounded-full flex items-center justify-center shadow-xl shadow-teal-900/20 border-4 border-white">
                <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        {{-- 3. MAIN TITLE & MESSAGE --}}
        <div class="text-center space-y-3 mb-10">
            <h2 class="text-3xl font-semibold text-teal-900 leading-tight">Jadwal Anda <br> Sudah Terdaftar</h2>
            <p class="text-base text-slate-500 font-medium leading-relaxed px-4">
                Terima kasih telah mempercayakan Klinik Anjali. Tim kami akan segera meninjau dan mengonfirmasi bukti transfer Anda.
            </p>
        </div>

        {{-- 4. SUMMARY CARD (WHITE) --}}
        <div class="w-full bg-white rounded-[2rem] border border-slate-200 shadow-sm p-6 space-y-6 mb-8">
            {{-- Therapist Info --}}
            <div class="flex flex-col items-center text-center">
                <p class="text-[10px] font-semibold text-teal-600 uppercase tracking-[0.2em] mb-4">Pendaftaran Sukses</p>
                <div class="relative mb-3">
                    <div class="w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center border border-teal-100 shadow-inner">
                        <svg class="w-10 h-10 text-teal-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                </div>
                <h4 class="text-lg font-semibold text-slate-800 leading-none">Menunggu Persetujuan</h4>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mt-2">Rumah Terapi Anjali</p>
            </div>

            {{-- Compact Detail Box --}}
            <div class="bg-slate-50 rounded-2xl p-5 space-y-4">
                <div class="flex gap-4">
                    <svg class="w-5 h-5 text-teal-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <div>
                        <p class="text-[9px] font-semibold text-slate-400 uppercase tracking-widest mb-0.5">Status Booking</p>
                        <p class="text-sm font-semibold text-teal-600 leading-tight">Menunggu Verifikasi Pembayaran</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. NEXT STEPS BOX (LIGHT GRAY) --}}
        <div class="w-full bg-slate-100 rounded-2xl p-6 space-y-5 mb-10">
            <h3 class="text-sm font-bold text-slate-700">Langkah Selanjutnya</h3>
            
            <div class="flex gap-4">
                <div class="w-8 h-8 shrink-0 bg-white rounded-lg flex items-center justify-center text-teal-600 shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-sm font-medium text-slate-500 leading-relaxed">
                    Silakan cek halaman dashboard Anda secara berkala untuk melihat perubahan status pemesanan.
                </p>
            </div>

            <div class="flex gap-4">
                <div class="w-8 h-8 shrink-0 bg-white rounded-lg flex items-center justify-center text-teal-600 shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <p class="text-sm font-medium text-slate-500 leading-relaxed">
                    Kami akan segera mengirimkan konfirmasi pendaftaran setelah bukti transfer Anda disetujui.
                </p>
            </div>
        </div>

        {{-- 6. ACTION BUTTONS --}}
        <div class="w-full space-y-4">
            <button onclick="window.location.href='{{ route('patient.dashboard') }}'" class="w-full py-5 bg-teal-800 text-white rounded-2xl text-base font-semibold uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all cursor-pointer">
                Lihat Dashboard Saya
            </button>
            <button onclick="window.location.href='{{ route('landing') }}'" class="w-full py-5 bg-white border border-slate-200 text-slate-500 rounded-2xl text-base font-semibold uppercase tracking-[0.2em] hover:bg-slate-50 active:scale-95 transition-all cursor-pointer">
                Kembali Ke Beranda
            </button>
        </div>

    </div>

    {{-- BOTTOM NAVBAR --}}
    <x-navigation.patient-navbar active="booking" />

</x-layouts.mobile-app>

@endsection