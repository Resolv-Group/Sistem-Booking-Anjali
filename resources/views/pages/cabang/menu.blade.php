@extends('components.layouts.app')

@section('title', 'Manage Cabang')

@section('content')
<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen">

    {{-- 1. CONTEXT HEADER --}}
    <div class="px-6 py-6 bg-white border-b border-slate-100 sticky top-0 z-50 backdrop-blur-xl bg-white/90">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin-global.cabang') }}" class="p-2 -ml-2 text-slate-400 hover:text-blue-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-lg font-black text-slate-800 uppercase tracking-widest leading-none">Nama Cabang</h1>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1.5">Dashboard Pengaturan Cabang</p>
            </div>
        </div>
    </div>

    <div class="px-6 py-8 space-y-8 pb-32">
        
        {{-- 2. OPERATIONAL QUICK STATS (Visual Feedback) --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl">
                <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-1">Layanan Aktif</p>
                <p class="text-xl font-black text-blue-700">24 <span class="text-xs font-bold opacity-50">Menu</span></p>
            </div>
            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl">
                <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest mb-1">Terapis</p>
                <p class="text-xl font-black text-emerald-700">12 <span class="text-xs font-bold opacity-50">Orang</span></p>
            </div>
        </div>

        {{-- 3. CORE MANAGEMENT MENUS --}}
        <div class="space-y-4">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Konfigurasi Sistem</h3>
            
            <div class="grid grid-cols-1 gap-3">
                {{-- Menu Item 6 --}}
                <a href="#" class="flex items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl hover:border-blue-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 bg-slate-50 text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <span class="text-sm font-black text-slate-700 uppercase tracking-widest">Kelola Booking & Antrean</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition-all group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M9 5l7 7-7 7"/></svg>
                </a>

                {{-- Menu Item 1 --}}
                <a href="{{ route('admin-global.layanan') }}" class="flex items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl hover:border-blue-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 bg-slate-50 text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <span class="text-sm font-black text-slate-700 uppercase tracking-widest">Kelola Layanan</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition-all group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M9 5l7 7-7 7"/></svg>
                </a>

                {{-- Menu Item 2 --}}
                <a href="{{ route('admin-global.operasional-jadwal') }}" class="flex items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl hover:border-blue-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 bg-slate-50 text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-sm font-black text-slate-700 uppercase tracking-widest">Jam Operasional</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition-all group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M9 5l7 7-7 7"/></svg>
                </a>

                {{-- Menu Item 4 --}}
                <a href="#" class="flex items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl hover:border-blue-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 bg-slate-50 text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="text-sm font-black text-slate-700 uppercase tracking-widest">Kelola Karyawan</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition-all group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M9 5l7 7-7 7"/></svg>
                </a>

                {{-- Menu Item 3 --}}
                <a href="{{ route('admin-global.therapist-list') }}" class="flex items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl hover:border-blue-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 bg-slate-50 text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="text-sm font-black text-slate-700 uppercase tracking-widest">Mapping Terapis</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition-all group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M9 5l7 7-7 7"/></svg>
                </a>

                {{-- Menu Item 5 --}}
                <a href="#" class="flex items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl hover:border-blue-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 bg-slate-50 text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span class="text-sm font-black text-slate-700 uppercase tracking-widest">Kelola Pasien</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition-all group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M9 5l7 7-7 7"/></svg>
                </a>

                
            </div>
        </div>
    </div>

</x-layouts.mobile-app>
@endsection