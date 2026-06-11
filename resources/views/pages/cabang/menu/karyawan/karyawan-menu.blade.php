@extends('components.layouts.app')

@section('title', 'Kelola Karyawan')

@section('content')
    <x-layouts.mobile-app class="bg-[#F4F7F9] min-h-screen pb-32" x-data="{
        search: '',
        activeTab: 'cabang',
        selectedIds: [],
    
        toggleSelect(id) {
            if (this.selectedIds.includes(id)) {
                this.selectedIds = this.selectedIds.filter(i => i !== id);
            } else {
                this.selectedIds.push(id);
            }
        },
    
        get isAllSelected() {
            return this.selectedIds.length > 0;
        }
    }">

        {{-- 1. TOPBAR GLASSY --}}
        <div class="sticky top-0 z-50 bg-white/85 backdrop-blur-xl border-b border-slate-100/80 shadow-sm">
            <div class="h-1 w-full bg-gradient-to-r from-teal-500 via-teal-700 to-emerald-500"></div>
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin-global.cabang.menu', $kolaborasi->id) }}" class="p-2 -ml-2 text-slate-400 hover:text-teal-600 hover:bg-slate-50 rounded-xl active:scale-95 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">
                            {{ $kolaborasi->nama_kolaborasi }}
                        </span>
                        <h1 class="text-xs font-black text-slate-800 uppercase tracking-wider leading-none">
                            Kelola Karyawan
                        </h1>
                    </div>
                </div>
                {{-- Right Slot: Simple Decorative Brand Icon --}}
                <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center text-teal-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="px-6 pt-8 space-y-8">

            {{-- 2. HEADER SECTION --}}
            <div class="space-y-1">
                <h2 class="text-2xl font-black text-slate-800 leading-tight">Kelola Karyawan</h2>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Pilih Cabang Terlebih Dahulu</p>
                </div>
            </div>

            {{-- 3. SPLIT SELECTION CARDS (Replacement for Tabs) --}}
            <div class="grid grid-cols-2 gap-3">
                <button @click="activeTab = 'cabang'"
                    :class="activeTab === 'cabang' ? 'bg-teal-800 text-white shadow-teal-900/20' :
                        'bg-white text-slate-400 border-white/50'"
                    class="p-4 rounded-[1.5rem] border text-left transition-all duration-300 shadow-xl relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[10px] font-bold uppercase tracking-widest mb-1 opacity-60">Internal</p>
                        <h3 class="text-sm font-black tracking-tight">Karyawan
                            {{ $kolaborasi->nama_kolaborasi ?? 'Kolaborasi' }}</h3>
                        <p class="text-[10px] mt-2 font-bold">{{ $karyawans->count() }} Orang</p>
                    </div>
                    <svg class="absolute -right-2 -bottom-2 w-16 h-16 opacity-10 group-hover:scale-110 transition-transform"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </button>

                <button @click="activeTab = 'mapping'"
                    :class="activeTab === 'mapping' ? 'bg-teal-800 text-white shadow-teal-900/20' :
                        'bg-white text-slate-400 border-white/50'"
                    class="p-4 rounded-[1.5rem] border text-left transition-all duration-300 shadow-xl relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[10px] font-bold uppercase tracking-widest mb-1 opacity-60">Mapping</p>
                        <h3 class="text-sm font-black tracking-tight">Petakan Staff</h3>
                        <p class="text-[10px] mt-2 font-bold">{{ $otherKaryawans->count() }} Tersedia</p>
                    </div>
                    <svg class="absolute -right-2 -bottom-2 w-16 h-16 opacity-10 group-hover:scale-110 transition-transform"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 3L5 6.99h3V14h2V6.99h3L9 3zm7 14.01V10h-2v7.01h-3L15 21l4-3.99h-3z" />
                    </svg>
                </button>
            </div>

            {{-- 4. SEARCH BAR (Glassy) --}}
            <div class="relative group">
                <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 group-focus-within:text-teal-500 transition-colors" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="search" placeholder="Cari spesialis..."
                    class="w-full pl-12 pr-5 py-4 bg-white/60 backdrop-blur-md border border-white/50 rounded-2xl text-xs font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-sm">
            </div>

            {{-- TAB 1: KARYAWAN CABANG --}}
            <div x-show="activeTab === 'cabang'" class="space-y-4 animate-in fade-in slide-in-from-bottom-4 duration-500">
                @forelse($karyawans as $item)
                    <a href="{{ route('admin-global.karyawan.detail', [$kolaborasi->id, $item->kode_karyawan]) }}"
                        class="bg-white/80 backdrop-blur-md rounded-[2rem] p-4 border border-white shadow-sm flex items-center justify-between group active:scale-[0.98] transition-all"
                        x-show="'{{ strtolower($item->nama_karyawan) }}'.includes(search.toLowerCase()) || search === ''">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-14 h-14 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-50 shadow-inner flex items-center justify-center">
                                @if ($item->foto_path)
                                    <img src="{{ Storage::url($item->foto_path) }}" class="w-full h-full object-cover">
                                @else
                                    <span
                                        class="text-slate-400 font-black text-sm">{{ substr($item->nama_karyawan, 0, 2) }}</span>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-sm font-black text-slate-800 leading-tight">{{ $item->nama_karyawan }}</h4>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="px-2 py-0.5 bg-teal-50 text-teal-700 text-[8px] font-black uppercase rounded">{{ $item->peran }}</span>
                                    <span class="text-[9px] font-bold text-slate-400">{{ $item->no_telp }}</span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 group-hover:bg-teal-50 group-hover:text-teal-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                @empty
                    {{-- Empty State --}}
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                            <span class="text-slate-300 text-5xl">👥</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-700 mb-2">Tidak Ada Karyawan</h3>
                        <p class="text-sm text-slate-400 max-w-xs mx-auto">Karyawan yang terhubung dengan cabang ini
                            akan muncul di sini setelah ditambahkan.</p>
                    </div>
                @endforelse
            </div>

            {{-- TAB 2: PETAKAN KARYAWAN (Multi-select) --}}
            <div x-show="activeTab === 'mapping'" class="space-y-4 animate-in fade-in slide-in-from-bottom-4 duration-500"
                x-cloak>
                <div class="flex justify-between items-center px-1">
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Karyawan Tersedia</p>
                    <button x-show="selectedIds.length > 0" @click="selectedIds = []"
                        class="text-[10px] font-black text-rose-500 uppercase italic">Batalkan Semua</button>
                </div>

                @forelse($otherKaryawans as $item)
                    <div @click="toggleSelect({{ $item->id }})"
                        :class="selectedIds.includes({{ $item->id }}) ? 'border-teal-500 bg-teal-50/50' :
                            'bg-white/80 border-white'"
                        class="p-4 rounded-[2rem] border shadow-sm flex items-center justify-between transition-all cursor-pointer select-none active:scale-[0.98]"
                        x-show="'{{ strtolower($item->nama_karyawan) }}'.includes(search.toLowerCase()) || search === ''">

                        <div class="flex items-center gap-4">
                            {{-- Checkbox Visual --}}
                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors"
                                :class="selectedIds.includes({{ $item->id }}) ? 'bg-teal-600 border-teal-600' :
                                    'border-slate-200 bg-white'">
                                <svg x-show="selectedIds.includes({{ $item->id }})" class="w-3 h-3 text-white"
                                    fill="none" stroke="currentColor" stroke-width="4" viewBox="0 0 24 24">
                                    <path d="M5 13l4 4L19 7" />
                                </svg>
                            </div>

                            <div
                                class="w-12 h-12 rounded-xl bg-orange-50 border border-orange-100 overflow-hidden shrink-0 flex items-center justify-center">
                                @if ($item->foto_path)
                                    <img src="{{ Storage::url($item->foto_path) }}" class="w-full h-full object-cover">
                                @else
                                    <span
                                        class="text-orange-700 font-black text-xs">{{ substr($item->nama_karyawan, 0, 2) }}</span>
                                @endif
                            </div>

                            <div class="space-y-0.5">
                                <h4 class="text-sm font-black text-slate-800 leading-tight">{{ $item->nama_karyawan }}</h4>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">
                                    {{ $item->kolaborasi ? $item->kolaborasi->nama_kolaborasi : 'Umum' }}</p>
                            </div>
                        </div>

                        <span
                            class="px-2 py-1 bg-slate-100 text-slate-600 text-[8px] font-black uppercase rounded">{{ $item->peran }}</span>
                    </div>
                @empty
                    {{-- Empty State --}}
                @endforelse
            </div>
        </div>

        {{-- FLOATING ACTIONS --}}

        {{-- FAB for Add New (Only in Cabang Tab) --}}
        <div x-show="activeTab === 'cabang' && !isAllSelected"
            class="fixed bottom-28 left-1/2 -translate-x-1/2 w-full max-w-[430px] pointer-events-none px-6 z-40">
            <div class="flex justify-end pointer-events-auto">
                <a href="{{ route('admin-global.karyawan.create', $kolaborasi->id) }}"
                    class="w-14 h-14 bg-teal-950 text-white rounded-2xl flex items-center justify-center shadow-2xl shadow-teal-900/40 active:scale-90 transition-all animate-in zoom-in duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Floating Action Bar for Multi-Mapping (Only when items selected) --}}
        <div x-show="activeTab === 'mapping' && isAllSelected" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
            class="fixed bottom-28 left-1/2 -translate-x-1/2 w-full max-w-[430px] px-6 z-50">
            <form action="{{ route('admin-global.karyawan.map', $kolaborasi->id) }}" method="POST">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="employee_ids[]" :value="id">
                </template>

                <button type="submit"
                    class="w-full py-4 bg-teal-800 text-white rounded-2xl shadow-2xl shadow-teal-900/40 flex items-center justify-center gap-3 active:scale-95 transition-all">
                    <span class="text-sm font-black uppercase tracking-[0.1em]">Petakan <span
                            x-text="selectedIds.length"></span> Karyawan</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </button>
            </form>
        </div>

        <x-navigation.admin-global-navbar active="cabang" />
    </x-layouts.mobile-app>
@endsection
