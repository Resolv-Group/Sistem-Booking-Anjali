@extends('components.layouts.app')

@section('title', 'Riwayat Janji Temu')

@section('content')

<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{
    activeTab: 'semua',
    search: '',
    allItems: {{ Js::from($mappedBookings) }},
    limit: 5,
    get filteredItems() {
        let items = this.allItems;

        // Filter by tab
        if (this.activeTab !== 'semua') {
            items = items.filter(i => i.status === this.activeTab);
        }

        // Filter by search
        if (this.search.trim() !== '') {
            const q = this.search.toLowerCase();
            items = items.filter(i => 
                i.nama.toLowerCase().includes(q) || 
                i.id.toString().includes(q)
            );
        }

        return items;
    },
    get displayedItems() {
        return this.filteredItems.slice(0, this.limit);
    },
    get hasMore() {
        return this.limit < this.filteredItems.length;
    },
    loadMore() {
        this.limit += 5;
    },
    goToDetail(item) {
        const ids = (item.allPeserta || [{ id: item.patient_id }]).map(p => p.id).join(',');
        window.location.href = '/admin-cabang/patient/' + item.patient_id + '?group=' + ids;
    }
}">

    {{-- 1. TOPBAR --}}
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
                            Riwayat Pasien
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

    <div class="px-6 pt-8 pb-32 space-y-8">

        {{-- 2. TITLE SECTION --}}
        <div class="space-y-2">
            <h2 class="text-3xl font-semibold text-teal-900 tracking-tight leading-tight">Riwayat Janji Temu</h2>
            <p class="text-sm text-slate-500 font-medium leading-relaxed">
                Riwayat dan histori janji temu yang sudah diproses.
            </p>
        </div>

        {{-- 3. SEARCH & FILTER --}}
        <div class="space-y-4">
            <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-300 group-focus-within:text-teal-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" placeholder="Cari nama pasien atau ID" x-model="search"
                    class="w-full pl-12 pr-4 py-3.5 bg-gray-100 border-none rounded-2xl text-sm font-semibold focus:ring-4 focus:ring-teal-50/50 transition-all outline-none">
            </div>

            <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
                <button @click="activeTab = 'semua'; limit = 5" :class="activeTab === 'semua' ? 'bg-teal-800 text-white shadow-lg shadow-teal-900/20' : 'bg-white text-slate-400 border border-slate-100'" class="px-7 py-2.5 rounded-full text-xs font-semibold uppercase tracking-widest transition-all">Semua</button>
                <button @click="activeTab = 'selesai'; limit = 5" :class="activeTab === 'selesai' ? 'bg-teal-800 text-white shadow-lg' : 'bg-white text-slate-400 border border-slate-100'" class="px-7 py-2.5 rounded-full text-xs font-semibold uppercase tracking-widest transition-all">Selesai</button>
                <button @click="activeTab = 'disetujui'; limit = 5" :class="activeTab === 'disetujui' ? 'bg-teal-800 text-white shadow-lg' : 'bg-white text-slate-400 border border-slate-100'" class="px-7 py-2.5 rounded-full text-xs font-semibold uppercase tracking-widest transition-all">Disetujui</button>
                <button @click="activeTab = 'ditolak'; limit = 5" :class="activeTab === 'ditolak' ? 'bg-teal-800 text-white shadow-lg' : 'bg-white text-slate-400 border border-slate-100'" class="px-7 py-2.5 rounded-full text-xs font-semibold uppercase tracking-widest transition-all">Ditolak</button>
                <button @click="activeTab = 'dibatalkan'; limit = 5" :class="activeTab === 'dibatalkan' ? 'bg-teal-800 text-white shadow-lg' : 'bg-white text-slate-400 border border-slate-100'" class="px-7 py-2.5 rounded-full text-xs font-semibold uppercase tracking-widest transition-all">Dibatalkan</button>
            </div>
        </div>

        {{-- 4. STATS CARDS --}}
        <div class="space-y-3">
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Total Janji Temu ({{ $monthName }})</p>
                    <h3 class="text-3xl font-semibold text-slate-800">{{ $totalThisMonth }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 flex items-center justify-center text-teal-600 border border-teal-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Tingkat Persetujuan</p>
                    <h3 class="text-3xl font-semibold text-slate-800">{{ $approvalRate }}%</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
        </div>

        {{-- 5. HISTORY LIST --}}
        <div class="space-y-6">
            <h3 class="text-[11px] font-semibold text-slate-400 uppercase tracking-[0.2em] ml-1">Pemesanan Sebelumnya</h3>
            
            {{-- Empty State --}}
            <template x-if="filteredItems.length === 0">
                <div class="text-center py-12 space-y-3">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Belum ada riwayat</p>
                    <p class="text-xs text-slate-400">Tidak ada data yang ditemukan untuk filter ini.</p>
                </div>
            </template>

            <template x-for="(item, index) in displayedItems" :key="item.id_raw">
                <div 
                    :class="{
                        'border-r-emerald-400 bg-gradient-to-l from-emerald-50/40 via-white to-white': item.status === 'disetujui',
                        'border-r-blue-400 bg-gradient-to-l from-blue-50/40 via-white to-white': item.status === 'selesai',
                        'border-r-rose-400 bg-gradient-to-l from-rose-50/40 via-white to-white': item.status === 'ditolak',
                        'border-r-slate-400 bg-gradient-to-l from-slate-50/40 via-white to-white': item.status === 'dibatalkan'
                    }"
                    class="bg-white rounded-[2rem] border-r-4 border border-slate-200/60 p-7 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden group">
                    
                    {{-- Session Type Badge --}}
                    <div class="absolute top-0 left-0">
                        <template x-if="item.tipe === 'Personal'">
                            <div class="bg-teal-500 text-white text-[8px] font-black uppercase tracking-[0.2em] px-3 py-1.5 rounded-br-xl flex items-center gap-1 shadow-sm">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Personal</span>
                            </div>
                        </template>
                        <template x-if="item.tipe === 'Group'">
                            <div class="bg-orange-500 text-white text-[8px] font-black uppercase tracking-[0.2em] px-3 py-1.5 rounded-br-xl flex items-center gap-1 shadow-sm">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Group</span>
                            </div>
                        </template>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <div class="w-14 h-14 rounded-2xl bg-teal-50 flex items-center justify-center text-teal-600 shrink-0">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-base font-semibold text-slate-800 truncate" x-text="item.nama"></h4>
                                    <template x-if="item.tipe === 'Group'">
                                        <button @click.stop="item.showPeserta = !item.showPeserta" 
                                            class="px-2 py-0.5 bg-orange-50 text-orange-600 text-[9px] font-bold rounded-full border border-orange-100 active:scale-90 transition-all">
                                            <span x-text="'+' + item.extra"></span>
                                        </button>
                                    </template>
                                </div>
                                <span class="text-[10px] font-semibold text-teal-600 bg-teal-50 px-2 py-0.5 rounded border border-teal-100 uppercase" x-text="'ID: #' + item.id"></span>
                            </div>
                            <div class="mt-2 space-y-1">
                                <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <span x-text="item.info"></span>
                                </div>
                                <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span x-text="item.waktu"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Expanded Participants List --}}
                    <template x-if="item.tipe === 'Group' && item.showPeserta">
                        <div x-show="item.showPeserta" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="bg-slate-50 rounded-xl p-3 border border-slate-100 space-y-2 mt-3">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Anggota Grup
                            </p>
                            <div class="grid grid-cols-1 gap-1.5">
                                <template x-for="p in item.peserta">
                                    <div @click="window.location.href = '/admin-cabang/patient/' + p.id + '?group=' + item.allPeserta.map(ap => ap.id).join(',')"
                                         class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-slate-100 shadow-sm cursor-pointer hover:bg-slate-50 transition-all active:scale-[0.98]">
                                        <div class="w-1 h-1 rounded-full bg-orange-400"></div>
                                        <span class="text-[11px] font-semibold text-slate-600" x-text="p.nama"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Rejection/Cancellation Reason --}}
                    <template x-if="(item.status === 'ditolak' || item.status === 'dibatalkan') && item.alasan_status">
                        <div class="mt-3 bg-rose-50/50 rounded-xl p-3 border border-rose-100">
                            <p class="text-[8px] font-black text-rose-400 uppercase tracking-widest mb-1">Alasan</p>
                            <p class="text-xs text-rose-600 font-medium" x-text="item.alasan_status"></p>
                        </div>
                    </template>

                    <div class="flex items-center justify-between pt-5 border-t border-slate-50">
                        {{-- Status Indicator --}}
                        <div>
                            <template x-if="item.status === 'disetujui'">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Disetujui</span>
                                </div>
                            </template>
                            <template x-if="item.status === 'ditolak'">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                                    <span class="text-[10px] font-bold text-rose-600 uppercase tracking-widest">Ditolak</span>
                                </div>
                            </template>
                            <template x-if="item.status === 'selesai'">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                    <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Selesai</span>
                                </div>
                            </template>
                            <template x-if="item.status === 'dibatalkan'">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-slate-400"></div>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Dibatalkan</span>
                                </div>
                            </template>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2">
                            <template x-if="item.status === 'selesai' || item.status === 'disetujui'">
                                <button @click="goToDetail(item)"
                                        class="flex items-center gap-2 px-4 py-2 bg-slate-50 text-slate-600 rounded-xl text-[10px] font-bold uppercase tracking-widest border border-slate-200 active:scale-95 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Detail
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Load More Section --}}
            <div class="pt-4 pb-12">
                <button 
                    x-show="hasMore"
                    @click="loadMore()"
                    class="w-full py-4 bg-white border-2 border-dashed border-slate-200 rounded-[1.5rem] text-xs font-bold text-slate-400 uppercase tracking-[0.2em] flex items-center justify-center gap-3 active:scale-95 transition-all">
                    <div class="flex items-center gap-2">
                        <span>Muat Lebih Banyak Riwayat</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </button>

                {{-- Nothing more to show --}}
                <div x-show="!hasMore && filteredItems.length > 0" x-cloak class="text-center py-6 space-y-2">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 text-slate-300">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tidak ada riwayat lagi</p>
                </div>
            </div>
        </div>

    </div>

    {{-- 7. BOTTOM NAVBAR --}}
    <x-navigation.admin-cabang-navbar active="booking" />

</x-layouts.mobile-app>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

@endsection