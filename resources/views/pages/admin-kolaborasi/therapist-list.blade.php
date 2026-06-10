@extends('components.layouts.app')

@section('title', 'Tim Spesialis Terapis')

{{-- Ubah variabel global agar sesuai konteks --}}
<script>
    window.therapistList = @js($therapists);
</script>

@section('content')
    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32" x-data="{
        search: '',
        therapists: window.therapistList
    }">

        {{-- 1. TOPBAR GLASSY --}}
        <nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-100/80 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">ANJALI SADINA MULYO</span>
                        <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none uppercase">Tim Spesialis</h1>
                    </div>
                </div>
                <div class="relative">
                    <div class="w-9 h-9 rounded-xl border-2 border-white shadow-sm p-0.5 bg-slate-50">
                        <img src="{{ asset('images/logo_anjali.jpg') }}" class="w-full h-full rounded-[8px] object-cover">
                    </div>
                </div>
            </div>
        </nav>

        <div class="px-6 pt-8 space-y-8">

            {{-- 2. TITLE SECTION --}}
            <div class="space-y-2">
                <h2 class="text-3xl font-bold text-teal-900 tracking-tight">Rekan Terapis</h2>
                <p class="text-sm text-slate-500 font-medium leading-relaxed">Daftar tenaga ahli dan spesialis yang berkolaborasi dalam jaringan Anjali.</p>
            </div>

            {{-- 3. TOTAL THERAPIST CARD (Summary) --}}
            <div class="bg-teal-900 rounded-[2.2rem] p-6 text-white shadow-xl shadow-teal-900/20 relative overflow-hidden">
                <div class="relative z-10 flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-teal-300 uppercase tracking-[0.2em]">Total Tim Ahli</p>
                        <div class="flex items-baseline gap-2">
                            <h3 class="text-4xl font-black tracking-tighter" x-text="therapists.length">0</h3>
                            <span class="text-sm font-bold text-teal-200 uppercase tracking-widest">Spesialis</span>
                        </div>
                    </div>
                    <div class="w-14 h-14 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20">
                        <svg class="w-7 h-7 text-teal-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                {{-- Decorative circles --}}
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-teal-800 rounded-full opacity-40"></div>
                <div class="absolute -right-2 -top-10 w-24 h-24 bg-teal-700 rounded-full opacity-20"></div>
            </div>

            {{-- 4. SEARCH BAR --}}
            <div class="relative group">
                <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-300 group-focus-within:text-teal-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                </div>
                <input type="text" x-model="search" placeholder="Cari nama atau spesialisasi..."
                    class="w-full pl-12 pr-5 py-4 bg-white border border-slate-100 rounded-2xl text-xs font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none shadow-sm">
            </div>

            {{-- 5. THERAPIST LIST --}}
            <div class="space-y-4">
                <template x-for="t in therapists.filter(i => i.nama.toLowerCase().includes(search.toLowerCase()) || i.peran.toLowerCase().includes(search.toLowerCase()))" :key="t.id_raw">
                    <div @click="window.location.href = '/admin-cabang/therapist/' + t.id_raw"
                        class="bg-white rounded-[2rem] p-5 border border-slate-100 shadow-sm flex items-center justify-between group active:scale-[0.98] transition-all duration-300 cursor-pointer">
                        <div class="flex items-center gap-4">
                            {{-- Avatar Terapis --}}
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-50 shadow-inner">
                                <img :src="t.foto" class="w-full h-full object-cover">
                            </div>

                            <div class="space-y-1">
                                <h4 class="text-sm font-black text-slate-800 leading-tight" x-text="t.nama"></h4>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 bg-teal-50 text-teal-600 text-[8px] font-black uppercase rounded tracking-widest" x-text="t.peran"></span>
                                    <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase" x-text="t.kolaborasi"></span>
                                </div>
                                <div class="flex items-center gap-1.5 pt-0.5">
                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Aktif Bertugas</p>
                                </div>
                            </div>
                        </div>

                        {{-- Action Button (WhatsApp / Detail) --}}
                        <div class="flex items-center gap-2" @click.stop>
                            <a :href="'https://wa.me/' + t.telepon" target="_blank"
                                class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </template>

                {{-- EMPTY STATE --}}
                <div x-cloak
                    x-show="therapists.filter(i => i.nama.toLowerCase().includes(search.toLowerCase()) || i.peran.toLowerCase().includes(search.toLowerCase())).length === 0"
                    class="text-center py-20 px-10 space-y-4">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto border border-slate-100">
                        <svg class="w-8 h-8 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Terapis tidak ditemukan</p>
                </div>
            </div>
        </div>

        {{-- BOTTOM NAVBAR --}}
        <x-navigation.admin-cabang-navbar active="terapis" />
    </x-layouts.mobile-app>
@endsection