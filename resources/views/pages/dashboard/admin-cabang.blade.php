@extends('components.layouts.app')

@section('title', 'Dashboard Admin Cabang')

@section('content')

<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32" x-data="{
    showRejectModal: false,
    rejectBookingId: null,
    rejectReason: ''
}">

<nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-100/80 px-6 py-4">
            <div class="flex items-center justify-between">

                {{-- Left: Navigation & Context --}}
                <div class="flex items-center gap-4">
                    {{-- Tombol Back/Menu dengan Hitbox Luas --}}
                    <div class="flex flex-col">
                        {{-- Nama Cabang/Kolaborasi --}}
                        <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">
                            {{ auth()->user()->karyawan->kolaborasi->nama_kolaborasi ?? 'Rumah Terapi Anjali' }}
                        </span>
                        <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none uppercase">
                            Dashboard
                        </h1>
                    </div>
                </div>

                {{-- Right: Profile with Status Indicator --}}
                <div class="flex items-center gap-3">
                    <div class="relative">
                        {{-- Avatar dengan Ring Status --}}
                        <div class="w-10 h-10 rounded-xl border-2 border-white shadow-md p-0.5">
                            <img src="{{ asset('images/logo_anjali.jpg') }}"
                                class="w-full h-full rounded-[10px] object-cover bg-white">
                        </div>
                    </div>
                </div>

            </div>
        </nav>


    <div class="space-y-6 p-4">

        {{-- SUCCESS/ERROR NOTIFICATIONS --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:leave="transition ease-in duration-300"
                class="bg-teal-600 text-white rounded-2xl p-4 text-xs font-black uppercase tracking-widest text-center shadow-lg shadow-teal-700/20">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:leave="transition ease-in duration-300"
                class="bg-rose-500 text-white rounded-2xl p-4 text-xs font-black uppercase tracking-widest text-center shadow-lg shadow-rose-600/20">
                {{ session('error') }}
            </div>
        @endif

        {{-- Welcome Card --}}
        <div class="bg-teal-900 rounded-[2rem] p-6 text-white shadow-xl shadow-teal-900/10 relative overflow-hidden">
            <div class="relative z-10 space-y-1">
                <span class="text-[10px] font-black text-teal-300 uppercase tracking-[0.2em] leading-none">Selamat Datang</span>
                <h2 class="text-xl font-bold tracking-tight">{{ auth()->user()->name }}</h2>
                <p class="text-[11px] text-teal-100 font-medium">Administrator Klinik Anjali</p>
            </div>
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-teal-800 rounded-full opacity-40"></div>
            <div class="absolute -right-2 -top-10 w-20 h-20 bg-teal-700 rounded-full opacity-20"></div>
        </div>

        {{-- STATS GRID --}}
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-2">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider leading-tight">Booking Hari Ini</span>
                <span class="text-2xl font-black text-teal-800 tracking-tight">{{ $bookingHariIni }}</span>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-2">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider leading-tight">Terapis Aktif</span>
                <span class="text-2xl font-black text-teal-800 tracking-tight">{{ $terapisAktif }}</span>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-2">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider leading-tight">Total Layanan</span>
                <span class="text-2xl font-black text-teal-800 tracking-tight">{{ $totalLayanan }}</span>
            </div>
        </div>

        {{-- PENDING APPROVAL --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.25em]">Menunggu Approval</h3>
                <span class="px-2 py-0.5 bg-orange-50 text-orange-600 text-[9px] font-bold rounded-full border border-orange-100">{{ $pendingBookings->count() }} Janji</span>
            </div>

            <div class="space-y-3">
                @forelse($pendingBookings as $booking)
                    <div class="bg-white rounded-[2rem] p-5 border border-slate-100 shadow-sm space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-black text-slate-800 leading-none">{{ $booking['nama'] }}</h4>
                                    <span class="px-1.5 py-0.5 bg-slate-50 text-slate-500 text-[8px] font-bold uppercase rounded border border-slate-200 tracking-tight">{{ $booking['id'] }}</span>
                                </div>
                                <p class="text-[11px] font-bold text-teal-700 uppercase tracking-wide mt-1.5">{{ $booking['layanan'] }}</p>
                                <div class="flex items-center gap-1.5 mt-2">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="text-[10px] font-medium text-slate-500">Terapis: <strong>{{ $booking['terapis'] }}</strong></span>
                                </div>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-[10px] font-medium text-slate-500">{{ $booking['waktu'] }}</span>
                                </div>
                            </div>

                            @if($booking['bukti_transfer_url'])
                                <a href="{{ $booking['bukti_transfer_url'] }}" target="_blank" class="shrink-0 flex flex-col items-center gap-1 p-2 bg-slate-50 rounded-xl border border-slate-100 hover:bg-teal-50 active:scale-95 transition-all">
                                    <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-[8px] font-black uppercase text-slate-400">Bukti TF</span>
                                </a>
                            @else
                                <div class="shrink-0 flex flex-col items-center gap-1 p-2 bg-rose-50/50 rounded-xl border border-rose-100/50">
                                    <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span class="text-[8px] font-black uppercase text-rose-400">Belum TF</span>
                                </div>
                            @endif
                        </div>

                        {{-- ACTIONS --}}
                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-50">
                            <form action="{{ route('admin-cabang.booking.accept', $booking['id_raw']) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-3 bg-teal-800 text-white rounded-xl text-[10px] font-black uppercase tracking-widest active:scale-95 transition-all shadow-sm">Setujui</button>
                            </form>

                            <button @click="rejectBookingId = {{ $booking['id_raw'] }}; rejectReason = ''; showRejectModal = true;"
                                class="w-full py-3 bg-rose-50 text-rose-600 rounded-xl text-[10px] font-black uppercase tracking-widest active:scale-95 transition-all border border-rose-100 shadow-sm">
                                Tolak
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 space-y-3">
                        <div class="w-16 h-16 mx-auto bg-slate-100 rounded-2xl flex items-center justify-center text-slate-300">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-400">Tidak ada janji menunggu approval.</p>
                        <p class="text-xs text-slate-300">Semua booking terverifikasi dengan rapi.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- REJECT CONFIRMATION MODAL --}}
    <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-[999] flex items-center justify-center p-6"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showRejectModal = false"></div>

        {{-- Modal --}}
        <div class="relative bg-white rounded-[2rem] p-7 max-w-sm w-full shadow-2xl space-y-6"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100">

            <div class="text-center space-y-2">
                <div class="w-14 h-14 mx-auto bg-rose-50 rounded-2xl flex items-center justify-center text-rose-500">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-black text-slate-800">Tolak Booking</h3>
                <p class="text-xs text-slate-400">Silakan masukkan alasan penolakan untuk dikirim ke pasien.</p>
            </div>

            <form :action="'/admin-cabang/booking/' + rejectBookingId + '/reject'" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Alasan Penolakan</label>
                    <textarea name="alasan_status" x-model="rejectReason" required rows="3"
                        class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-xs font-semibold text-slate-700 focus:ring-4 focus:ring-rose-500/10 focus:bg-white transition-all outline-none resize-none shadow-inner"
                        placeholder="Contoh: Jadwal Terapis Berbenturan / Alasan Lain..."></textarea>
                </div>

                <div class="space-y-2 pt-2">
                    <button type="submit" :disabled="!rejectReason.trim()"
                        class="w-full py-4 bg-rose-500 text-white rounded-xl text-xs font-black uppercase tracking-widest active:scale-95 transition-all shadow-lg shadow-rose-500/20 disabled:opacity-50 disabled:active:scale-100">
                        Ya, Tolak Booking
                    </button>
                    <button type="button" @click="showRejectModal = false"
                        class="w-full py-3 bg-slate-100 text-slate-500 rounded-xl text-xs font-black uppercase tracking-widest active:scale-95 transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <x-navigation.admin-cabang-navbar active="dashboard" />

</x-layouts.mobile-app>

@endsection