@extends('components.layouts.app')

@section('title', 'List Janji Temu')

@section('content')

<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen">

    {{-- 1. TOPBAR --}}
    <div class="px-6 py-5 flex justify-between items-center bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 shadow-sm">
        <h1 class="text-sm font-bold text-teal-800 uppercase tracking-widest leading-none">Rumah Terapi Anjali</h1>
        <div class="w-10 h-10 rounded-xl border-2 border-orange-200 p-0.5 bg-white shadow-sm">
            <img src="https://i.pravatar.cc/100?u=admin" class="w-full h-full rounded-lg object-cover">
        </div>
    </div>

    <div class="px-6 pt-8 pb-32 space-y-8">

        {{-- 2. TITLE SECTION --}}
        <div class="space-y-2 px-1">
            <h2 class="text-3xl font-semibold text-teal-900 tracking-tight leading-tight">List Janji Temu</h2>
            <p class="text-sm text-slate-500 font-medium leading-relaxed">
                Mengelola dan memverifikasi janji temu pasien yang masuk.
            </p>
        </div>

        {{-- 3. QUICK STATS --}}
        <div class="grid grid-cols-2 gap-4">
            {{-- Pending Card --}}
            <div class="bg-white p-5 rounded-[1.8rem] border border-slate-100 shadow-sm space-y-1">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest leading-tight">Status<br>Pending</p>
                <h3 class="text-3xl font-semibold text-orange-500">12</h3>
            </div>
            {{-- Total Card --}}
            <div class="bg-white p-5 rounded-[1.8rem] border border-slate-100 shadow-sm space-y-1">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest leading-tight">Total<br>Hari Ini</p>
                <div class="flex items-baseline gap-1">
                    <h3 class="text-3xl font-semibold text-teal-600">45</h3>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Pasien</span>
                </div>
            </div>
        </div>

        {{-- 4. SECTION HEADER --}}
        <div class="flex justify-between items-end px-1">
            <h3 class="text-lg font-semibold text-slate-800 tracking-tight">Antrian Saat Ini</h3>
            <a href="{{ route('therapist.booking.history') }}" class="text-xs font-semibold text-teal-600 flex items-center gap-1 hover:underline">
                Lihat Histori
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M13 7l5 5-5 5M6 7l5 5-5 5"/></svg>
            </a>
        </div>

        {{-- 5. APPOINTMENT LIST --}}
        <div x-data="{ 
            limit: 3, 
            loading: false, 
            finished: false,
            searchQuery: '',
            lokasiTerpilih: 'Semua Lokasi',
            layananTerpilih: 'Semua Layanan',
            items: [
                { nama: 'Yulian', id: 'SH-88218', status: 'paid', tipe: 'Personal', extra: 0, peserta: [], showPeserta: false, terapis: 'Dr. Elena', waktu: '13 Mei, 10:30 AM', lokasi: 'Surabaya', layanan: 'Akupunktur' },
                { nama: 'Jenni', id: 'SH-90210', status: 'unpaid', tipe: 'Personal', extra: 0, peserta: [], showPeserta: false, terapis: 'Dr. Elena', waktu: '13 Mei, 1:00 PM', lokasi: 'Sidoarjo', layanan: 'Fisioterapi' },
                { nama: 'CANTIKA', id: 'SH-98212', status: 'paid', tipe: 'Group', extra: 2, peserta: ['Anisa Putri', 'Bagus Setiawan'], showPeserta: false, terapis: 'Dr. Aris', waktu: '13 Mei, 02:30 AM', lokasi: 'Surabaya', layanan: 'Refleksi' },
                { nama: 'Budi', id: 'SH-99001', status: 'paid', tipe: 'Personal', extra: 0, peserta: [], showPeserta: false, terapis: 'Dr. Elena', waktu: '14 Mei, 09:00 AM', lokasi: 'Malang', layanan: 'Akupunktur' },
                { nama: 'Siska', id: 'SH-99002', status: 'unpaid', tipe: 'Group', extra: 3, peserta: ['Dedi Kurniawan', 'Eka Saputra', 'Fani Lestari'], showPeserta: false, terapis: 'Dr. Aris', waktu: '14 Mei, 11:30 AM', lokasi: 'Sidoarjo', layanan: 'Fisioterapi' }
            ],
            get filteredItems() {
                const query = this.searchQuery.toLowerCase().trim();
                const location = this.lokasiTerpilih;
                const service = this.layananTerpilih;

                return this.items.filter(item => {
                    const matchesSearch = !query ||
                        item.nama.toLowerCase().includes(query) ||
                        item.id.toLowerCase().includes(query) ||
                        item.terapis.toLowerCase().includes(query);

                    const matchesLocation = location === 'Semua Lokasi' || item.lokasi === location;
                    const matchesService = service === 'Semua Layanan' || item.layanan === service;

                    return matchesSearch && matchesLocation && matchesService;
                });
            },
            get finished() {
                return this.limit >= this.filteredItems.length;
            },
            loadMore() {
                this.loading = true;
                setTimeout(() => {
                    this.loading = false;
                    this.limit += 3;
                }, 500);
            }
        }" class="space-y-6">

            {{-- SEARCH & FILTERS --}}
            <div class="space-y-4">
                {{-- Search Input --}}
                <div class="relative group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" x-model="searchQuery" placeholder="Cari nama pasien, ID, atau terapis..."
                        class="w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl text-base font-medium focus:border-teal-500 outline-none shadow-sm">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    {{-- Lokasi Filter --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                            class="w-full flex items-center justify-between px-5 py-4 bg-white border border-slate-200 rounded-2xl shadow-sm text-sm font-semibold text-slate-700">
                            <span x-text="lokasiTerpilih"></span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                            class="absolute mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 z-50 p-2 space-y-1">
                            <template x-for="loc in ['Semua Lokasi', 'Surabaya', 'Sidoarjo', 'Malang']">
                                <button @click="lokasiTerpilih = loc; open = false" type="button"
                                    class="w-full text-left px-4 py-3 text-sm font-semibold rounded-xl hover:bg-teal-50 hover:text-teal-700"
                                    :class="lokasiTerpilih === loc ? 'bg-teal-50 text-teal-700' : 'text-slate-650'"
                                    x-text="loc"></button>
                            </template>
                        </div>
                    </div>

                    {{-- Layanan Filter --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                            class="w-full flex items-center justify-between px-5 py-4 bg-white border border-slate-200 rounded-2xl shadow-sm text-sm font-semibold text-slate-700">
                            <span x-text="layananTerpilih"></span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                            class="absolute mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 z-50 p-2 space-y-1">
                            <template x-for="lay in ['Semua Layanan', 'Akupunktur', 'Fisioterapi', 'Refleksi']">
                                <button @click="layananTerpilih = lay; open = false" type="button"
                                    class="w-full text-left px-4 py-3 text-sm font-semibold rounded-xl hover:bg-teal-50 hover:text-teal-700"
                                    :class="layananTerpilih === lay ? 'bg-teal-50 text-teal-700' : 'text-slate-650'"
                                    x-text="lay"></button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. SECTION HEADER --}}
            <div class="flex justify-between items-end px-1 pt-4">
                <h3 class="text-lg font-semibold text-slate-800 tracking-tight">Antrian Saat Ini</h3>
                <a href="{{ route('therapist.booking.history') }}" class="text-xs font-semibold text-teal-600 flex items-center gap-1 hover:underline">
                    Lihat Histori
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M13 7l5 5-5 5M6 7l5 5-5 5"/></svg>
                </a>
            </div>

            <template x-for="(item, index) in filteredItems.slice(0, limit)" :key="index">
                <div class="bg-white rounded-[2rem] border border-slate-200 p-7 shadow-sm space-y-6 relative overflow-hidden group">
                    
                    {{-- Session Type Badge --}}
                    <div class="absolute top-0 left-0">
                        <template x-if="item.tipe === 'Personal'">
                            <div class="bg-teal-500 text-white text-[9px] font-black uppercase tracking-[0.2em] px-4 py-2 rounded-br-2xl flex items-center gap-1.5 shadow-sm">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Personal Session</span>
                            </div>
                        </template>
                        <template x-if="item.tipe === 'Group'">
                            <div class="bg-orange-500 text-white text-[9px] font-black uppercase tracking-[0.2em] px-4 py-2 rounded-br-2xl flex items-center gap-1.5 shadow-sm">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Group Session</span>
                            </div>
                        </template>
                    </div>

                    {{-- Status Badge --}}
                    <div class="absolute top-10 right-6">
                        <template x-if="item.status === 'paid'">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-full border border-blue-100">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                                <span class="text-[9px] font-bold uppercase tracking-widest">Bukti Pembayaran</span>
                            </div>
                        </template>
                        <template x-if="item.status !== 'paid'">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-500 rounded-full border border-rose-100">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span class="text-[9px] font-bold uppercase tracking-widest">Belum Bayar</span>
                            </div>
                        </template>
                    </div>

                    {{-- Patient Info --}}
                    <div class="space-y-1">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Pasien</p>
                        <div class="flex items-center gap-2">
                            <h4 class="text-lg font-semibold text-teal-700" x-text="item.nama"></h4>
                            <template x-if="item.tipe === 'Group'">
                                <button @click.stop="item.showPeserta = !item.showPeserta" 
                                    class="px-2 py-0.5 bg-orange-50 text-orange-600 text-[10px] font-bold rounded-full border border-orange-100 active:scale-90 transition-all">
                                    <span x-text="'+' + item.extra"></span>
                                </button>
                            </template>
                        </div>
                        <p class="text-[11px] font-medium text-slate-400 font-mono tracking-tighter" x-text="'#' + item.id"></p>
                    </div>

                    {{-- Expanded Participants List --}}
                    <template x-if="item.tipe === 'Group' && item.showPeserta">
                        <div x-show="item.showPeserta" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-3">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Anggota Grup
                            </p>
                            <div class="grid grid-cols-1 gap-2">
                                <template x-for="name in item.peserta">
                                    <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-slate-100 shadow-sm">
                                        <div class="w-1.5 h-1.5 rounded-full bg-orange-400"></div>
                                        <span class="text-xs font-semibold text-slate-600" x-text="name"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Specialist Info --}}
                    <div class="space-y-1.5">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Spesialis</p>
                        <div class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <div class="w-6 h-6 rounded-md bg-teal-50 flex items-center justify-center text-teal-600">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <span x-text="item.terapis"></span>
                        </div>
                    </div>

                    {{-- Time Info --}}
                    <div class="space-y-1.5">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Tanggal & Waktu</p>
                        <p class="text-sm font-semibold text-slate-800" x-text="item.waktu"></p>
                    </div>

                    {{-- Buttons Actions --}}
                    <div class="space-y-3 pt-2">
                        <template x-if="item.status === 'paid'">
                            <button class="w-full py-4 bg-[#0F5A58] text-white rounded-xl text-[11px] font-bold uppercase tracking-widest flex items-center justify-center gap-2 shadow-lg shadow-teal-900/10 active:scale-95 transition-all">
                                🖼️ Lihat Bukti Pembayaran
                            </button>
                        </template>

                        <div class="flex gap-3">
                            <button class="flex-1 py-3.5 bg-emerald-500 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest flex items-center justify-center gap-2 active:scale-95 transition-all shadow-md shadow-emerald-500/20">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                                Terima Janji
                            </button>
                            <button class="flex-1 py-3.5 bg-white border border-rose-200 text-rose-500 rounded-xl text-[10px] font-bold uppercase tracking-widest flex items-center justify-center gap-2 active:scale-95 transition-all hover:bg-rose-50">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                Tolak Janji
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- EMPTY STATE UI --}}
            <div x-show="filteredItems.length === 0" x-cloak x-transition
                class="flex flex-col items-center justify-center py-12 px-6 text-center space-y-6 bg-white rounded-[2.5rem] border border-dashed border-slate-200">
                <div class="relative">
                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-8 h-8 bg-white rounded-full shadow-sm flex items-center justify-center">
                        <span class="text-lg">🔍</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <h3 class="text-xl font-bold text-slate-800">Janji Temu Tidak Ditemukan</h3>
                    <p class="text-sm text-slate-500 max-w-[240px] mx-auto">
                        Maaf, kami tidak menemukan janji temu yang sesuai dengan kriteria pencarian Anda.
                    </p>
                </div>

                <button @click="searchQuery = ''; lokasiTerpilih = 'Semua Lokasi'; layananTerpilih = 'Semua Layanan'" 
                    class="px-6 py-3 bg-teal-50 text-teal-700 text-sm font-bold rounded-2xl hover:bg-teal-100 transition-all active:scale-95">
                    Atur Ulang Semua Filter
                </button>
            </div>

            {{-- Load More Section --}}
            <div class="pt-4 pb-12" x-show="filteredItems.length > 0">
                <button 
                    x-show="!finished"
                    @click="loadMore()"
                    :disabled="loading"
                    class="w-full py-4 bg-white border-2 border-dashed border-slate-200 rounded-[1.5rem] text-xs font-bold text-slate-400 uppercase tracking-[0.2em] flex items-center justify-center gap-3 active:scale-95 transition-all disabled:opacity-50">
                    <template x-if="!loading">
                        <div class="flex items-center gap-2">
                            <span>Muat Lebih Banyak</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </template>
                    <template x-if="loading">
                        <div class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-teal-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-teal-600">Memuat...</span>
                        </div>
                    </template>
                </button>

                {{-- Nothing more to show --}}
                <div x-show="finished" x-cloak class="text-center py-6 space-y-2">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 text-slate-300">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tidak ada lagi data untuk ditampilkan</p>
                </div>
            </div>
        </div>

    </div>

    {{-- 6. BOTTOM NAVBAR --}}
    <x-navigation.therapist-navbar active="booking" />

</x-layouts.mobile-app>

@endsection