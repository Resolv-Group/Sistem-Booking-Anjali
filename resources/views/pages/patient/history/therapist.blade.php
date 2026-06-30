@extends('components.layouts.app')

@section('title', 'Riwayat Medis Pasien')

<script>
    window.records = @js($records);
</script>

@section('content')
    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32" x-data="{
        activeId: null,
        search: '',
        records: window.records,
    }">

        {{-- 1. TOPBAR GLASSY --}}
        <nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-100/80 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="javascript:void(0)" onclick="window.history.back()"
                        class="group flex items-center justify-center w-10 h-10 bg-white border border-slate-100 rounded-xl shadow-sm hover:bg-teal-50 transition-all active:scale-90">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-600" fill="none" stroke="currentColor"
                            stroke-width="3" viewBox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="flex flex-col">
                        <span
                            class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">Pasien
                            Saya</span>
                        <h1
                            class="text-sm font-black text-slate-800 tracking-tight leading-none uppercase truncate max-w-[180px]">
                            Riwayat Medis
                        </h1>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-full border-2 border-white shadow-sm overflow-hidden">
                    <img src="{{ asset('images/logo_anjali.jpg') }}" class="w-full h-full object-cover">
                </div>
            </div>
        </nav>

        <div class="px-6 pt-8 space-y-8">

            {{-- 2. PATIENT INFO HEADER --}}
            <div class="bg-white p-6 rounded-[2.2rem] border border-slate-100 shadow-sm flex items-center gap-5">
                <div
                    class="w-20 h-20 rounded-[1.8rem] bg-teal-50 flex items-center justify-center text-teal-600 shrink-0 overflow-hidden border border-teal-100">
                    <img src="{{ $patient->foto ? 'data:' . $patient->foto_mime . ';base64,' . $patient->foto : asset('images/logo_anjali.jpg') }}"
                        class="w-full h-full object-cover">
                </div>
                <div class="space-y-1 flex-1">
                    <div class="flex justify-between items-start">
                        <h2 class="text-xl font-black text-slate-800 leading-tight">{{ $patient->nama_pasien }}</h2>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        {{-- Info Umur --}}
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[9px] font-black uppercase rounded">
                            {{ $patient->tanggal_lahir ? $patient->tanggal_lahir->age : '-' }} Tahun
                        </span>

                        {{-- Info Gender --}}
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[9px] font-black uppercase rounded">
                            {{ $patient->jenis_kelamin == 'L' ? 'Pria' : 'Wanita' }}
                        </span>

                        {{-- TOTAL SESI (DITAMBAHKAN) --}}
                        <span
                            class="px-2 py-0.5 bg-teal-50 text-teal-700 border border-teal-100 text-[9px] font-black uppercase rounded shadow-sm">
                            {{ count($records) }} Kunjungan
                        </span>
                    </div>

                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mt-1">
                        Terdaftar sejak {{ $patient->created_at->translatedFormat('M Y') }}
                    </p>
                </div>
            </div>

            {{-- 3. SEARCH WITHIN HISTORY --}}
            <div class="relative group">
                <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                </div>
                <input type="text" x-model="search" placeholder="Cari layanan atau tanggal..."
                    class="w-full pl-12 pr-5 py-4 bg-white border border-slate-100 rounded-2xl text-xs font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none shadow-sm">
            </div>

            {{-- 4. TIMELINE LIST --}}
            <div class="space-y-4">
                <template
                    x-for="r in records.filter(i => i.layanan.toLowerCase().includes(search.toLowerCase()) || i.tanggal.toLowerCase().includes(search.toLowerCase()))"
                    :key="r.id_raw">
                    <div
                        class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden transition-all duration-300">

                        {{-- Accordion Trigger --}}
                        <div @click="activeId = activeId === r.id_raw ? null : r.id_raw"
                            class="p-5 flex items-center justify-between cursor-pointer active:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-inner transition-colors"
                                    :class="r.status === 'selesai' ? 'bg-teal-50 text-teal-600' :
                                        'bg-orange-50 text-orange-600'">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="text-sm font-black text-slate-800" x-text="r.layanan"></h4>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter"
                                        x-text="r.tanggal + ' • ' + r.jam"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 text-[8px] font-black uppercase rounded shadow-sm"
                                    :class="r.status === 'selesai' ? 'bg-emerald-50 text-emerald-600' :
                                            (r.status === 'dibatalkan' ? 'bg-rose-50 text-rose-600' : 'bg-orange-50 text-orange-600')"
                                    x-text="r.status"></span>
                                <svg class="w-4 h-4 text-slate-300 transition-transform duration-300"
                                    :class="activeId === r.id_raw ? 'rotate-180 text-teal-500' : ''" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        {{-- Accordion Content --}}
                        <div x-show="activeId === r.id_raw" x-collapse>
                            <div class="px-6 pb-6 pt-2 space-y-5">
                                <div class="h-px bg-slate-50 w-full"></div>

                                {{-- Keluhan --}}
                                <div class="space-y-2">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Keluhan
                                        Pasien</p>
                                    <div class="p-4 bg-slate-50 rounded-2xl border border-white text-xs font-medium text-slate-600 leading-relaxed"
                                        x-text="r.keluhan"></div>
                                </div>

                                {{-- Ringkasan Sesi --}}
                                <div class="space-y-2">
                                    <p class="text-[9px] font-black text-teal-600 uppercase tracking-widest ml-1">Ringkasan
                                        Sesi</p>
                                    <div class="p-4 bg-teal-50/50 rounded-2xl border border-white text-xs font-bold text-teal-800 leading-relaxed"
                                        x-text="r.ringkasan"></div>
                                </div>

                                {{-- Catatan Medis Terapis --}}
                                <div class="space-y-2">
                                    <p class="text-[9px] font-black text-orange-600 uppercase tracking-widest ml-1">Catatan
                                        Diagnostik / Tindakan</p>
                                    <div class="p-4 bg-orange-50/30 rounded-2xl border border-orange-100 text-xs font-medium text-slate-500 italic leading-relaxed"
                                        x-text="r.catatan"></div>
                                </div>

                                {{-- Edit Button (Jika Sesi Masih Aktif/Baru) --}}
                                <div class="pt-2" x-show="r.status !== 'dibatalkan'">
                                    <a :href="'/jadwal/therapist/session/' + r.id_raw + '/catatan'"
                                        class="w-full py-3 bg-white border border-slate-200 text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center justify-center gap-2 active:scale-95 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            {{-- Papan Clipboard --}}
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            {{-- Garis-garis catatan --}}
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6" />
                                        </svg>
                                        Lihat Catatan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- EMPTY STATE --}}
                <div x-show="records.length === 0" class="text-center py-20 px-10">
                    <div
                        class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto border border-slate-100 mb-4">
                        <svg class="w-8 h-8 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Belum ada riwayat sesi</p>
                </div>
            </div>
        </div>

        {{-- BOTTOM NAVBAR --}}
        <x-navigation.therapist-navbar active="pasien" />
    </x-layouts.mobile-app>
@endsection
