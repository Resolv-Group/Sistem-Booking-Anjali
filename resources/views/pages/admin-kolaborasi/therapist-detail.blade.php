@extends('components.layouts.app')

@section('title', 'Detail Terapis')

@section('content')

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32">

        {{-- TOPBAR GLASSY --}}
        <nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-100/80 px-6 py-4">
            <div class="flex items-center justify-between">

                {{-- Left: Navigation & Context --}}
                <div class="flex items-center gap-4">
                    {{-- Tombol Back/Menu dengan Hitbox Luas --}}
                    <a href="javascript:void(0)" onclick="window.history.back()"
                        class="group flex items-center justify-center w-10 h-10 bg-white border border-slate-100 rounded-xl shadow-sm hover:bg-teal-50 transition-all active:scale-90">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-600" fill="none" stroke="currentColor"
                            stroke-width="3" viewBox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>

                    <div class="flex flex-col">
                        {{-- Nama Cabang/Kolaborasi --}}
                        <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">
                            {{-- {{ $sessions[0]['kolaborasi'] ?? 'Rumah Terapi Anjali' }} --}}
                            ANJALI SADINA MULYO
                        </span>
                        <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none uppercase">
                            Detail Terapis
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

        <div class="px-6 pt-6 space-y-6">

            {{-- PROFILE CARD --}}
            <div
                class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm flex flex-col items-center text-center space-y-4">
                <div class="relative">
                    <div class="w-24 h-24 rounded-[2.2rem] p-1 bg-white border border-slate-100 shadow-lg overflow-hidden">
                        <img src="{{ $fotoUrl }}" class="w-full h-full rounded-[1.8rem] object-cover">
                    </div>
                    <div
                        class="absolute bottom-0 right-0 w-5 h-5 rounded-full border-2 border-white flex items-center justify-center
                    {{ $therapist->status_karyawan === 'Aktif' ? 'bg-emerald-500' : 'bg-slate-300' }}">
                    </div>
                </div>

                <div class="space-y-1">
                    <h2 class="text-xl font-black text-slate-800 leading-tight">{{ $therapist->nama_karyawan }}</h2>
                    <div class="flex items-center justify-center gap-2">
                        <span
                            class="px-2.5 py-0.5 bg-teal-50 text-teal-600 text-[9px] font-black uppercase tracking-widest rounded-md border border-teal-100">
                            {{ $therapist->peran }}
                        </span>
                        <span
                            class="px-2.5 py-0.5 bg-slate-50 text-slate-500 text-[9px] font-black uppercase tracking-widest rounded-md border border-slate-100">
                            {{ $therapist->status_karyawan }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- PERSONAL DATA --}}
            <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em] border-b border-slate-50 pb-2">
                    Informasi Pribadi</h3>

                <div class="grid grid-cols-2 gap-y-4 gap-x-2">
                    <div class="space-y-1">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">NIK</span>
                        <p class="text-xs font-bold text-slate-700">{{ $therapist->nik ?: '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Jenis Kelamin</span>
                        <p class="text-xs font-bold text-slate-700">
                            {{ $therapist->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nomor Telepon</span>
                        <p class="text-xs font-bold text-slate-700">{{ $therapist->no_telp }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Email</span>
                        <p class="text-xs font-bold text-slate-700 break-all">{{ $therapist->email ?: '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Lahir</span>
                        <p class="text-xs font-bold text-slate-700">
                            {{ $therapist->tanggal_lahir ? $therapist->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Bergabung</span>
                        <p class="text-xs font-bold text-slate-700">
                            {{ $therapist->tanggal_bergabung ? $therapist->tanggal_bergabung->translatedFormat('d F Y') : '-' }}
                        </p>
                    </div>
                    <div class="col-span-2 space-y-1">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Alamat</span>
                        <p class="text-xs font-medium text-slate-600 leading-relaxed">{{ $therapist->alamat ?: '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- SCHEDULE --}}
            <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em] border-b border-slate-50 pb-2">
                    Jadwal Operasional</h3>

                <div class="space-y-3">
                    @forelse($schedules as $sched)
                        <div
                            class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100/50">
                            <span class="text-xs font-bold text-slate-700">{{ $dayNames[$sched->hari] ?? '-' }}</span>
                            <div class="flex items-center gap-3">
                                <span
                                    class="px-2 py-0.5 bg-teal-50 text-teal-600 text-[9px] font-bold rounded border border-teal-100">
                                    {{ substr($sched->waktu_mulai, 0, 5) }}
                                </span>
                                <span class="text-[10px] font-medium text-slate-400">Kuota:
                                    <strong>{{ $sched->kuota }}</strong></span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs font-bold text-slate-400 text-center py-4">Jadwal operasional belum diatur.</p>
                    @endforelse
                </div>
            </div>

            {{-- SERVICES --}}
            <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em] border-b border-slate-50 pb-2">
                    Layanan Spesialisasi</h3>

                <div class="space-y-2">
                    @forelse($services as $svc)
                        <div
                            class="flex items-center justify-between p-3 bg-teal-50/20 rounded-xl border border-teal-100/30">
                            <div class="space-y-0.5">
                                <h4 class="text-xs font-bold text-slate-800">{{ $svc->nama }}</h4>
                                <p class="text-[10px] text-slate-400">{{ $svc->deskripsi ?: 'Layanan profesional.' }}</p>
                            </div>
                            <span
                                class="text-xs font-black text-teal-800 shrink-0">Rp{{ number_format($svc->base_harga, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-xs font-bold text-slate-400 text-center py-4">Belum ada layanan yang ditugaskan.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- BOTTOM NAVBAR --}}
        <x-navigation.admin-cabang-navbar active="terapis" />

    </x-layouts.mobile-app>

@endsection
