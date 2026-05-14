@extends('components.layouts.app')

@section('title', 'Daftar Terapis')

@section('content')

<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen">
    
    <x-ui.topbar title="Rumah Terapi Anjali">

        <x-slot:right>
            <a href="#">
                <img
                    src="https://i.pravatar.cc/100"
                    class="h-10 w-10 rounded-full object-cover"
                >
            </a>
        </x-slot:right>

    </x-ui.topbar>

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
        <div class="space-y-6" x-data="{ 
            showLokasi: false, 
            searchLokasi: '', 
            lokasiTerpilih: 'Semua Lokasi',
            daftarLokasi: ['Jakarta Pusat', 'Surabaya Timur', 'Malang City', 'Bandung Dago', 'Semarang'],
            get filteredLokasi() {
                return this.daftarLokasi.filter(l => l.toLowerCase().includes(this.searchLokasi.toLowerCase()))
            }
        }">
            {{-- Search Bar --}}
            <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400 group-focus-within:text-teal-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" placeholder="Cari terapis atau layanan..." 
                    class="w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl text-base font-medium focus:border-teal-500 focus:ring-4 focus:ring-teal-50/50 transition-all outline-none shadow-sm">
            </div>

            {{-- Advanced Filters --}}
            <div class="grid grid-cols-1 gap-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="relative bg-white border border-slate-200 rounded-2xl p-3 shadow-sm focus-within:border-teal-500 transition-all">
                        <label class="block text-[10px] font-bold text-teal-600 uppercase mb-1">Tanggal</label>
                        <input type="date" class="w-full bg-transparent text-sm font-semibold text-slate-700 outline-none" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="relative bg-white border border-slate-200 rounded-2xl p-3 shadow-sm focus-within:border-teal-500 transition-all">
                        <label class="block text-[10px] font-bold text-teal-600 uppercase mb-1">Jam</label>
                        <input type="time" class="w-full bg-transparent text-sm font-semibold text-slate-700 outline-none" value="09:00">
                    </div>
                </div>

                <div class="relative">
                    <button @click="showLokasi = !showLokasi" class="w-full flex items-center justify-between px-5 py-4 bg-white border border-slate-200 rounded-2xl shadow-sm text-sm font-semibold text-slate-700">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span x-text="lokasiTerpilih"></span>
                        </div>
                        <svg class="w-5 h-5 text-slate-300 transition-transform" :class="showLokasi ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    {{-- Searchable Dropdown --}}
                    <div x-show="showLokasi" @click.outside="showLokasi = false" x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        class="absolute mt-2 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 z-40 p-3 space-y-3">
                        <input type="text" x-model="searchLokasi" placeholder="Cari kota..." 
                            class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-teal-500/20">
                        <div class="max-h-48 overflow-y-auto space-y-1 custom-scrollbar">
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

        {{-- 4. THERAPIST LIST --}}
        <div class="space-y-8">
        @php
            $terapis = [
                [
                    'nama' => 'Dr. Elena, Sp. Ak', 
                    'slots' => 3, 
                    'img' => 'doc1', 
                    'layanan' => ['Akupunktur', 'Bekam Medis'],
                    'rating' => '4.9',
                    'reviews' => 124,
                    'harga' => 150000
                ],
                [
                    'nama' => 'Dr. Aris Budiman', 
                    'slots' => 1, 
                    'img' => 'doc2', 
                    'layanan' => ['Akupunktur', 'Refleksi', 'Konsultasi'],
                    'rating' => '4.8',
                    'reviews' => 89,
                    'harga' => 175000
                ],
                [
                    'nama' => 'Siti Aminah, M.Ak', 
                    'slots' => 5, 
                    'img' => 'doc3', 
                    'layanan' => ['Bekam Medis'],
                    'rating' => '5.0',
                    'reviews' => 210,
                    'harga' => 125000
                ],
            ];
        @endphp

        @foreach ($terapis as $t)
        <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 group active:scale-[0.98] transition-all duration-300">
            <div class="flex flex-col items-center">
                {{-- Profile Image with Slot Badge --}}
                <div class="relative mb-6">
                    <div class="w-32 h-32 rounded-[2.5rem] bg-slate-50 overflow-hidden border-2 border-white shadow-md">
                        <img src="https://i.pravatar.cc/150?u={{$t['img']}}" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-2 -right-1 bg-white px-3 py-1 rounded-full shadow-md border border-slate-50 flex items-center gap-1.5 animate-bounce-subtle">
                        <div class="w-2 h-2 rounded-full {{ $t['slots'] <= 1 ? 'bg-rose-500' : 'bg-emerald-500' }}"></div>
                        <span class="text-[11px] font-bold text-slate-700 uppercase">{{ $t['slots'] }} Slot Tersisa</span>
                    </div>
                </div>

                {{-- Info --}}
                <div class="text-center space-y-1.5">
                    <h4 class="text-xl font-bold text-slate-800 tracking-tight">{{ $t['nama'] }}</h4>
                    <p class="text-xs font-semibold text-teal-600 uppercase tracking-widest">Spesialis Akupunktur</p>
                    <p class="text-sm font-medium text-slate-400">Klinik Terapi Anjali, Surabaya</p>
                    
                    {{-- Review Stars (New) --}}
                    <div class="flex items-center justify-center gap-1 mt-1">
                        <div class="flex items-center text-amber-400">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span class="text-xs font-bold text-slate-700 ml-1">{{ $t['rating'] }}</span>
                        <span class="text-xs font-medium text-slate-400">({{ $t['reviews'] }} Review)</span>
                    </div>
                </div>

                {{-- Kategori Layanan (Show 2 items max) --}}
                <div class="mt-5 flex flex-wrap justify-center gap-2">
                    @foreach(collect($t['layanan'])->take(2) as $layanan)
                        <span class="px-3 py-1.5 bg-slate-50 text-slate-500 text-[11px] font-semibold uppercase tracking-wider rounded-lg border border-slate-100">
                            {{ $layanan }}
                        </span>
                    @endforeach
                    
                    @if(count($t['layanan']) > 2)
                        <span class="px-2 py-1.5 bg-teal-50 text-teal-600 text-[11px] font-bold uppercase rounded-lg border border-teal-100">
                            +{{ count($t['layanan']) - 2 }}
                        </span>
                    @endif
                </div>

                {{-- Availability Pill --}}
                <div class="mt-6 px-5 py-3 w-full bg-slate-50 rounded-2xl flex items-center justify-between border border-slate-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-xs font-bold text-slate-500 uppercase">Jadwal Terdekat</span>
                    </div>
                    <span class="text-xs font-bold text-slate-800">Sen, 10:00 AM</span>
                </div>

                {{-- Pricing (New) --}}
                <div class="mt-6 flex flex-col items-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-1">Mulai Dari</p>
                    <h5 class="text-lg font-bold text-slate-800 leading-none">Rp {{ number_format($t['harga'], 0, ',', '.') }}<span class="text-xs text-slate-400 font-medium ml-1">/ Sesi</span></h5>
                </div>

                {{-- Action --}}
                <a href="{{ route('patient.booking.form', ['therapist_id' => $loop->index + 1]) }}" class="mt-5 block text-center w-full py-4 bg-teal-700 text-white rounded-2xl text-sm font-bold uppercase tracking-widest shadow-xl shadow-teal-700/20 active:shadow-none active:translate-y-1 transition-all">
                    Buat Janji
                </a>
            </div>
        </div>
        @endforeach
    </div>

    </div>

    {{-- BOTTOM NAVBAR --}}
    <x-navigation.patient-navbar active="terapis" />

</x-layouts.mobile-app>

@endsection