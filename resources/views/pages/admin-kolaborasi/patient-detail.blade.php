@extends('components.layouts.app')

@section('title', 'Detail Pasien')

@section('content')

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32" x-data="{ activeTab: 'personal' }">

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
                            {{ auth()->user()->karyawan->kolaborasi->nama_kolaborasi ?? 'Rumah Terapi Anjali' }}
                        </span>
                        <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none uppercase">
                            Detail Pasien
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

            @if(isset($groupNav))
                {{-- Group Patient Navigation --}}
                <div class="bg-white rounded-[2rem] p-5 border border-slate-100 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-50 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-pulse"></span>
                            <span class="text-xs font-black text-slate-700 uppercase tracking-wider">Anggota Grup ({{ $groupNav['current_index'] + 1 }} / {{ $groupNav['total'] }})</span>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        @if($groupNav['prev_id'])
                            <a href="{{ route('admin-cabang.patient.detail', ['id' => $groupNav['prev_id'], 'group' => $groupNav['query']]) }}"
                                class="flex-1 flex items-center justify-center gap-2 py-3.5 bg-slate-50 text-slate-600 rounded-xl text-xs font-bold uppercase tracking-widest border border-slate-200 active:scale-95 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                                Seb
                            </a>
                        @else
                            <button disabled class="flex-1 flex items-center justify-center gap-2 py-3.5 bg-slate-50 text-slate-300 rounded-xl text-xs font-bold uppercase tracking-widest border border-slate-100 opacity-50 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                                Seb
                            </button>
                        @endif

                        @if($groupNav['next_id'])
                            <a href="{{ route('admin-cabang.patient.detail', ['id' => $groupNav['next_id'], 'group' => $groupNav['query']]) }}"
                                class="flex-1 flex items-center justify-center gap-2 py-3.5 bg-teal-50 text-teal-700 rounded-xl text-xs font-bold uppercase tracking-widest border border-teal-200 active:scale-95 transition-all">
                                Sel
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @else
                            <button disabled class="flex-1 flex items-center justify-center gap-2 py-3.5 bg-slate-50 text-slate-300 rounded-xl text-xs font-bold uppercase tracking-widest border border-slate-100 opacity-50 cursor-not-allowed">
                                Sel
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            {{-- PROFILE SUMMARY CARD --}}
            <div
                class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm flex flex-col items-center text-center space-y-4">
                <div class="relative">
                    <div class="w-24 h-24 rounded-[2.2rem] p-1 bg-white border border-slate-100 shadow-lg overflow-hidden">
                        <img src="{{ $fotoUrl }}" class="w-full h-full rounded-[1.8rem] object-cover">
                    </div>
                </div>

                <div class="space-y-1">
                    <h2 class="text-xl font-black text-slate-800 leading-tight">{{ $patient->nama_pasien }}</h2>
                    <div class="flex items-center justify-center gap-2">
                        <span
                            class="px-2.5 py-0.5 bg-teal-50 text-teal-600 text-[9px] font-black uppercase tracking-widest rounded-md border border-teal-100">
                            {{ $patient->membership_tier ?: 'Basic' }}
                        </span>
                        <span
                            class="px-2.5 py-0.5 bg-slate-50 text-slate-500 text-[9px] font-black uppercase tracking-widest rounded-md border border-slate-100">
                            {{ $patient->pasien_public_id }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- TABS NAVIGATION --}}
            <div class="bg-white p-1.5 rounded-2xl border border-slate-100 shadow-sm grid grid-cols-3 gap-1">
                <button @click="activeTab = 'personal'"
                    :class="activeTab === 'personal' ? 'bg-teal-800 text-white shadow-md' :
                        'text-slate-400 hover:text-slate-700'"
                    class="py-3 text-[10px] font-black uppercase tracking-wider rounded-xl transition-all duration-300">
                    Pribadi
                </button>
                <button @click="activeTab = 'bookings'"
                    :class="activeTab === 'bookings' ? 'bg-teal-800 text-white shadow-md' :
                        'text-slate-400 hover:text-slate-700'"
                    class="py-3 text-[10px] font-black uppercase tracking-wider rounded-xl transition-all duration-300">
                    Booking
                </button>
                <button @click="activeTab = 'medical'"
                    :class="activeTab === 'medical' ? 'bg-teal-800 text-white shadow-md' :
                        'text-slate-400 hover:text-slate-700'"
                    class="py-3 text-[10px] font-black uppercase tracking-wider rounded-xl transition-all duration-300">
                    Medis
                </button>
            </div>

            {{-- TAB CONTENT: PERSONAL DATA --}}
            <div x-show="activeTab === 'personal'" x-transition class="space-y-6">
                <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm space-y-4">
                    <h3
                        class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em] border-b border-slate-50 pb-2">
                        Informasi Kontak</h3>

                    <div class="grid grid-cols-2 gap-y-4 gap-x-2">
                        <div class="space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">NIK</span>
                            <p class="text-xs font-bold text-slate-700">{{ $patient->nik ?: '-' }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Jenis Kelamin</span>
                            <p class="text-xs font-bold text-slate-700">
                                {{ $patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nomor Telepon</span>
                            <p class="text-xs font-bold text-slate-700">{{ $patient->no_telp }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Email</span>
                            <p class="text-xs font-bold text-slate-700 break-all">{{ $patient->email ?: '-' }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Lahir</span>
                            <p class="text-xs font-bold text-slate-700">
                                {{ $patient->tanggal_lahir ? $patient->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Umur</span>
                            <p class="text-xs font-bold text-slate-700">
                                {{ $patient->age ? $patient->age . ' Tahun' : '-' }}</p>
                        </div>
                        <div class="col-span-2 space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Alamat</span>
                            <p class="text-xs font-medium text-slate-600 leading-relaxed">{{ $patient->alamat ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm space-y-4">
                    <h3
                        class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em] border-b border-slate-50 pb-2">
                        Kesehatan & Membership</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Golongan Darah</span>
                            <p class="text-xs font-bold text-slate-700">{{ $patient->golongan_darah ?: '-' }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Referral Code</span>
                            <p class="text-xs font-bold text-slate-700">{{ $patient->kode_referral ?: '-' }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tinggi Badan</span>
                            <p class="text-xs font-bold text-slate-700">
                                {{ $patient->tinggi_badan ? $patient->tinggi_badan . ' cm' : '-' }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Poin Referral</span>
                            <p class="text-xs font-bold text-slate-700">{{ $patient->poin_referral ?: '0' }} Poin</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Berat Badan</span>
                            <p class="text-xs font-bold text-slate-700">
                                {{ $patient->berat_badan ? $patient->berat_badan . ' kg' : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB CONTENT: BOOKINGS HISTORY --}}
            <div x-show="activeTab === 'bookings'" x-transition class="space-y-4">
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em] px-1">Riwayat Booking
                    ({{ $bookings->count() }})</h3>

                @forelse($bookings as $bk)
                    <div class="bg-white rounded-[1.8rem] p-5 border border-slate-100 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <span
                                class="px-2 py-0.5 bg-slate-50 text-slate-500 text-[8px] font-black uppercase tracking-wider rounded border border-slate-200">{{ $bk['id'] }}</span>

                            @php
                                $colorMap = [
                                    'pending' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                    'approved' => 'bg-teal-50 text-teal-600 border-teal-100',
                                    'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'cancelled' => 'bg-slate-50 text-slate-400 border-slate-100',
                                    'rejected' => 'bg-rose-50 text-rose-500 border-rose-100',
                                ];
                                $statusColor =
                                    $colorMap[strtolower($bk['status'])] ??
                                    'bg-slate-50 text-slate-500 border-slate-100';
                            @endphp

                            <span
                                class="px-2.5 py-0.5 text-[8px] font-black uppercase tracking-widest rounded border {{ $statusColor }}">
                                {{ $bk['status'] }}
                            </span>
                        </div>

                        <div class="space-y-1 pt-1">
                            <h4 class="text-xs font-black text-slate-800 leading-tight">{{ $bk['layanan'] }}</h4>
                            <div class="flex items-center gap-1 text-[10px] font-medium text-slate-400">
                                <span>Terapis: <strong>{{ $bk['terapis'] }}</strong></span>
                            </div>
                            <div class="text-[10px] font-medium text-slate-400">
                                {{ $bk['tanggal'] }} • {{ $bk['jam'] }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-white rounded-[2rem] border border-slate-100 shadow-sm space-y-2">
                        <p class="text-xs font-bold text-slate-400">Belum ada riwayat booking.</p>
                    </div>
                @endforelse
            </div>

            {{-- TAB CONTENT: MEDICAL HISTORY --}}
            <div x-show="activeTab === 'medical'" x-transition class="space-y-4">
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em] px-1">Riwayat Rekam Medis
                    ({{ $medicalRecords->count() }})</h3>

                @forelse($medicalRecords as $med)
                    <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-50 pb-2">
                            <div class="space-y-0.5">
                                <h4 class="text-xs font-black text-slate-800">{{ $med['layanan'] }}</h4>
                                <p class="text-[9px] font-medium text-slate-400">{{ $med['tanggal'] }} •
                                    {{ $med['jam'] }}</p>
                            </div>
                            <div class="text-right">
                                <span
                                    class="text-[8px] font-bold text-slate-400 uppercase block tracking-wider">Terapis</span>
                                <span class="text-xs font-bold text-slate-700">{{ $med['terapis'] }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs leading-relaxed">
                            <div class="space-y-0.5">
                                <span
                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Tensi</span>
                                <p class="font-bold text-slate-700">{{ $med['tensi'] }}</p>
                            </div>
                            <div class="space-y-0.5">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Skala
                                    Nyeri</span>
                                <p class="font-bold text-slate-700">{{ $med['skala_nyeri'] }}/10</p>
                            </div>
                            <div class="col-span-2 space-y-0.5">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Keluhan
                                    Pasien</span>
                                <p
                                    class="font-medium text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100/50">
                                    {{ $med['keluhan'] }}</p>
                            </div>
                            <div class="col-span-2 space-y-0.5">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Catatan
                                    Terapis</span>
                                <p
                                    class="font-medium text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100/50">
                                    {{ $med['catatan_terapis'] }}</p>
                            </div>
                            <div class="col-span-2 space-y-0.5">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Saran &
                                    Rekomendasi</span>
                                <p
                                    class="font-medium text-slate-600 bg-teal-50/20 p-2.5 rounded-xl border border-teal-100/20">
                                    {{ $med['saran'] }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-white rounded-[2rem] border border-slate-100 shadow-sm space-y-2">
                        <p class="text-xs font-bold text-slate-400">Belum ada riwayat medis tercatat.</p>
                    </div>
                @endforelse
            </div>

        </div>

        {{-- BOTTOM NAVBAR --}}
        @if (auth()->user()->role === \App\Enums\UserRole::THERAPIST)
            <x-navigation.therapist-navbar active="" />
        @else
            <x-navigation.admin-cabang-navbar active="pasien" />
        @endif

    </x-layouts.mobile-app>

@endsection
