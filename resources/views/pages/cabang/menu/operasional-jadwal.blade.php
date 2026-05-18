@extends('components.layouts.app')

@section('title', 'Jam Operasional Klinik')

@section('content')
<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{ 
    days: [
        { name: 'Senin', active: true, open: '08:00', close: '18:00', hasBreak: false, breakStart: '12:00', breakEnd: '13:00' },
        { name: 'Selasa', active: false, open: '08:00', close: '18:00', hasBreak: false, breakStart: '12:00', breakEnd: '13:00' },
        { name: 'Rabu', active: true, open: '08:00', close: '18:00', hasBreak: true, breakStart: '12:00', breakEnd: '13:00' },
        { name: 'Kamis', active: true, open: '08:00', close: '17:00', hasBreak: false, breakStart: '12:00', breakEnd: '13:00' },
        { name: 'Jumat', active: true, open: '08:00', close: '17:00', hasBreak: false, breakStart: '12:00', breakEnd: '13:00' },
        { name: 'Sabtu', active: false, open: '08:00', close: '15:00', hasBreak: false, breakStart: '12:00', breakEnd: '13:00' },
        { name: 'Minggu', active: false, open: '08:00', close: '15:00', hasBreak: false, breakStart: '12:00', breakEnd: '13:00' }
    ]
}">

    {{-- 1. HEADER --}}
    <div class="px-6 py-5 flex justify-between items-center bg-white/90 backdrop-blur-xl sticky top-0 z-50 border-b border-slate-100">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin-global.cabang.menu') }}" class="p-2 -ml-2 text-slate-400 hover:text-teal-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-sm font-bold text-teal-800 uppercase tracking-widest leading-none">Rumah Terapi Anjali</h1>
        </div>
        <img src="https://i.pravatar.cc/100?u=admin" class="w-10 h-10 rounded-xl border-2 border-orange-100 p-0.5 object-cover">
    </div>

    <div class="px-6 pt-8 pb-32 space-y-8">
        
        {{-- 2. TITLE SECTION --}}
        <div class="space-y-3 px-1">
            <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none">Jam Operasional <br> Klinik</h2>
            <p class="text-sm font-medium text-slate-500 leading-relaxed">
                Informasi jam buka dan tutup untuk membantu merencanakan kunjungan atau sesi konsultasi berikutnya secara lebih mudah.
            </p>
        </div>

        {{-- 3. DAYS LIST --}}
        <div class="space-y-4">
            <template x-for="(day, index) in days" :key="index">
                <div class="bg-white rounded-[1.5rem] border border-slate-100 shadow-sm transition-all duration-300 overflow-hidden"
                    :class="!day.active && 'opacity-50 grayscale-[0.3] bg-slate-50/50'">
                    
                    {{-- Card Header: Day & Toggle --}}
                    <div class="px-6 py-5 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-slate-50 text-slate-400 border border-slate-100 transition-colors"
                                :class="day.active && 'bg-teal-50 text-teal-600 border-teal-100'">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-base font-black text-slate-700 uppercase tracking-widest" x-text="day.name"></h3>
                        </div>

                        {{-- Toggle Switch --}}
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="day.active" class="sr-only peer">
                            <div class="w-12 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-[20px] after:w-[20px] after:transition-all peer-checked:bg-teal-700"></div>
                        </label>
                    </div>

                    {{-- Card Content (Only visible if active) --}}
                    <div x-show="day.active" x-collapse x-cloak class="px-6 pb-6 space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 grid grid-cols-2 gap-3">
                                {{-- Buka --}}
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Buka</label>
                                    <div class="relative flex items-center">
                                        <input type="time" x-model="day.open" class="w-full px-4 py-3 bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20">
                                    </div>
                                </div>
                                {{-- Tutup --}}
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Tutup</label>
                                    <div class="relative flex items-center">
                                        <input type="time" x-model="day.close" class="w-full px-4 py-3 bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20">
                                    </div>
                                </div>
                            </div>
                            <div class="p-2.5 opacity-0 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"></svg>
                            </div>
                        </div>

                        {{-- Section Istirahat --}}
                        <div class="space-y-4">
                            {{-- Baris Istirahat (Muncul jika hasBreak) --}}
                            <div x-show="day.hasBreak" x-transition class="space-y-3">
                                <div class="relative flex items-center justify-center">
                                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100 border-dashed"></div></div>
                                    <span class="relative px-3 bg-white text-[9px] font-black text-slate-300 uppercase tracking-[0.3em]">Jam Istirahat</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 grid grid-cols-2 gap-3">
                                        <input type="time" x-model="day.breakStart" class="w-full px-4 py-3 bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-700">
                                        <input type="time" x-model="day.breakEnd" class="w-full px-4 py-3 bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-700">
                                    </div>
                                    <button @click="day.hasBreak = false" class="p-2.5 text-slate-300 hover:text-rose-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Tombol Tambah Istirahat (Muncul jika belum ada break) --}}
                            <button x-show="!day.hasBreak" @click="day.hasBreak = true" 
                                class="w-full py-4 border-2 border-dashed border-slate-100 rounded-xl flex items-center justify-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:bg-slate-50 transition-all active:scale-[0.98]">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
                                Tambah Waktu Istirahat Siang
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- 4. FOOTER ACTIONS --}}
        <div class="space-y-3 pt-6">
            <button class="w-full py-5 bg-teal-800 text-white rounded-xl text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                Simpan Operasional Jam
            </button>
            <button class="w-full py-4 bg-slate-200/50 text-slate-500 rounded-xl text-sm font-black uppercase tracking-[0.2em] active:scale-95 transition-all">
                Batal
            </button>
        </div>

    </div>

    <x-navigation.admin-global-navbar active="cabang" />

</x-layouts.mobile-app>

<style>
    /* Styling input time agar bersih di mobile */
    input[type="time"]::-webkit-calendar-picker-indicator {
        background: transparent;
        color: transparent;
        cursor: pointer;
        height: auto;
        width: auto;
    }
    
    [x-cloak] { display: none !important; }
</style>

@endsection