@extends('components.layouts.app')

@section('title', 'Daftar Terapis')

@section('content')

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen">

        {{-- MOVE x-data HERE to wrap everything --}}
        <div class="px-6 pt-8 pb-32 space-y-10" x-data="{
            showLokasi: false,
            openBranch: false,
            selectedKolaborasi: 'Semua Kolaborasi',
            lokasiTerpilih: 'Semua Lokasi',
            searchLokasi: '',
            searchKolaborasi: '',
            searchQuery: '',
            selectedDate: '',
        
            daftarLokasi: {{ json_encode($uniqueCities->values()) }},
            daftarKolaborasis: {{ json_encode($allKolaborasis->pluck('nama_kolaborasi')->values()) }},
        
            get filteredLokasi() {
                return this.daftarLokasi.filter(l => l.toLowerCase().includes(this.searchLokasi.toLowerCase()))
            },
        
            get filteredKolaborasis() {
                return this.daftarKolaborasis.filter(k => k.toLowerCase().includes(this.searchKolaborasi.toLowerCase()));
            },
        
            formatDate(dateStr) {
                if (!dateStr) return 'Pilih Tanggal';
                const date = new Date(dateStr);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            },
        
            shouldShow(kolaborasiName, therapistName, services, kotaKolaborasi, availableSessions) {
                // 1. Filter by Branch
                const matchesKolaborasi = this.selectedKolaborasi === 'Semua Kolaborasi' || kolaborasiName === this.selectedKolaborasi;
        
                // 2. Filter by City
                const matchesLokasi = this.lokasiTerpilih === 'Semua Lokasi' || kotaKolaborasi.toLowerCase() === this.lokasiTerpilih.toLowerCase();
        
                // 3. Filter by Search (name + layanan only)
                const searchLower = this.searchQuery.toLowerCase().trim();
                const matchesSearch = !searchLower ||
                    therapistName.toLowerCase().includes(searchLower) ||
                    services.some(s => s.toLowerCase().includes(searchLower));
        
                // 4. Filter by Date: if a date is selected, therapist must have a session on that date
                let matchesDate = true;
                if (this.selectedDate !== '') {
                    if (availableSessions && availableSessions.length > 0) {
                        matchesDate = availableSessions.some(s => s.startsWith(this.selectedDate));
                    } else {
                        matchesDate = false;
                    }
                }
        
                return matchesKolaborasi && matchesLokasi && matchesSearch && matchesDate;
            }
        }">

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
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    {{-- Added x-model here --}}
                    <input type="text" x-model="searchQuery" placeholder="Cari terapis atau layanan..."
                        class="w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl text-base font-medium focus:border-teal-500 outline-none shadow-sm">
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <!-- Filter Tanggal (Redesigned to match other filters) -->
                    {{-- <div class="relative">
                        <div class="w-full flex items-center justify-between px-5 py-4 bg-white border border-slate-200 rounded-2xl shadow-sm text-sm font-semibold transition-all"
                            :class="selectedDate ? 'text-slate-700 border-teal-500 ring-4 ring-teal-500/5' : 'text-slate-700'">

                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span x-text="formatDate(selectedDate)"></span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button x-show="selectedDate" @click.stop="selectedDate = ''" type="button"
                                    class="p-1 text-slate-300 hover:text-rose-500 transition-colors z-20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <svg class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <input type="date" x-model="selectedDate"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    </div> --}}
                    <div class="relative">
                        <button @click="showLokasi = !showLokasi"
                            class="w-full flex items-center justify-between px-5 py-4 bg-white border border-slate-200 rounded-2xl shadow-sm text-sm font-semibold text-slate-700">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span x-text="lokasiTerpilih"></span>
                            </div>
                            <svg class="w-5 h-5 text-slate-300 transition-transform" :class="showLokasi ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Searchable Dropdown --}}
                        <div x-show="showLokasi" @click.outside="showLokasi = false" x-cloak
                            class="absolute mt-2 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 z-40 p-3 space-y-3">

                            <input type="text" x-model="searchLokasi" placeholder="Cari kota..."
                                class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-sm font-medium outline-none">

                            <div class="max-h-48 overflow-y-auto space-y-1 custom-scrollbar">

                                <!-- ADD THIS BUTTON MANUALLY -->
                                <button @click="lokasiTerpilih = 'Semua Lokasi'; showLokasi = false"
                                    class="w-full text-left px-4 py-3 text-sm font-semibold hover:bg-teal-50 hover:text-teal-700 rounded-xl transition-all"
                                    :class="lokasiTerpilih === 'Semua Lokasi' ? 'bg-teal-50 text-teal-700' : 'text-slate-600'">
                                    Semua Kota
                                </button>

                                <template x-for="lokasi in filteredLokasi" :key="lokasi">
                                    <button @click="lokasiTerpilih = lokasi; showLokasi = false"
                                        class="w-full text-left px-4 py-3 text-sm font-semibold hover:bg-teal-50 hover:text-teal-700 rounded-xl transition-all"
                                        :class="lokasiTerpilih === lokasi ? 'bg-teal-50 text-teal-700' : 'text-slate-600'"
                                        x-text="lokasi"></button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Branch Filter Dropdown (Kolaborasi) -->
                    <div class="relative">
                        <button @click="openBranch = !openBranch; showLokasi = false"
                            class="w-full flex items-center justify-between px-5 py-4 bg-white border border-slate-200 rounded-2xl shadow-sm text-sm font-semibold text-slate-700">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span x-text="selectedKolaborasi"></span>
                            </div>
                            <svg class="w-5 h-5 text-slate-300 transition-transform"
                                :class="openBranch ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="openBranch" @click.outside="openBranch = false" x-cloak
                            class="absolute mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 z-50 p-2 overflow-hidden">

                            <!-- Search Input inside Dropdown -->
                            <div class="p-2 border-b border-slate-50">
                                <input type="text" x-model="searchKolaborasi" placeholder="Cari Kolaborasi..."
                                    class="w-full px-4 py-2 bg-slate-50 border-none rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-teal-500/20">
                            </div>

                            <div class="max-h-60 overflow-y-auto p-1 space-y-1 custom-scrollbar">
                                <!-- Manual Reset Button -->
                                <button
                                    @click="selectedKolaborasi = 'Semua Kolaborasi'; openBranch = false; searchKolaborasi = ''"
                                    class="w-full text-left px-4 py-3 text-sm font-semibold rounded-xl hover:bg-teal-50"
                                    :class="selectedKolaborasi === 'Semua Kolaborasi' ? 'bg-teal-50 text-teal-700' :
                                        'text-slate-600'">
                                    Semua Kolaborasi
                                </button>

                                <!-- Dynamic Search Results -->
                                <template x-for="kolab in filteredKolaborasis" :key="kolab">
                                    <button @click="selectedKolaborasi = kolab; openBranch = false; searchKolaborasi = ''"
                                        class="w-full text-left px-4 py-3 text-sm font-semibold hover:bg-teal-50 hover:text-teal-700 rounded-xl transition-all"
                                        :class="selectedKolaborasi === kolab ? 'bg-teal-50 text-teal-700' : 'text-slate-600'"
                                        x-text="kolab">
                                    </button>
                                </template>

                                <!-- Empty State -->
                                <div x-show="filteredKolaborasis.length === 0"
                                    class="px-4 py-3 text-xs text-slate-400 text-center">
                                    Kolaborasi tidak ditemukan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. THERAPIST LIST --}}
            <div class="grid grid-cols-1 gap-8">
                @foreach ($terapis as $t)
                    @php
                        $layanans = $t->layanans->pluck('nama')->toArray();
                        $namaKolaborasi = $t->kolaborasi ? $t->kolaborasi->nama_kolaborasi : 'Anjali';
                        $alamatKolaborasi = $t->kolaborasi ? $t->kolaborasi->alamat_kolaborasi : ''; // used for the Location filter
                        $kotaKolaborasi = $t->kolaborasi ? $t->kolaborasi->kota_kolaborasi : '';

                        // 1. Force the timezone to match your location (Asia/Jakarta is WIB)
                        $now = \Carbon\Carbon::now('Asia/Jakarta');
                        $todayStr = $now->toDateString(); // e.g., '2026-05-19'
                        $currentTimeStr = $now->toTimeString(); // e.g., '14:15:00'

                        // 2. Filter sessions with explicit logic
                        $nextSession = $t->sessions
                            ->filter(function ($session) use ($todayStr, $currentTimeStr) {
                                // A session is valid if:
                                // Condition A: It's a future date
        $isFutureDate = $session->tanggal_sesi > $todayStr;

        // Condition B: It's today, but the start time hasn't passed yet
        $isTodayButUpcoming =
            $session->tanggal_sesi === $todayStr && $session->waktu_mulai > $currentTimeStr;

        // Condition C: It's open and has slots
                                $isAvailable = $session->status === 'terbuka' && $session->remaining_capacity > 0;

                                return ($isFutureDate || $isTodayButUpcoming) && $isAvailable;
                            })
                            ->sortBy(function ($session) {
                                return $session->tanggal_sesi . ' ' . $session->waktu_mulai;
                            })
                            ->first();

                        // 3. Display Logic
                        if ($nextSession) {
                            $slots = $nextSession->remaining_capacity;
                            $date = \Carbon\Carbon::parse($nextSession->tanggal_sesi);

                            // Determine Pagi/Siang/Malam based on hour
                            $hour = (int) substr($nextSession->waktu_mulai, 0, 2);
                            if ($hour < 12) {
                                $timeType = 'Pagi';
                            } elseif ($hour < 16) {
                                $timeType = 'Siang';
                            } elseif ($hour < 18) {
                                $timeType = 'Sore';
                            } else {
                                $timeType = 'Malam';
                            }

                            if ($date->isToday()) {
                                $dayLabel = 'Hari ini';
                            } elseif ($date->isTomorrow()) {
                                $dayLabel = 'Besok';
                            } else {
                                $dayLabel = $date->translatedFormat('D');
                            }

                            $nextTime = $dayLabel . ', ' . substr($nextSession->waktu_mulai, 0, 5) . ' ' . $timeType;
                        } else {
                            $slots = 0;
                            $nextTime = 'Penuh / Tutup';
                        }
                        $isBookable = $nextSession && $slots > 0;

                        $rating = $t->nilai_review ?: '5.0';
                        $harga = $t->layanans->min('base_harga') ?: 150000;
                        $img = $t->foto
                                ? 'data:' . ($t->foto_mime ?? 'image/jpg') . ';base64,' . $t->foto
                                : asset('images/logo_anjali.jpg'); 

                        // Build a flat array of "YYYY-MM-DD HH:MM" strings for all open future sessions
                        $availableSessions = $t->sessions
                            ->filter(function ($session) use ($todayStr, $currentTimeStr) {
                                $isFutureDate = $session->tanggal_sesi > $todayStr;
                                $isTodayButUpcoming =
                                    $session->tanggal_sesi === $todayStr && $session->waktu_mulai > $currentTimeStr;
                                $isAvailable = $session->status === 'terbuka' && $session->remaining_capacity > 0;
                                return ($isFutureDate || $isTodayButUpcoming) && $isAvailable;
                            })
                            ->map(fn($s) => $s->tanggal_sesi . ' ' . substr($s->waktu_mulai, 0, 5))
                            ->values()
                            ->toArray();
                    @endphp

                    <div x-show="shouldShow('{{ $namaKolaborasi }}', '{{ addslashes($t->nama_karyawan) }}', @js($layanans), '{{ $kotaKolaborasi }}', @js($availableSessions))"
                        x-transition
                        class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 relative group transition-all duration-300"
                        :class="!{{ $isBookable ? 'true' : 'false' }} ? 'opacity-75' : ''">

                        <div class="flex flex-col items-center">
                            <div class="relative mb-6">
                                <div
                                    class="absolute -top-3 left-1/2 -translate-x-1/2 z-10 whitespace-nowrap bg-teal-600 text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest shadow-lg">
                                    {{ $namaKolaborasi }}
                                </div>

                                <div
                                    class="w-32 h-32 rounded-[2.5rem] bg-slate-50 overflow-hidden border-2 border-white shadow-md">
                                    <img src="{{ $img }}"
                                        class="w-full h-full object-cover {{ !$isBookable ? 'grayscale' : '' }}">
                                </div>

                                <div
                                    class="absolute -bottom-2 -right-1 bg-white px-3 py-1 rounded-full shadow-md border border-slate-50 flex items-center gap-1.5">
                                    @if ($isBookable)
                                        <div
                                            class="w-2 h-2 rounded-full {{ $slots <= 1 ? 'bg-orange-500' : 'bg-emerald-500' }}">
                                        </div>
                                        <span class="text-[11px] font-bold text-slate-700 uppercase">{{ $slots }}
                                            Slot Tersisa</span>
                                    @else
                                        <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                                        <span class="text-[11px] font-bold text-rose-600 uppercase">Penuh / Tutup</span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-center space-y-1.5">
                                <h4 class="text-xl font-bold text-slate-800 tracking-tight">{{ $t->nama_karyawan }}</h4>
                                <p class="text-xs font-semibold text-teal-600 uppercase tracking-widest">Spesialis
                                    Akupunktur</p>

                                <div class="flex items-center justify-center gap-1 text-slate-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-xs font-medium">{{ $alamatKolaborasi }}, {{ $kotaKolaborasi }}</p>
                                </div>
                            </div>

                            {{-- Hover Layanan Logic --}}
                            <div class="mt-5 flex flex-wrap justify-center gap-2">
                                {{-- Visible Service Pills --}}
                                @foreach (collect($layanans)->take(2) as $layanan)
                                    <span
                                        class="px-3 py-1.5 bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider rounded-lg border border-slate-100 shadow-sm">
                                        {{ $layanan }}
                                    </span>
                                @endforeach

                                {{-- The "+X" Badge and Better Popover --}}
                                @if (count($layanans) > 2)
                                    <div class="relative" x-data="{ showAll: false }">
                                        {{-- Trigger Badge --}}
                                        <button type="button" @mouseenter="showAll = true" @mouseleave="showAll = false"
                                            @click="showAll = !showAll"
                                            class="cursor-pointer px-2.5 py-1.5 bg-teal-50 text-teal-700 text-[10px] font-extrabold uppercase rounded-lg border border-teal-100 hover:bg-teal-100 transition-colors">
                                            +{{ count($layanans) - 2 }}
                                        </button>

                                        {{-- Refined Popover Card --}}
                                        <div x-show="showAll" x-cloak
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-56 bg-white border border-slate-100 rounded-2xl z-50 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] overflow-hidden">

                                            {{-- Header --}}
                                            <div class="bg-slate-50/50 px-4 py-2.5 border-b border-slate-100">
                                                <p class="text-[10px] font-bold text-teal-600 uppercase tracking-widest">
                                                    Layanan Lainnya</p>
                                            </div>

                                            {{-- List --}}
                                            <ul class="p-2 space-y-1">
                                                @foreach (collect($layanans)->slice(2) as $l_item)
                                                    <li
                                                        class="flex items-center gap-2 px-2 py-1.5 rounded-xl hover:bg-slate-50 transition-colors">
                                                        <div
                                                            class="shrink-0 w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,122,120,0.4)]">
                                                        </div>
                                                        <span
                                                            class="text-xs font-semibold text-slate-600 truncate">{{ $l_item }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            {{-- Little Triangle Pointer --}}
                                            <div
                                                class="absolute top-full left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-r border-b border-slate-100 rotate-45 -mt-1.5">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-3 flex items-center justify-center gap-1">
                                <div class="flex items-center text-amber-400">
                                    @for ($i = 0; $i < 5; $i++)
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-xs font-bold text-slate-700 ml-1">{{ $rating }}</span>
                            </div>
                        </div>

                        <div
                            class="mt-6 px-5 py-3 w-full bg-slate-50 rounded-2xl flex items-center justify-between border {{ $isBookable ? 'bg-slate-50 border-slate-100' : 'bg-rose-50 border-rose-100' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 {{ $isBookable ? 'text-teal-500' : 'text-rose-500' }}" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span
                                    class="text-xs font-bold {{ $isBookable ? 'text-slate-500' : 'text-rose-500' }} uppercase">{{ $isBookable ? 'Jadwal Selanjutnya' : 'Status' }}</span>
                            </div>
                            <span
                                class="text-xs font-bold {{ $isBookable ? 'text-slate-800' : 'text-rose-700' }} capitalize">{{ $nextTime }}</span>
                        </div>

                        @if ($isBookable)
                            <a href="{{ route('patient.booking.form', ['therapist_id' => $t->id]) }}"
                                class="mt-5 block text-center w-full py-4 bg-teal-700 text-white rounded-2xl text-sm font-bold uppercase tracking-widest active:translate-y-1 transition-all">
                                Buat Janji
                            </a>
                        @else
                            <button type="button"
                                class="mt-5 block text-center w-full py-4 bg-slate-500 text-white rounded-2xl text-sm font-bold uppercase tracking-widest active:translate-y-1 transition-all"
                                disabled>
                                Jadwal Tidak Tersedia
                            </button>
                        @endif
                    </div>
            </div>
            @endforeach
        </div>
        </div>

        <x-navigation.patient-navbar active="booking" />

    </x-layouts.mobile-app>

@endsection
