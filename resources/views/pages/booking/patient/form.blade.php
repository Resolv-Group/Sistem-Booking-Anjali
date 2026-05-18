@extends('components.layouts.app')

@section('title', 'Perjanjian')

@section('content')

@php
    $patientName = auth()->user() ? auth()->user()->name : 'Pasien Utama';
    $patientPublicId = auth()->user() && auth()->user()->pasien ? auth()->user()->pasien->pasien_public_id : 'PSN-GUEST';
    
    $words = explode(' ', $therapist->nama_karyawan);
    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    $namaCabang = $therapist->cabang ? $therapist->cabang->nama_cabang : 'Rumah Terapi Anjali';
    $alamatCabang = $therapist->cabang ? $therapist->cabang->alamat : 'Surabaya';
@endphp

<script>
    window.bookingServices = @json($services);
    window.bookingSessions = @json($sessions);
</script>

<x-layouts.mobile-app class="bg-slate-50 min-h-screen" 
    x-data="{ 
        step: 1,
        slots: 1,
        selectedServices: [],
        searchService: '',
        services: window.bookingServices,
        sessions: window.bookingSessions,
        selectedDate: '',
        timeType: 'pagi', 
        selectedTime: '',
        selectedSessionId: null,
        
        init() {
            if (this.sessions && this.sessions.length > 0) {
                let dates = this.availableDates;
                if (dates.length > 0) {
                    this.selectedDate = dates[0];
                }
            }
        },

        get availableDates() {
            return [...new Set(this.sessions.map(s => s.tanggal_sesi))].sort();
        },

        get timeSlotsForSelectedDate() {
            let daySessions = this.sessions.filter(s => s.tanggal_sesi === this.selectedDate);
            let groups = { pagi: [], siang: [], malam: [] };
            
            daySessions.forEach(s => {
                let hour = parseInt(s.waktu_mulai.split(':')[0]);
                let slotInfo = { id: s.id, time: s.waktu_mulai, slots: s.kuota_sisa };
                
                if (hour < 12) {
                    groups.pagi.push(slotInfo);
                } else if (hour < 18) {
                    groups.siang.push(slotInfo);
                } else {
                    groups.malam.push(slotInfo);
                }
            });
            return groups;
        },
        
        // Verifikasi data
        accountNumber: '87923998',
        copied: false,
        copyToClipboard() {
            navigator.clipboard.writeText(this.accountNumber);
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        },
        
        toggleService(id) {
            if (this.selectedServices.includes(id)) {
                this.selectedServices = this.selectedServices.filter(i => i !== id);
            } else {
                this.selectedServices.push(id);
            }
        },

        get filteredServices() {
            if (!this.searchService) return this.services;
            return this.services.filter(s => s.name.toLowerCase().includes(this.searchService.toLowerCase()));
        },

        get selectedServicesNames() {
            return this.selectedServices.map(id => {
                let service = this.services.find(s => s.id === id);
                return service ? service.name : '';
            }).filter(n => n !== '').join(', ');
        },

        get totalConsultationCost() {
            return this.selectedServices.reduce((sum, id) => {
                let service = this.services.find(s => s.id === id);
                return sum + (service ? service.price : 0);
            }, 0);
        },

        get grandTotal() {
            return this.totalConsultationCost + 5000;
        },

        formatRupiah(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }
    }">

    <form method="POST" action="{{ route('patient.booking.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Hidden bindings to pass Alpine data to backend --}}
        <input type="hidden" name="patient_id" value="{{ auth()->id() ?? 1 }}">
        <input type="hidden" name="services" :value="JSON.stringify(selectedServices)">
        <input type="hidden" name="date" :value="selectedDate">
        <input type="hidden" name="time" :value="selectedTime">
        <input type="hidden" name="slots" :value="slots">
        <input type="hidden" name="therapist_id" value="{{ $therapist->id }}">
        <input type="hidden" name="terapis_sesi_id" :value="selectedSessionId">

        {{-- TOPBAR --}}
        <x-ui.topbar title="Rumah Terapi Anjali">
            <x-slot:left>
                <button type="button" @click="if(step > 1) step--; else window.location.href='{{ route('patient.booking.index') }}'" class="p-2 -ml-2 text-slate-400 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                </button>
            </x-slot:left>
            <x-slot:right>
                <img src="https://i.pravatar.cc/100?u=anjali" class="w-9 h-9 rounded-xl border border-slate-200 object-cover">
            </x-slot:right>
        </x-ui.topbar>

        <div class="px-6 pt-8 pb-32">

            @if ($errors->any())
                <div class="p-5 mb-8 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl shadow-sm">
                    <p class="font-bold text-sm mb-2 uppercase tracking-wider text-rose-700">Pendaftaran Gagal</p>
                    <ul class="list-disc pl-5 text-xs space-y-1 font-semibold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ==============================
                 STEP 1: FORM PERJANJIAN
                 ============================== --}}
            <div x-show="step === 1" x-transition class="space-y-10">
                
                {{-- 1. TITLE SECTION --}}
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl font-semibold text-teal-600">02</span>
                        <div class="h-1 flex-1 bg-slate-200 rounded-full">
                            <div class="w-4/5 h-full bg-teal-500"></div>
                        </div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Step 2 dari 5</span>
                    </div>
                    <h2 class="text-3xl font-semibold text-slate-800 tracking-tight uppercase">Perjanjian</h2>
                    <p class="text-base text-slate-500 font-semibold leading-relaxed">Pilih langkah yang tepat untuk kesehatan jangka panjang Anda.</p>
                </div>

                {{-- 2. THERAPIST CARD --}}
                <div class="p-4 bg-white border border-slate-200 rounded-2xl flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 bg-teal-50 rounded-xl flex items-center justify-center text-teal-600 font-semibold text-lg">{{ $initials }}</div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Terapis</p>
                            <p class="text-lg font-semibold text-slate-800">{{ $therapist->nama_karyawan }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 bg-teal-50 text-teal-700 text-xs font-semibold uppercase rounded-md border border-teal-100">Tersedia</span>
                </div>

                {{-- 3. PILIH LAYANAN --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Pilih Layanan</h3>
                    </div>

                    <input type="text" x-model="searchService" placeholder="Cari layanan..." 
                        class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl text-base font-semibold shadow-sm focus:border-teal-500 outline-none transition-all">

                    <div class="space-y-3">
                        <template x-for="service in filteredServices" :key="service.id">
                            <button type="button" @click="toggleService(service.id)" 
                                :class="selectedServices.includes(service.id) ? 'border-teal-500 bg-teal-50/30' : 'border-slate-200 bg-white'"
                                class="w-full p-5 border rounded-2xl text-left transition-all relative group shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1 pr-4">
                                        <h4 class="text-base font-semibold text-slate-800" x-text="service.name"></h4>
                                        <p class="text-sm text-slate-400 mt-1 font-semibold" x-text="service.desc"></p>
                                    </div>
                                    <span class="text-base font-semibold text-teal-600" x-text="formatRupiah(service.price)"></span>
                                </div>
                                <div x-show="selectedServices.includes(service.id)" class="absolute -top-2 -right-2 w-6 h-6 bg-teal-500 rounded-full flex items-center justify-center text-white shadow-lg border-2 border-white">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- 4. TANGGAL & WAKTU --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Pilih Tanggal & Waktu</h3>
                    </div>

                    <div class="space-y-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-teal-700 uppercase tracking-widest ml-1">Tanggal Perjanjian</label>
                            <select x-model="selectedDate"
                                class="w-full bg-slate-50 p-4 rounded-xl text-lg font-semibold text-slate-700 outline-none border border-slate-100 focus:border-teal-500 transition-all">
                                <option value="" disabled>Pilih Tanggal</option>
                                <template x-for="date in availableDates" :key="date">
                                    <option :value="date" x-text="date"></option>
                                </template>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-teal-700 uppercase tracking-widest ml-1">Waktu Kunjungan</label>
                            <div class="flex p-1 bg-slate-100 rounded-xl">
                                <button type="button" @click="timeType = 'pagi'" 
                                    :class="timeType === 'pagi' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-500'"
                                    class="flex-1 py-3 text-sm font-semibold uppercase tracking-widest rounded-lg transition-all">Pagi</button>
                                <button type="button" @click="timeType = 'siang'" 
                                    :class="timeType === 'siang' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-500'"
                                    class="flex-1 py-3 text-sm font-semibold uppercase tracking-widest rounded-lg transition-all">Siang</button>
                                <button type="button" @click="timeType = 'malam'" 
                                    :class="timeType === 'malam' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-500'"
                                    class="flex-1 py-3 text-sm font-semibold uppercase tracking-widest rounded-lg transition-all">Malam</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <template x-for="slot in timeSlotsForSelectedDate[timeType]" :key="slot.id">
                                <button type="button" 
                                    @click="if(slot.slots > 0) { selectedTime = slot.time; selectedSessionId = slot.id; }"
                                    :disabled="slot.slots === 0"
                                    :class="{
                                        'border-teal-500 bg-teal-50 ring-1 ring-teal-500': selectedTime === slot.time,
                                        'border-slate-100 bg-white hover:border-teal-200': selectedTime !== slot.time && slot.slots > 0,
                                        'opacity-40 bg-slate-50 cursor-not-allowed border-transparent': slot.slots === 0
                                    }"
                                    class="p-4 border-2 rounded-xl text-left transition-all relative overflow-hidden group">
                                    
                                    <div class="flex flex-col">
                                        <span class="text-lg font-semibold tracking-tight" 
                                            :class="selectedTime === slot.time ? 'text-teal-700' : 'text-slate-700'" 
                                            x-text="slot.time"></span>
                                        
                                        <div class="flex items-center gap-1.5 mt-1">
                                            <div class="w-1.5 h-1.5 rounded-full" :class="slot.slots > 0 ? 'bg-emerald-500' : 'bg-rose-500'"></div>
                                            <span class="text-xs font-semibold uppercase tracking-tighter" 
                                                :class="slot.slots > 0 ? 'text-slate-400' : 'text-rose-400'"
                                                x-text="slot.slots > 0 ? 'Sisa ' + slot.slots + ' Slot' : 'Penuh'"></span>
                                        </div>
                                    </div>
                                    <div x-show="selectedTime === slot.time" class="absolute top-2 right-2">
                                        <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- 5. MASUKKAN SESI (Stepper) --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Masukkan Sesi</h3>
                    </div>

                    <div class="p-6 bg-white border border-slate-200 rounded-2xl flex items-center justify-between shadow-sm">
                        <div>
                            <h4 class="text-lg font-semibold text-slate-800" x-text="slots > 1 ? 'Sesi Grup' : 'Individual'"></h4>
                            <p class="text-sm text-slate-400 font-semibold uppercase tracking-tighter">Maksimal 3 orang per sesi</p>
                        </div>

                        <div class="flex items-center bg-slate-50 p-1.5 rounded-xl border border-slate-100">
                            <button type="button" @click="if(slots > 1) slots--" class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm text-slate-400 font-semibold text-xl">-</button>
                            <span class="w-12 text-center font-semibold text-slate-800 text-xl" x-text="slots"></span>
                            <button type="button" @click="if(slots < 3) slots++" class="w-10 h-10 flex items-center justify-center bg-teal-800 rounded-xl shadow-sm text-white font-semibold text-xl hover:bg-teal-700 transition">+</button>
                        </div>
                    </div>
                </div>

                {{-- 6. DETAIL PASIEN LOGIC --}}
                <div class="space-y-6 pt-4 border-t border-slate-100">
                    
                    {{-- VIEW: SLOT = 1 (Simple Mode) --}}
                    <div x-show="slots === 1" class="space-y-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Keluhan Saat Ini</h3>
                        </div>
                        <textarea name="complaint_main" :disabled="slots > 1" placeholder="Ceritakan apa yang Anda rasakan atau keluhan Anda hari ini..."
                            class="w-full px-6 py-5 bg-white border border-slate-200 rounded-2xl text-lg font-semibold text-slate-700 h-40 shadow-sm focus:border-teal-500 outline-none transition-all resize-none"></textarea>
                    </div>

                    {{-- VIEW: SLOT > 1 (Group Mode) --}}
                    <div x-show="slots > 1" class="space-y-6 animate-in fade-in duration-300">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Detail Pasien Grup</h3>
                        </div>

                        {{-- Pasien 1 (Utama) --}}
                        <div class="p-6 bg-white border border-slate-200 rounded-2xl space-y-4 shadow-sm relative overflow-hidden">
                            <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                                <span class="text-xs font-semibold text-teal-600 uppercase tracking-widest bg-teal-50 px-2 py-1 rounded">Pasien 1 (Utama)</span>
                            </div>
                            <div>
                                <p class="text-base font-semibold text-slate-800 uppercase tracking-widest">{{ $patientName }}</p>
                                <p class="text-xs text-slate-400 font-semibold uppercase mt-1">ID: {{ $patientPublicId }}</p>
                                <input type="hidden" name="patient_names[]" value="{{ $patientName }}" :disabled="slots === 1">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-teal-700 uppercase tracking-widest">Keluhan Utama</label>
                                <textarea name="patient_complaints[]" :disabled="slots === 1" placeholder="Ceritakan keluhan {{ $patientName }}..." 
                                    class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-base font-semibold h-24 focus:ring-2 focus:ring-teal-100 outline-none resize-none"></textarea>
                            </div>
                        </div>

                        {{-- Pasien Tambahan --}}
                        <template x-for="i in Array.from({length: slots - 1}, (_, i) => i + 2)" :key="i">
                            <div class="p-6 bg-white border border-slate-200 rounded-2xl space-y-5 shadow-sm">
                                <div class="flex items-center gap-3 border-b border-slate-50 pb-3">
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest bg-slate-50 px-2 py-1 rounded" x-text="'Pasien ' + i"></span>
                                </div>
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label class="text-xs font-semibold text-teal-700 uppercase tracking-widest">Nama Lengkap Pasien</label>
                                        <input type="text" name="patient_names[]" :disabled="slots === 1" placeholder="Masukkan nama..."
                                            class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-base font-semibold outline-none focus:ring-2 focus:ring-teal-100">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-semibold text-teal-700 uppercase tracking-widest">Keluhan Utama</label>
                                        <textarea name="patient_complaints[]" :disabled="slots === 1" placeholder="Ceritakan keluhan..." 
                                            class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-base font-semibold h-24 outline-none focus:ring-2 focus:ring-teal-100 resize-none"></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- 7. ACTION --}}
                <div class="space-y-4 pt-6">
                    <button type="button" @click="step = 2" class="w-full text-center block py-5 bg-teal-800 text-white rounded-2xl text-lg font-semibold uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                        Lanjut
                    </button>
                    <div class="flex items-center justify-center gap-2 text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span class="text-xs font-semibold uppercase tracking-widest text-center">Pembayaran Aman & Terenkripsi</span>
                    </div>
                </div>

            </div>


            {{-- ==============================
                 STEP 2: RINGKASAN JANJI TEMU
                 ============================== --}}
            <div x-show="step === 2" x-transition x-cloak class="space-y-8">
                
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl font-semibold text-teal-600">03</span>
                        <div class="h-1 flex-1 bg-slate-200 rounded-full">
                            <div class="w-3/5 h-full bg-teal-500"></div>
                        </div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Step 3 dari 5</span>
                    </div>
                    <h2 class="text-3xl font-semibold text-slate-800 tracking-tight leading-tight uppercase">Ringkasan <br> Janji Temu</h2>
                    <p class="text-base text-slate-500 font-semibold leading-relaxed">Cek kembali detail jadwal Anda sebelum melakukan konfirmasi.</p>
                </div>

                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden p-6 space-y-8">
                    
                    <div class="flex flex-col items-center text-center">
                        <p class="text-xs font-semibold text-teal-600 uppercase tracking-[0.2em] mb-4">Terapis Terpilih</p>
                        <div class="relative mb-4">
                            <div class="w-20 h-20 bg-teal-50 rounded-full flex items-center justify-center border-2 border-white shadow-md text-teal-600 font-bold text-2xl">
                                {{ $initials }}
                            </div>
                            <div class="absolute bottom-0 right-0 w-6 h-6 bg-teal-500 rounded-full border-2 border-white flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <h4 class="text-xl font-semibold text-slate-800">{{ $therapist->nama_karyawan }}</h4>
                        <p class="text-sm font-semibold text-slate-400 uppercase tracking-widest mt-1">Spesialis Akupunktur</p>
                    </div>

                    <div class="bg-slate-50 rounded-[1.5rem] p-6 space-y-6">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 shrink-0 bg-white rounded-xl shadow-sm flex items-center justify-center text-teal-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Layanan Terpilih</p>
                                <p class="text-base font-semibold text-slate-800 leading-tight" x-text="selectedServicesNames || 'Belum memilih layanan'"></p>
                                <p class="text-xs font-semibold text-slate-400 mt-1">Sesi Terapi</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-10 h-10 shrink-0 bg-white rounded-xl shadow-sm flex items-center justify-center text-teal-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Jadwal</p>
                                <p class="text-base font-semibold text-slate-800 leading-tight" x-text="selectedDate"></p>
                                <p class="text-xs font-semibold text-slate-400 mt-1" x-text="selectedTime"></p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-10 h-10 shrink-0 bg-white rounded-xl shadow-sm flex items-center justify-center text-teal-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Lokasi</p>
                                <p class="text-base font-semibold text-slate-800 leading-tight">{{ $namaCabang }}</p>
                                <p class="text-xs font-semibold text-slate-400 mt-1 leading-relaxed">{{ $alamatCabang }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-slate-500">Biaya Konsultasi</span>
                        <span class="text-base font-semibold text-slate-800" x-text="formatRupiah(totalConsultationCost)"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-slate-500">Biaya Admin</span>
                        <span class="text-base font-semibold text-slate-800">Rp 5.000</span>
                    </div>
                    <div class="pt-4 border-t border-slate-200 flex justify-between items-end">
                        <div>
                            <p class="text-[11px] font-semibold text-teal-600 uppercase tracking-widest">Grand Total</p>
                            <p class="text-[10px] font-semibold text-slate-400 uppercase">Termasuk Pajak</p>
                        </div>
                        <span class="text-2xl font-semibold text-slate-900 tracking-tight" x-text="formatRupiah(grandTotal)"></span>
                    </div>
                </div>

                <div class="space-y-4 pt-4 text-center">
                    <button type="button" @click="step = 3" class="text-center block w-full py-5 bg-teal-800 text-white rounded-2xl text-base font-semibold uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                        Konfirmasi & Lanjutkan
                    </button>
                    <p class="text-xs font-semibold text-slate-400 leading-relaxed px-6">
                        Dengan menekan tombol konfirmasi, Anda menyetujui kebijakan pembatalan 24 jam kami.
                    </p>
                </div>

            </div>


            {{-- ==============================
                 STEP 3: VERIFIKASI PEMBAYARAN
                 ============================== --}}
            <div x-show="step === 3" x-transition x-cloak class="space-y-8">

                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl font-semibold text-teal-600">04</span>
                        <div class="h-1 flex-1 bg-slate-200 rounded-full">
                            <div class="w-4/5 h-full bg-teal-500"></div>
                        </div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Step 4 dari 5</span>
                    </div>
                    <h2 class="text-3xl font-semibold text-slate-800 tracking-tight leading-tight uppercase">Verifikasi <br> Pembayaran</h2>
                    <p class="text-base text-slate-500 font-semibold leading-relaxed">Silakan transfer sesuai total biaya ke rekening berikut untuk mengonfirmasi jadwal Anda.</p>
                </div>

                <div class="bg-[#2D7A78] rounded-[2rem] p-8 text-white shadow-xl shadow-teal-900/10 space-y-6">
                    <div class="flex items-center gap-3 border-b border-white/10 pb-4">
                        <svg class="w-5 h-5 text-teal-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        <span class="text-xs font-semibold uppercase tracking-[0.2em]">Rincian Pembayaran</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm font-medium opacity-90">
                            <span>Biaya Konsultasi</span>
                            <span x-text="formatRupiah(totalConsultationCost)"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-medium opacity-90">
                            <span>Biaya Admin</span>
                            <span>Rp 5.000</span>
                        </div>
                        <div class="pt-6 border-t border-white/20 flex justify-between items-end">
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-widest opacity-60 mb-1">Grand Total</p>
                                <p class="text-3xl font-semibold tracking-tight" x-text="formatRupiah(grandTotal)"></p>
                            </div>
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] border border-slate-200 p-8 shadow-sm space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-teal-600 border border-slate-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Bank Transfer Details</p>
                            <p class="text-base font-semibold text-slate-800">BCA (Bank Central Asia)</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest ml-1">Nomor Rekening</p>
                            <div class="flex items-center justify-between bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-lg font-semibold text-slate-800 tracking-wider" x-text="accountNumber"></span>
                                <button type="button" @click="copyToClipboard()" 
                                    class="px-4 py-1.5 bg-white border border-slate-200 text-teal-600 text-[10px] font-semibold uppercase tracking-widest rounded-lg shadow-sm active:scale-95 transition-all">
                                    <span x-text="copied ? 'Tersalin!' : 'Copy'"></span>
                                </button>
                            </div>
                        </div>
                        <div class="space-y-1 ml-1">
                            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Nama Rekening</p>
                            <p class="text-base font-semibold text-slate-800">Klinik Anjali</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] border border-slate-200 p-8 shadow-sm text-center space-y-6">
                    <div class="flex items-center gap-4 text-left">
                        <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-teal-600 border border-slate-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Bank Transfer Details</p>
                            <p class="text-base font-semibold text-slate-800">BCA (Bank Central Asia)</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest">QRIS - BCA</p>
                        <div class="mx-auto w-48 h-48 p-2 bg-white border-2 border-slate-50 rounded-2xl shadow-inner">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=AnjaliClinic" class="w-full h-full object-contain">
                        </div>
                        <div class="mt-4">
                            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Nama Rekening</p>
                            <p class="text-base font-semibold text-slate-800">Klinik Anjali</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-2 px-1">
                        <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <h3 class="text-sm font-semibold text-slate-800 uppercase tracking-widest">Upload Bukti Transfer</h3>
                    </div>
                    
                    <label class="block group cursor-pointer">
                        <div class="bg-white border-2 border-dashed border-slate-200 rounded-[2rem] p-10 text-center space-y-4 group-hover:border-teal-500 transition-all shadow-sm relative overflow-hidden" x-data="{ fileName: '' }">
                            <div class="w-14 h-14 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-slate-400 group-hover:text-teal-600 group-hover:bg-teal-50 transition-all">
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div class="space-y-1">
                                <p class="text-base font-semibold text-slate-700" x-text="fileName ? fileName : 'Tap to select or take photo'"></p>
                                <p class="text-xs font-medium text-slate-400 uppercase tracking-tighter" x-show="!fileName">JPEG, PNG, or PDF supported</p>
                            </div>
                            <input type="file" name="payment_proof" class="absolute inset-0 opacity-0 cursor-pointer" @change="fileName = $event.target.files[0].name">
                        </div>
                    </label>
                </div>

                <div class="bg-teal-50/50 border border-teal-100 rounded-2xl p-5 flex gap-4">
                    <svg class="w-6 h-6 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-xs font-semibold text-teal-800 uppercase tracking-widest mb-1">Informasi Verifikasi</p>
                        <p class="text-sm font-medium text-teal-700 leading-relaxed">Tim kami akan memverifikasi bukti pembayaran Anda dalam waktu maksimal 2 jam.</p>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="text-center block w-full py-5 bg-teal-800 text-white rounded-2xl text-base font-bold uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                        Kirim & Selesaikan
                    </button>
                </div>

            </div>

        </div>

    </form>

    <x-navigation.patient-navbar active="booking" />

</x-layouts.mobile-app>

@endsection