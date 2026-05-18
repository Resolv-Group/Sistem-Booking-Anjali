@extends('components.layouts.app')

@section('title', 'Direktori Cabang')

@section('content')
<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{ search: '' }">

    {{-- 1. HEADER WITH ADD ACTION --}}
    <div class="px-6 py-6 bg-white border-b border-slate-100 sticky top-0 z-50 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Cabang<span class="text-blue-600">.</span></h1>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Manajemen Lokasi MJA</p>
            </div>
            
            {{-- ADD BUTTON ON TOP --}}
            <a href="{{ route('admin-global.cabang.create') }}" 
                class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-slate-200 active:scale-90 transition-all">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
            </a>
        </div>

        {{-- SEARCH BAR --}}
        <div class="relative group">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" x-model="search" placeholder="Cari nama cabang atau kota..." 
                class="w-full pl-12 pr-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-100 focus:bg-white transition-all outline-none shadow-inner">
        </div>
    </div>

    {{-- 2. BRANCH LIST (SCALABLE) --}}
    <div class="px-6 py-8 space-y-4 pb-32">
        @php
            $branches = [
                ['id' => 1, 'name' => 'Rumah Terapi Anjali', 'loc' => 'Surabaya Pusat', 'staff' => 12, 'theme' => 'teal'],
                ['id' => 2, 'name' => 'Lima Jari', 'loc' => 'Malang Kota', 'staff' => 8, 'theme' => 'blue'],
                ['id' => 3, 'name' => 'Anjali Jakarta', 'loc' => 'Jakarta Selatan', 'staff' => 24, 'theme' => 'teal'],
            ];
        @endphp

        @foreach($branches as $b)
        <a href="{{ route('cabang.show', $b['id']) }}" 
            class="block p-5 bg-white border border-slate-100 rounded-[1.5rem] shadow-sm hover:shadow-xl hover:shadow-slate-200/50 hover:border-blue-200 transition-all active:scale-[0.98] group">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white font-black text-xl shadow-lg {{ $b['theme'] === 'teal' ? 'bg-teal-600 shadow-teal-100' : 'bg-blue-600 shadow-blue-100' }}">
                        {{ substr($b['name'], 0, 2) }}
                    </div>
                    <div>
                        <h4 class="text-base font-black text-slate-800 leading-tight group-hover:text-blue-600 transition-colors">{{ $b['name'] }}</h4>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $b['loc'] }}</span>
                            <div class="w-1 h-1 rounded-full bg-slate-200"></div>
                            <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">{{ $b['staff'] }} Staff</span>
                        </div>
                    </div>
                </div>
                <svg class="w-5 h-5 text-slate-200 group-hover:text-slate-800 transition-all transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>
        @endforeach
    </div>

    <x-navigation.admin-global-navbar active="cabang" />
</x-layouts.mobile-app>
@endsection