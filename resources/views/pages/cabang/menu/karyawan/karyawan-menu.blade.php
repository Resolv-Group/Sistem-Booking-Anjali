@extends('components.layouts.app')

@section('title', 'Kelola Karyawan Klinik')

@section('content')

<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{ 
    search: '', 
    activeTab: 'cabang' 
}">

    {{-- 1. TOPBAR --}}
    <div class="px-6 py-5 flex justify-between items-center bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin-global.cabang.menu', $kolaborasi->id) }}" class="p-1 -ml-1 text-slate-400 hover:text-teal-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-sm font-bold text-teal-800 uppercase tracking-widest leading-none">Rumah Terapi Anjali</h1>
        </div>
        <div class="w-10 h-10 rounded-xl border-2 border-orange-100 p-0.5 bg-white">
            <img src="https://i.pravatar.cc/100?u=admin" class="w-full h-full rounded-lg object-cover">
        </div>
    </div>

    <div class="px-6 pt-8 pb-32 space-y-6">

        {{-- SUCCESS NOTIFICATION --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-4"
                class="bg-teal-600 text-white rounded-2xl p-4 text-sm font-bold text-center shadow-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- 2. TITLE SECTION --}}
        <div class="space-y-3 px-1">
            <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none">Kelola <br> Karyawan Klinik</h2>
            <p class="text-xs font-bold text-teal-600 uppercase tracking-widest">{{ $kolaborasi->nama_kolaborasi }}</p>
            <p class="text-sm font-medium text-slate-500 leading-relaxed">
                Kelola daftar karyawan atau petakan karyawan dari cabang lain ke cabang ini.
            </p>
        </div>

        {{-- 3. TAB CONTROLS --}}
        <div class="grid grid-cols-2 p-1 bg-slate-100 rounded-xl">
            <button 
                @click="activeTab = 'cabang'" 
                :class="activeTab === 'cabang' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-500'"
                class="py-3 rounded-lg text-xs font-black uppercase tracking-wider transition-all"
            >
                Karyawan Cabang ({{ $karyawans->count() }})
            </button>
            <button 
                @click="activeTab = 'mapping'" 
                :class="activeTab === 'mapping' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-500'"
                class="py-3 rounded-lg text-xs font-black uppercase tracking-wider transition-all"
            >
                Petakan Karyawan ({{ $otherKaryawans->count() }})
            </button>
        </div>

        {{-- 4. SEARCH BAR --}}
        <div class="relative group">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400 group-focus-within:text-teal-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" x-model="search" placeholder="Cari nama karyawan..." 
                class="w-full pl-12 pr-4 py-4 bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none">
        </div>

        {{-- 5. CONTENT TABS --}}
        
        {{-- TAB 1: KARYAWAN CABANG --}}
        <div x-show="activeTab === 'cabang'" class="space-y-4">
            @forelse($karyawans as $item)
            <a href="{{ route('admin-global.karyawan.detail', [$kolaborasi->id, $item->id]) }}" 
                class="bg-white rounded-[1.8rem] p-5 shadow-sm border border-slate-100 flex items-center justify-between group active:scale-[0.98] transition-all"
                x-show="'{{ strtolower($item->nama_karyawan) }}'.includes(search.toLowerCase()) || search === ''"
            >
                <div class="flex items-center gap-4">
                    {{-- Avatar --}}
                    <div class="w-14 h-14 rounded-2xl bg-teal-50 border border-slate-100 overflow-hidden shrink-0 flex items-center justify-center">
                        @if($item->foto_path)
                            <img src="{{ Storage::url($item->foto_path) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-teal-700 font-black text-lg">{{ substr($item->nama_karyawan, 0, 2) }}</span>
                        @endif
                    </div>

                    {{-- Text Info --}}
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="text-sm font-black text-slate-800 leading-tight">{{ $item->nama_karyawan }}</h4>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[8px] font-black uppercase tracking-widest rounded">
                                {{ $item->peran }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-slate-400">{{ $item->no_telp }}</span>
                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                            @if($item->status_karyawan === 'Aktif')
                                <span class="text-[9px] font-black text-teal-600 uppercase tracking-wider">Aktif</span>
                            @else
                                <span class="text-[9px] font-black text-red-500 uppercase tracking-wider">{{ $item->status_karyawan }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action Arrow --}}
                <svg class="w-4 h-4 text-slate-300 group-hover:text-teal-600 transition-all group-hover:translate-x-1 shrink-0"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path d="M9 5l7 7-7 7" />
                </svg>
            </a>
            @empty
            <div class="text-center py-16 space-y-3 bg-white rounded-3xl border border-slate-100">
                <div class="w-16 h-16 mx-auto bg-slate-100 rounded-2xl flex items-center justify-center text-slate-300">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857" />
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-400">Belum ada karyawan di cabang ini.</p>
                <p class="text-xs text-slate-300">Daftarkan karyawan baru menggunakan tombol di bawah.</p>
            </div>
            @endforelse
        </div>

        {{-- TAB 2: PETAKAN KARYAWAN --}}
        <div x-show="activeTab === 'mapping'" class="space-y-4" x-cloak>
            <p class="text-xs text-slate-400 font-bold px-1 uppercase tracking-wider">Karyawan dari cabang lain / tidak ber-cabang:</p>
            
            @forelse($otherKaryawans as $item)
            <div 
                class="bg-white rounded-[1.8rem] p-5 shadow-sm border border-slate-100 flex items-center justify-between group transition-all"
                x-show="'{{ strtolower($item->nama_karyawan) }}'.includes(search.toLowerCase()) || search === ''"
            >
                <div class="flex items-center gap-4">
                    {{-- Avatar --}}
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 border border-slate-100 overflow-hidden shrink-0 flex items-center justify-center">
                        @if($item->foto_path)
                            <img src="{{ Storage::url($item->foto_path) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-orange-700 font-black text-lg">{{ substr($item->nama_karyawan, 0, 2) }}</span>
                        @endif
                    </div>

                    {{-- Text Info --}}
                    <div class="space-y-1">
                        <h4 class="text-sm font-black text-slate-800 leading-tight">{{ $item->nama_karyawan }}</h4>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-1.5 py-0.5 bg-slate-100 text-slate-500 text-[8px] font-black uppercase tracking-widest rounded">
                                {{ $item->peran }}
                            </span>
                            <span class="px-1.5 py-0.5 bg-blue-50 text-blue-600 text-[8px] font-bold rounded">
                                {{ $item->kolaborasi ? $item->kolaborasi->nama_kolaborasi : 'Belum Dipetakan' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action Map Button --}}
                <form action="{{ route('admin-global.karyawan.map', [$kolaborasi->id, $item->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-teal-50 hover:bg-teal-100 text-teal-700 text-[10px] font-black uppercase tracking-widest rounded-xl border border-teal-100 active:scale-95 transition-all">
                        Petakan
                    </button>
                </form>
            </div>
            @empty
            <div class="text-center py-16 space-y-3 bg-white rounded-3xl border border-slate-100">
                <div class="w-16 h-16 mx-auto bg-slate-100 rounded-2xl flex items-center justify-center text-slate-300">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-400">Tidak ada karyawan cabang lain yang tersedia.</p>
            </div>
            @endforelse
        </div>

    </div>

    {{-- FLOATING ACTION BUTTON (Hanya aktif untuk Tab Karyawan Cabang) --}}
    <div x-show="activeTab === 'cabang'" class="fixed bottom-24 right-6 z-50">
        <a href="{{ route('admin-global.karyawan.create', $kolaborasi->id) }}" 
           class="w-14 h-14 bg-teal-900 text-white rounded-xl flex items-center justify-center shadow-2xl active:scale-90 transition-all duration-300"
        >
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
        </a>
    </div>

    {{-- 6. BOTTOM NAVBAR --}}
    <x-navigation.admin-global-navbar active="cabang" />

</x-layouts.mobile-app>

@endsection
