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

    <script>
        window.allServices = @js($allServices);
        window.uniqueServices = @js($uniqueServices);
        window.uniqueCities = @js($uniqueCities);
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

        /* Memperhalus pergerakan di mobile */
        [x-ref="slider"] {
            user-select: none;
            -webkit-user-drag: none;
        }
    </style>

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{
        searchQuery: '',
        lokasiTerpilih: 'Semua Lokasi',
        activeCategory: 'Semua',
        showLokasi: false,
        searchLokasi: '',
    
        // Data dari Controller
        daftarLokasi: window.uniqueCities,
        daftarServices: window.uniqueServices,
    
        get filteredLokasi() {
            return this.daftarLokasi.filter(l => l.toLowerCase().includes(this.searchLokasi.toLowerCase()))
        },
    
        // Logika Filter Utama
        shouldShow(nama, city, services) {
            const matchSearch = nama.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchLokasi = this.lokasiTerpilih === 'Semua Lokasi' || city === this.lokasiTerpilih;
            const matchCategory = this.activeCategory === 'Semua' || services.includes(this.activeCategory);
    
            return matchSearch && matchLokasi && matchCategory;
        }
    }">
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
            <div class="space-y-6">
                {{-- Search Bar --}}
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

                {{-- Category Carousel (Fixed Alignment) --}}
                <div class="space-y-3" x-data="{
                    isDown: false,
                    startX: 0,
                    scrollLeft: 0,
                
                    startDragging(e) {
                        this.isDown = true;
                        this.startX = e.pageX - $refs.slider.offsetLeft;
                        this.scrollLeft = $refs.slider.scrollLeft;
                    },
                    stopDragging() {
                        this.isDown = false;
                    },
                    move(e) {
                        if (!this.isDown) return;
                        e.preventDefault();
                        const x = e.pageX - $refs.slider.offsetLeft;
                        const walk = (x - this.startX) * 2;
                        $refs.slider.scrollLeft = this.scrollLeft - walk;
                    }
                }">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Kategori Layanan</p>

                    {{-- Wrapper dengan -mx-6 untuk menjangkau pinggir layar HP --}}
                    <div class="relative -mx-6 overflow-hidden">
                        <div x-ref="slider" @mousedown="startDragging($event)" @mouseleave="stopDragging()"
                            @mouseup="stopDragging()" @mousemove="move($event)" {{-- px-6 di sini memastikan item pertama dan terakhir sejajar dengan konten lainnya --}}
                            class="flex gap-3 overflow-x-auto no-scrollbar pb-4 px-6 cursor-grab active:cursor-grabbing select-none"
                            style="-webkit-overflow-scrolling: touch; scroll-behavior: auto;">

                            {{-- Tombol Semua --}}
                            <button @click="activeCategory = 'Semua'"
                                :class="activeCategory === 'Semua' ?
                                    'bg-teal-600 text-white shadow-lg shadow-teal-600/20 border-transparent' :
                                    'bg-white text-slate-600 border-slate-200'"
                                class="shrink-0 px-8 py-3 border rounded-2xl text-sm font-bold transition-all">
                                Semua
                            </button>

                            {{-- Looping Services --}}
                            @foreach ($uniqueServices as $service)
                                <button @click="activeCategory = '{{ $service }}'"
                                    :class="activeCategory === '{{ $service }}' ?
                                        'bg-teal-600 text-white shadow-lg shadow-teal-600/20 border-transparent' :
                                        'bg-white text-slate-600 border-slate-200'"
                                    class="shrink-0 px-8 py-3 border rounded-2xl text-sm font-bold transition-all">
                                    {{ $service }}
                                </button>
                            @endforeach

                            {{-- Spacer Akhir: Menambahkan sedikit ruang kosong di akhir agar tombol terakhir tidak menempel ke pinggir saat di-scroll mentok --}}
                            <div class="shrink-0 w-3"></div>
                        </div>
                    </div>
                </div>

                {{-- Advanced Filters --}}
                <div class="grid grid-cols-1 gap-4">
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
                            ? 'data:' . ($t->foto_mime ?? 'image/jpeg') . ';base64,' . $t->foto 
                            : 'https://ui-avatars.com/api/?name=' . urlencode($t->nama_karyawan) . '&background=0d766e&color=fff';
                    @endphp

                    <div x-show="shouldShow('{{ addslashes($t->nama_karyawan) }}', '{{ $kota }}', @js($layanans))"
                        x-transition.opacity
                        class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 group active:scale-[0.98] transition-all duration-300 relative">
                        <div class="flex flex-col items-center">
                            {{-- Profile Image with Slot Badge --}}
                            <div class="relative mb-6">
                                <div
                                    class="w-32 h-32 rounded-[2.5rem] bg-slate-50 overflow-hidden border-2 border-white shadow-md">
                                    <div class="relative h-32 w-32 rounded-[2.8rem] p-1.5 bg-white shadow-2xl border border-white/50 overflow-hidden">
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
                                <p class="text-xs font-semibold text-teal-600 uppercase tracking-widest">{{ $t->peran }}
                                </p>
                                <p class="text-sm font-medium text-slate-400">{{ $namaCabang }}, {{ $kota }}</p>
                            </div>

                            {{-- Kategori Layanan --}}
{{-- Kategori Layanan (Show 2 items max + Popover for more) --}}
<div class="mt-4 flex flex-wrap justify-center gap-2">
    @foreach (collect($layanans)->take(2) as $layanan)
        <span class="px-3 py-1.5 bg-slate-50 text-slate-500 text-[11px] font-semibold uppercase tracking-wider rounded-lg border border-slate-100">
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
                <i data-lucide="chevron-down" class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>

            {{-- Popover Dropdown --}}
            <div x-show="open" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-48 bg-white border border-slate-100 rounded-2xl shadow-2xl z-50 p-3 space-y-2 text-left"
                style="display: none;">
                
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 pb-1">
                    Layanan Lainnya
                </p>
                <div class="max-h-40 overflow-y-auto custom-scrollbar space-y-1.5">
                    @foreach (collect($layanans)->slice(2) as $other)
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-teal-400"></div>
                            <span class="text-xs font-bold text-slate-700">{{ $other }}</span>
                        </div>
                    @endforeach
                </div>
                
                {{-- Segitiga Penunjuk (Arrow) --}}
                <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-8 border-transparent border-t-white"></div>
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
                            <a href="{{ route('patient.booking.form', ['therapist_id' => $t->id]) }}"
                                class="mt-6 w-full py-4 bg-teal-700 text-white text-center rounded-2xl text-sm font-bold uppercase tracking-widest shadow-xl shadow-teal-700/20 active:shadow-none active:translate-y-1 transition-all">
                                Buat Janji
                            </a>
                        </div>
                    </div>
                @endforeach
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
