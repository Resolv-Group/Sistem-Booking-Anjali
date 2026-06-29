@extends('components.layouts.app')

@section('title', 'Daftar Terapis')

@section('content')

    {{-- 1. Persiapkan data kategori secara dinamis dari database --}}
    @php
        $allServices = [];
        foreach ($terapis as $t) {
            foreach ($t->layanans as $l) {
                $allServices[] = $l->nama;
            }
        }
        $uniqueServices = collect($allServices)->unique()->sort()->values();
    @endphp

    @php
        $therapistData = $terapis->map(function($t) {
            return [
                'nama' => $t->nama_karyawan,
                'kota' => $t->kolaborasi ? $t->kolaborasi->kota_kolaborasi : '',
                'layanans' => $t->layanans->pluck('nama')->toArray(),
            ];
        });
    @endphp
    <script>
        window.allServices = @js($allServices);
        window.uniqueServices = @js($uniqueServices);
        window.uniqueCities = @js($uniqueCities);
        window.allTherapistData = @js($therapistData);
    </script>

    <style>
        /* Menyembunyikan scrollbar tapi tetap bisa di-scroll */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }
    </style>

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{
        searchQuery: '',
        lokasiTerpilih: 'Semua Lokasi',
        selectedServices: [],
        showLokasi: false,
        showServices: false,
        searchLokasi: '',
        searchService: '',
    
        // Data dari Controller
        daftarLokasi: window.uniqueCities,
        daftarServices: window.uniqueServices,
    
        get filteredLokasi() {
            return this.daftarLokasi.filter(l => l.toLowerCase().includes(this.searchLokasi.toLowerCase()))
        },

        get filteredServices() {
            return this.daftarServices.filter(s => s.toLowerCase().includes(this.searchService.toLowerCase()))
        },

        toggleService(service) {
            const idx = this.selectedServices.indexOf(service);
            if (idx === -1) {
                this.selectedServices.push(service);
            } else {
                this.selectedServices.splice(idx, 1);
            }
        },

        get serviceLabel() {
            if (this.selectedServices.length === 0) return 'Semua Layanan';
            if (this.selectedServices.length === 1) return this.selectedServices[0];
            return this.selectedServices[0] + ' +' + (this.selectedServices.length - 1) + ' lainnya';
        },
    
        // Data terapis untuk menghitung visibleCount
        allTherapists: window.allTherapistData || [],

        // Logika Filter Utama
        shouldShow(nama, city, services) {
            const matchSearch = nama.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchLokasi = this.lokasiTerpilih === 'Semua Lokasi' || city === this.lokasiTerpilih;
            const matchCategory = this.selectedServices.length === 0 || this.selectedServices.some(s => services.includes(s));
    
            return matchSearch && matchLokasi && matchCategory;
        },

        get visibleCount() {
            return this.allTherapists.filter(t =>
                this.shouldShow(t.nama, t.kota, t.layanans)
            ).length;
        }
    }">
    {{-- 1. TOPBAR --}}
        <nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-100/80 px-6 py-4">
            <div class="flex items-center justify-between">
                
                {{-- Left: Navigation & Context --}}
                <div class="flex items-center gap-4">
                    {{-- Tombol Back/Menu dengan Hitbox Luas --}}
                    {{-- <a href="javascript:void(0)" onclick="window.history.back()" 
                    class="group flex items-center justify-center w-10 h-10 bg-white border border-slate-100 rounded-xl shadow-sm hover:bg-teal-50 transition-all active:scale-90">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" />
                        </svg>
                    </a> --}}
                    
                    <div class="flex flex-col">
                        {{-- Nama Cabang/Kolaborasi --}}
                        <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">
                            {{-- {{ $sessions[0]['kolaborasi'] ?? 'Rumah Terapi Anjali' }} --}}
                            ANJALI SADINA MULYO
                        </span>
                        <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none uppercase">
                            Daftar Terapis
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

        <div class="px-6 pt-8 pb-32 space-y-10">

            {{-- 2. HERO SECTION --}}
            <div class="space-y-3">
                <h2 class="text-3xl font-bold text-slate-800 leading-tight">
                    Spesialis <span class="text-teal-600 font-semibold">Akupunktur</span>
                </h2>
                <p class="text-base text-slate-500 leading-relaxed font-medium">
                    Pilih ahli profesional yang siap membantu pemulihan kesehatan Anda hari ini.
                </p>
            </div>

            {{-- 3. SEARCH & FILTERS --}}
            <div class="space-y-4">
                {{-- Search Bar --}}
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Cari Terapis</p>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-teal-500 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" x-model="searchQuery" placeholder="Cari terapis atau layanan..."
                        class="w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl text-base font-medium focus:border-teal-500 focus:ring-4 focus:ring-teal-50/50 transition-all outline-none shadow-sm">
                </div>

                {{-- Category Dropdown Multiselect --}}
                <div class="space-y-2">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Kategori Layanan</p>

                    <div class="relative">
                        {{-- Trigger Button --}}
                        <button @click="showServices = !showServices"
                            class="w-full flex items-center justify-between px-5 py-4 bg-white border border-slate-200 rounded-2xl shadow-sm text-sm font-semibold text-slate-700 transition-all"
                            :class="selectedServices.length > 0 ? 'border-teal-400 ring-2 ring-teal-100' : ''">
                            <div class="flex items-center gap-3">
                                <i data-lucide="layers" class="w-5 h-5 text-teal-500"></i>
                                <span x-text="serviceLabel" class="truncate max-w-[200px]"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                {{-- Badge jumlah terpilih --}}
                                <span x-show="selectedServices.length > 0"
                                    class="inline-flex items-center justify-center w-5 h-5 bg-teal-600 text-white text-[10px] font-black rounded-full"
                                    x-text="selectedServices.length"></span>
                                <i data-lucide="chevron-down" class="w-5 h-5 text-slate-300 transition-transform"
                                    :class="showServices ? 'rotate-180' : ''"></i>
                            </div>
                        </button>

                        {{-- Dropdown Panel --}}
                        <div x-show="showServices" @click.outside="showServices = false" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                            class="absolute mt-2 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 p-3 space-y-3"
                            style="display: none;">

                            {{-- Search inside dropdown --}}
                            <input type="text" x-model="searchService" placeholder="Cari layanan..."
                                class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-teal-500/20">

                            {{-- Clear all button --}}
                            <div class="flex items-center justify-between px-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Layanan</span>
                                <button @click="selectedServices = []" x-show="selectedServices.length > 0"
                                    class="text-[11px] font-bold text-rose-400 hover:text-rose-600 transition-colors">
                                    Hapus Semua
                                </button>
                            </div>

                            {{-- Options list --}}
                            <div class="max-h-52 overflow-y-auto space-y-1 custom-scrollbar">
                                <template x-for="service in filteredServices" :key="service">
                                    <button @click="toggleService(service)"
                                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-teal-50 hover:text-teal-700 transition-all text-left"
                                        :class="selectedServices.includes(service) ? 'bg-teal-50 text-teal-700' : ''">
                                        <div class="flex items-center gap-3">
                                            {{-- Custom checkbox --}}
                                            <span class="w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0 transition-all"
                                                :class="selectedServices.includes(service) ? 'bg-teal-600 border-teal-600' : 'border-slate-300'">
                                                <svg x-show="selectedServices.includes(service)" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                    <path d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </span>
                                            <span x-text="service"></span>
                                        </div>
                                    </button>
                                </template>

                                {{-- Empty state --}}
                                <p x-show="filteredServices.length === 0"
                                    class="text-center text-sm text-slate-400 py-4">
                                    Tidak ada layanan ditemukan.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Selected tags chip --}}
                    <div x-show="selectedServices.length > 0" class="flex flex-wrap gap-2 pt-1">
                        <template x-for="s in selectedServices" :key="s">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-600 text-white text-xs font-bold rounded-full">
                                <span x-text="s"></span>
                                <button @click="toggleService(s)" class="hover:opacity-70 transition-opacity">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </span>
                        </template>
                    </div>
                </div>

                {{-- Advanced Filters --}}
                <div class="grid grid-cols-1 gap-4">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Lokasi</p>
                    <div class="relative">
                        <button @click="showLokasi = !showLokasi"
                            class="w-full flex items-center justify-between px-5 py-4 bg-white border border-slate-200 rounded-2xl shadow-sm text-sm font-semibold text-slate-700">
                            <div class="flex items-center gap-3">
                                <i data-lucide="map-pin" class="w-5 h-5 text-teal-500"></i>
                                <span x-text="lokasiTerpilih"></span>
                            </div>
                            <i data-lucide="chevron-down" class="w-5 h-5 text-slate-300 transition-transform"
                                :class="showLokasi ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="showLokasi" @click.outside="showLokasi = false" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            class="absolute mt-2 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 z-40 p-3 space-y-3">
                            <input type="text" x-model="searchLokasi" placeholder="Cari kota..."
                                class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-teal-500/20">
                            <div class="max-h-48 overflow-y-auto space-y-1 custom-scrollbar">
                                <button @click="lokasiTerpilih = 'Semua Lokasi'; showLokasi = false"
                                    class="w-full text-left px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-teal-50 rounded-xl">Semua
                                    Lokasi</button>
                                <template x-for="lokasi in filteredLokasi" :key="lokasi">
                                    <button @click="lokasiTerpilih = lokasi; showLokasi = false"
                                        class="w-full text-left px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-teal-50 hover:text-teal-700 rounded-xl transition-all"
                                        x-text="lokasi"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. THERAPIST LIST (Dynamic from Database) --}}
            <div class="space-y-8">
                @foreach ($terapis as $t)
                    @php
                        $layanans = $t->layanans->pluck('nama')->toArray();
                        $kota = $t->kolaborasi ? $t->kolaborasi->kota_kolaborasi : '';
                        $namaCabang = $t->kolaborasi ? $t->kolaborasi->nama_kolaborasi : 'Rumah Terapi Anjali';

                        // Ambil sesi terdekat yang tersedia
                        $nextSession = $t->sessions->sortBy('tanggal_sesi')->sortBy('waktu_mulai')->first();
                        $slots = $nextSession ? $nextSession->remaining_capacity : 0;
                        $nextTime = $nextSession
                            ? \Carbon\Carbon::parse($nextSession->tanggal_sesi)->translatedFormat('D') .
                                ', ' .
                                substr($nextSession->waktu_mulai, 0, 5)
                            : 'Penuh';
                        $photoUrl = $t->foto
                                ? 'data:' . ($t->foto_mime ?? 'image/jpg') . ';base64,' . $t->foto
                                : asset('images/logo_anjali.jpg'); 
                    @endphp

                    <div x-show="shouldShow('{{ addslashes($t->nama_karyawan) }}', '{{ $kota }}', @js($layanans))"
                        x-transition.opacity
                        class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 group active:scale-[0.98] transition-all duration-300 relative">
                        <div class="flex flex-col items-center">
                            {{-- Profile Image with Slot Badge --}}
                            <div class="relative mb-6">
                                <div
                                    class="w-32 h-32 rounded-[2.5rem] bg-slate-50 overflow-hidden border-2 border-white shadow-md">
                                    <div
                                        class="relative h-32 w-32 rounded-[2.8rem] p-1.5 bg-white shadow-2xl border border-white/50 overflow-hidden">
                                        <img src="{{ $photoUrl }}"
                                            class="h-full w-full rounded-[2.4rem] object-cover hover:scale-110 transition-transform duration-700"
                                            alt="{{ $t->nama_karyawan }}">
                                    </div>
                                </div>
                                <div
                                    class="absolute -bottom-2 -right-1 bg-white px-3 py-1 rounded-full shadow-md border border-slate-50 flex items-center gap-1.5 animate-bounce-subtle">
                                    <div class="w-2 h-2 rounded-full {{ $slots <= 1 ? 'bg-rose-500' : 'bg-emerald-500' }}">
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-700 uppercase">{{ $slots }} Slot
                                        Tersisa</span>
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="text-center space-y-1.5">
                                <h4 class="text-xl font-bold text-slate-800 tracking-tight">{{ $t->nama_karyawan }}</h4>
                                <p class="text-xs font-semibold text-teal-600 uppercase tracking-widest">
                                    {{ $t->peran }}
                                </p>
                                <p class="text-sm font-medium text-slate-400">{{ $namaCabang }}, {{ $kota }}</p>
                            </div>

                            {{-- Kategori Layanan --}}
                            <div class="mt-4 flex flex-wrap justify-center gap-2">
                                @foreach (collect($layanans)->take(2) as $layanan)
                                    <span
                                        class="px-3 py-1.5 bg-slate-50 text-slate-500 text-[11px] font-semibold uppercase tracking-wider rounded-lg border border-slate-100">
                                        {{ $layanan }}
                                    </span>
                                @endforeach

                                @if (count($layanans) > 2)
                                    {{-- Alpine Component untuk Dropdown per Terapis --}}
                                    <div class="relative" x-data="{ open: false }">
                                        {{-- Tombol +N --}}
                                        <button @click="open = !open" @click.away="open = false" type="button"
                                            class="px-2 py-1.5 bg-teal-50 text-teal-600 text-[11px] font-bold uppercase rounded-lg border border-teal-100 active:scale-95 transition-all flex items-center gap-1">
                                            +{{ count($layanans) - 2 }}
                                            <i data-lucide="chevron-down" class="w-3 h-3 transition-transform"
                                                :class="open ? 'rotate-180' : ''"></i>
                                        </button>

                                        {{-- Popover Dropdown --}}
                                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-48 bg-white border border-slate-100 rounded-2xl shadow-2xl z-50 p-3 space-y-2 text-left"
                                            style="display: none;">

                                            <p
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 pb-1">
                                                Layanan Lainnya
                                            </p>
                                            <div class="max-h-40 overflow-y-auto custom-scrollbar space-y-1.5">
                                                @foreach (collect($layanans)->slice(2) as $other)
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-1.5 h-1.5 rounded-full bg-teal-400"></div>
                                                        <span
                                                            class="text-xs font-bold text-slate-700">{{ $other }}</span>
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Segitiga Penunjuk (Arrow) --}}
                                            <div
                                                class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-8 border-transparent border-t-white">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Availability Pill --}}
                            <div
                                class="mt-6 px-5 py-3 w-full bg-slate-50 rounded-2xl flex items-center justify-between border border-slate-100">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="calendar-days" class="w-4 h-4 text-teal-500"></i>
                                    <span class="text-xs font-bold text-slate-500 uppercase">Jadwal Terdekat</span>
                                </div>
                                <span class="text-xs font-bold text-slate-800">{{ $nextTime }}</span>
                            </div>

                            {{-- Action --}}
                            @if ($nextTime != 'Penuh')
                                <a href="{{ route('patient.booking.form', ['therapist_id' => $t->id]) }}"
                                    class="mt-6 w-full py-4 bg-teal-700 text-white text-center rounded-2xl text-sm font-bold uppercase tracking-widest shadow-xl shadow-teal-700/20 active:shadow-none active:translate-y-1 transition-all">
                                    Buat Janji
                                </a>
                            @else
                                <div class="mt-5 block text-center w-full py-4 bg-slate-500 text-white rounded-2xl text-sm font-bold uppercase tracking-widest active:translate-y-1 transition-all">
                                    Jadwal Tidak Tersedia
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Empty State: shown when no therapists match the current filters --}}
            <div x-show="visibleCount === 0" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="flex flex-col items-center justify-center py-16 px-6">

                {{-- Illustration --}}
                <div class="relative w-32 h-32 mb-6">
                    {{-- Background circle --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-teal-50 to-slate-50 rounded-full"></div>
                    {{-- Icon --}}
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    {{-- Decorative dots --}}
                    <div class="absolute -top-1 -right-1 w-3 h-3 bg-teal-200 rounded-full opacity-60"></div>
                    <div class="absolute -bottom-2 -left-2 w-4 h-4 bg-slate-200 rounded-full opacity-40"></div>
                </div>

                {{-- Message --}}
                <h3 class="text-lg font-bold text-slate-700 mb-2 text-center">
                    Terapis Tidak Ditemukan
                </h3>
                <p class="text-sm text-slate-400 text-center max-w-[260px] leading-relaxed mb-6"
                    x-text="
                    searchQuery.trim()
                        ? 'Tidak ada terapis yang cocok dengan pencarian \'' + searchQuery.trim() + '\'. Coba kata kunci lain.'
                        : 'Tidak ada terapis yang tersedia untuk filter yang dipilih. Coba ubah filter Anda.'
                ">
                </p>

                {{-- Reset Button --}}
                <button type="button"
                    @click="searchQuery = ''; selectedServices = []; lokasiTerpilih = 'Semua Lokasi'"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-teal-700 shadow-sm hover:bg-teal-50 hover:border-teal-200 active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset Filter
                </button>
            </div>

            {{-- 5. PREMIUM VALUE PROP --}}
            <div class="bg-teal-50/50 rounded-[2.5rem] p-8 border border-teal-100 space-y-6">
                <h3 class="text-2xl font-black text-teal-900 leading-tight">Kualitas layanan yang mengutamakan Anda.</h3>
                <p class="text-sm text-slate-600 leading-relaxed font-medium">Setiap spesialis kami telah melalui seleksi
                    ketat untuk menjamin keahlian dan empati tinggi.</p>

                <div class="w-full aspect-[4/3] rounded-3xl overflow-hidden shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&q=80&w=800"
                        class="w-full h-full object-cover">
                </div>
            </div>
        </div>

        <x-navigation.patient-navbar active="therapists" />

    </x-layouts.mobile-app>

    {{-- Inisialisasi Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E2E8F0;
            border-radius: 10px;
        }

        @keyframes bounce-subtle {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-3px);
            }
        }

        .animate-bounce-subtle {
            animation: bounce-subtle 3s ease-in-out infinite;
        }
    </style>

@endsection
