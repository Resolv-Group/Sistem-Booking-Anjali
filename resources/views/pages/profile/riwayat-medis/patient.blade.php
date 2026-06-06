@extends('components.layouts.app')

@section('title', 'Rekam Medis - Anjali')

<script>
    window.records = @js($records);
</script>

@section('content')
    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32" x-data="{ 
        search: '',
        activeId: null,
        records: window.records
    }">

        {{-- 1. TOPBAR GLASSY --}}
        <nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-100 px-6 py-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('patient.profile') }}" 
                   class="group flex items-center justify-center w-10 h-10 bg-slate-50 hover:bg-teal-50 rounded-xl transition-all duration-300 active:scale-90 border border-slate-100">
                    <i data-lucide="chevron-left" class="w-5 h-5 text-slate-400 group-hover:text-teal-600"></i>
                </a>
                <div class="flex flex-col">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none mb-1">Informasi Medis</span>
                    <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none">Rekam Medis</h1>
                </div>
            </div>
        </nav>

        <div class="px-6 pt-8 space-y-8">
            
            {{-- 2. PATIENT MINI CARD --}}
            <div class="bg-teal-900 rounded-[2.2rem] p-6 text-white shadow-xl shadow-teal-900/20 relative overflow-hidden">
                <div class="relative z-10 space-y-4">
                    <div class="flex justify-between items-start">
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-teal-300 uppercase tracking-widest">Pasien Utama</p>
                            <h3 class="text-xl font-black tracking-tight">{{ $patient->nama_pasien }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20">
                            <i data-lucide="database" class="w-6 h-6 text-teal-200"></i>
                        </div>
                    </div>
                    <div class="pt-2 flex items-center gap-4">
                        <div class="px-3 py-1 bg-white/10 rounded-lg border border-white/10">
                            <p class="text-[8px] font-black text-teal-300 uppercase">ID Rekam Medis</p>
                            <p class="text-xs font-bold font-mono">{{ $patient->pasien_public_id }}</p>
                        </div>
                        <div class="px-3 py-1 bg-white/10 rounded-lg border border-white/10">
                            <p class="text-[8px] font-black text-teal-300 uppercase">Total Sesi</p>
                            <p class="text-xs font-bold">{{ $records->count() }} Sesi</p>
                        </div>
                    </div>
                </div>
                {{-- Decorative circles --}}
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-teal-800 rounded-full opacity-50"></div>
                <div class="absolute -right-5 -top-5 w-20 h-20 bg-teal-700 rounded-full opacity-30"></div>
            </div>

            {{-- 3. SEARCH HISTORY --}}
            <div class="relative group px-1">
                <i data-lucide="search" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300"></i>
                <input type="text" x-model="search" placeholder="Cari tanggal atau nama terapis..." 
                    class="w-full pl-12 pr-5 py-4 bg-white border border-slate-100 rounded-2xl text-xs font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/5 outline-none shadow-sm transition-all">
            </div>

            {{-- 4. MEDICAL LIST --}}
            <div class="space-y-4">
                <template x-for="record in records.filter(r => r.tanggal.toLowerCase().includes(search.toLowerCase()) || r.terapis.toLowerCase().includes(search.toLowerCase()))" :key="record.id">
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden transition-all duration-300"
                        :class="activeId === record.id ? 'ring-2 ring-teal-500/20 shadow-lg' : ''">
                        
                        {{-- Collapsed State (Header) --}}
                        <div @click="activeId = activeId === record.id ? null : record.id" 
                            class="p-5 flex items-center justify-between cursor-pointer active:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-teal-600 border border-slate-100">
                                    <i data-lucide="calendar-check" class="w-6 h-6"></i>
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="text-sm font-black text-slate-800" x-text="record.layanan"></h4>
                                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-tighter" x-text="record.tanggal"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-1 bg-emerald-50 text-emerald-600 text-[8px] font-black uppercase rounded-md border border-emerald-100">Selesai</span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-300 transition-transform duration-300" :class="activeId === record.id ? 'rotate-180 text-teal-500' : ''"></i>
                            </div>
                        </div>

                        {{-- Expanded State (Detail) --}}
                        <div x-show="activeId === record.id" x-collapse>
                            <div class="px-6 pb-6 pt-2 space-y-6">
                                {{-- Divider --}}
                                <div class="h-px bg-slate-50 w-full"></div>

                                {{-- Info Grid --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Terapis</p>
                                        <p class="text-xs font-bold text-slate-700" x-text="record.terapis"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Lokasi</p>
                                        <p class="text-xs font-bold text-slate-700" x-text="record.kolaborasi"></p>
                                    </div>
                                </div>

                                {{-- Notes Section --}}
                                <div class="space-y-4">
                                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                                        <div class="flex items-center gap-2 text-teal-700">
                                            <i data-lucide="activity" class="w-3 h-3"></i>
                                            <p class="text-[9px] font-black uppercase tracking-widest">Keluhan Awal</p>
                                        </div>
                                        <p class="text-xs font-medium text-slate-600 leading-relaxed" x-text="record.keluhan"></p>
                                    </div>

                                    <div class="p-4 bg-teal-50/50 rounded-2xl border border-teal-100/50 space-y-2">
                                        <div class="flex items-center gap-2 text-teal-800">
                                            <i data-lucide="file-text" class="w-3 h-3"></i>
                                            <p class="text-[9px] font-black uppercase tracking-widest">Ringkasan Sesi</p>
                                        </div>
                                        <p class="text-xs font-bold text-teal-900 leading-relaxed" x-text="record.ringkasan"></p>
                                    </div>

                                    <div class="p-4 bg-white border border-slate-100 rounded-2xl shadow-sm space-y-2">
                                        <div class="flex items-center gap-2 text-orange-600">
                                            <i data-lucide="lightbulb" class="w-3 h-3"></i>
                                            <p class="text-[9px] font-black uppercase tracking-widest">Catatan Ahli</p>
                                        </div>
                                        <p class="text-xs font-medium text-slate-500 italic leading-relaxed" x-text="record.catatan"></p>
                                    </div>
                                </div>

                                {{-- Footer Action --}}
                                {{-- <button class="w-full py-3 bg-white border border-slate-200 text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center justify-center gap-2 active:scale-95 transition-all">
                                    <i data-lucide="download" class="w-3 h-3"></i> Unduh Laporan (PDF)
                                </button> --}}
                            </div>
                        </div>
                    </div>
                </template>

                {{-- EMPTY STATE --}}
                <div x-show="records.length === 0" class="text-center py-20 px-10 space-y-4">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto border border-slate-100">
                        <i data-lucide="clipboard-x" class="w-8 h-8 text-slate-200"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Belum Ada Catatan</h3>
                        <p class="text-xs font-medium text-slate-400 leading-relaxed">
                            Rekam medis akan muncul secara otomatis setelah Anda menyelesaikan sesi terapi.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <x-navigation.patient-navbar active="profile" />
    </x-layouts.mobile-app>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
        // Hook for Alpine transitions if needed
        document.addEventListener('alpine:init', () => {
            Alpine.effect(() => { lucide.createIcons(); });
        });
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
    </style>
@endsection