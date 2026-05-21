@extends('components.layouts.app')

@section('title', 'Perjanjian')

@section('content')

    @php
        $patientName = auth()->user() ? auth()->user()->name : 'Pasien Utama';
        $patientPublicId =
            auth()->user() && auth()->user()->pasien ? auth()->user()->pasien->pasien_public_id : 'PSN-GUEST';

        $words = explode(' ', $therapist->nama_karyawan);
        $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
        $namaKolaborasi = $therapist->kolaborasi ? $therapist->kolaborasi->nama_kolaborasi : 'Rumah Terapi Anjali';
        $alamatKolaborasi = $therapist->kolaborasi ? $therapist->kolaborasi->alamat : 'Surabaya';
    @endphp

    <script>
        window.bookingServices = @json($services);
        window.bookingSessions = @json($sessions);
    </script>

    <x-layouts.mobile-app class="bg-slate-50 min-h-screen" x-data="{
        step: {{ old('current_step', 1) }},
        slots: {{ old('slots', 1) }},
        selectedServices: {{ old('services') ? old('services') : '[]' }},
        patientServiceOverrides: {
            0: '{{ old('patient_services.0', '') }}',
            1: '{{ old('patient_services.1', '') }}',
            2: '{{ old('patient_services.2', '') }}',
            3: '{{ old('patient_services.3', '') }}',
            4: '{{ old('patient_services.4', '') }}'
        },
        searchService: '',
        paymentProofFileName: '',
        services: window.bookingServices,
        sessions: window.bookingSessions,
        selectedDate: '{{ old('date', '') }}',
        timeType: '{{ old('time_type', 'pagi') }}',
        selectedTime: '{{ old('time', '') }}',
        selectedSessionId: '{{ old('terapis_sesi_id', '') }}',
        biayaHomecare: 0,
    
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
    
            let today = new Date();
            let todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
            let currentHour = today.getHours();
            let currentMinute = today.getMinutes();
            let isToday = (this.selectedDate === todayStr);
    
            daySessions.forEach(s => {
                let hour = parseInt(s.waktu_mulai.split(':')[0]);
                let minute = parseInt(s.waktu_mulai.split(':')[1]);
    
                let isPast = isToday && (hour < currentHour || (hour === currentHour && minute < currentMinute));
    
                let slotInfo = { id: s.id, time: s.waktu_mulai, slots: s.kuota_sisa, isPast: isPast };
    
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
    
        isPeriodDisabled(type) {
            const slots = this.timeSlotsForSelectedDate[type];
            // Jika tidak ada jadwal sama sekali di periode itu
            if (slots.length === 0) return true;
    
            // Cek apakah SEMUA slot di periode tersebut sudah penuh (0) atau sudah lewat (isPast)
            return slots.every(s => s.slots === 0 || s.isPast);
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
                this.selectedServices = [];
            } else {
                this.selectedServices = [id];
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
    
        getPatientServiceName(index) {
            let overrideId = this.patientServiceOverrides[index];
            if (overrideId) {
                let s = this.services.find(sv => sv.id === parseInt(overrideId));
                return s ? s.name : null;
            }
            return this.selectedServicesNames || null;
        },
    
        get biayaHomecare() {
            if (this.selectedServices.length > 0) {
                let defaultService = this.services.find(s => s.id === this.selectedServices[0]);
                return defaultService ? defaultService.homecare_price : 0;
            }
            return 0;
        },
    
        get totalConsultationCost() {
            let defaultCost = 0;
            if (this.selectedServices.length > 0) {
                let defaultService = this.services.find(s => s.id === this.selectedServices[0]);
                defaultCost = defaultService ? defaultService.price : 0;
            }
    
            let total = 0;
            for (let i = 0; i < this.slots; i++) {
                let overrideId = this.patientServiceOverrides[i];
                if (overrideId) {
                    let overrideService = this.services.find(s => s.id === parseInt(overrideId));
                    total += overrideService ? overrideService.price : 0;
                } else {
                    total += defaultCost;
                }
            }
            return total;
        },
    
        get diskon() {
            if (this.selectedServices.length > 0) {
                let defaultService = this.services.find(s => s.id === this.selectedServices[0]);
                return defaultService ? defaultService.discount : 0;
            }
            return 0;
        },
    
        get discountAmount() {
            return this.totalConsultationCost * (this.diskon / 100);
        },
    
        get grandTotal() {
            return (this.totalConsultationCost - this.discountAmount) + this.biayaHomecare + 5000;
        },
    
        formatRupiah(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        },
    
        formatDate(dateStr) {
            if (!dateStr) return '';
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', options);
        },
    
        formatTime(timeStr) {
            if (!timeStr) return '';
            return timeStr.substring(0, 5);
        }
    }">

        <form method="POST" action="{{ route('patient.booking.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Hidden bindings to pass Alpine data to backend --}}
            <input type="hidden" name="patient_id" value="{{ auth()->id() ?? 1 }}">

            <input type="hidden" name="current_step" :value="step" value="{{ old('current_step') }}">
            <input type="hidden" name="services" :value="JSON.stringify(selectedServices)" value="{{ old('services') }}">
            <input type="hidden" name="date" :value="selectedDate" value="{{ old('date') }}">
            <input type="hidden" name="time" :value="selectedTime" value="{{ old('time') }}">
            <input type="hidden" name="slots" :value="slots" value="{{ old('slots') }}">
            <input type="hidden" name="terapis_sesi_id" :value="selectedSessionId" value="{{ old('terapis_sesi_id') }}">

            {{-- TOPBAR --}}
            <x-ui.topbar title="Rumah Terapi Anjali">
                <x-slot:left>
                    <button type="button"
                        @click="if(step > 1) step--; else window.location.href='{{ route('patient.booking.index') }}'"
                        class="p-2 -ml-2 text-slate-400 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                </x-slot:left>
                <x-slot:right>
                    <img src="https://i.pravatar.cc/100?u=anjali"
                        class="w-9 h-9 rounded-xl border border-slate-200 object-cover">
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
                <div x-show="step === 1" x-transition class="space-y-10" x-ref="step1">

                    {{-- 1. TITLE SECTION --}}
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl font-semibold text-teal-600">02</span>
                            <div class="h-1 flex-1 bg-slate-200 rounded-full">
                                <div class="w-2/5 h-full bg-teal-500"></div>
                            </div>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Step 2 dari
                                5</span>
                        </div>
                        <h2 class="text-3xl font-semibold text-slate-800 tracking-tight uppercase">Perjanjian</h2>
                        <p class="text-base text-slate-500 font-semibold leading-relaxed">Pilih langkah yang tepat untuk
                            kesehatan jangka panjang Anda.</p>
                    </div>

                    {{-- 2. THERAPIST CARD --}}
                    <div
                        class="p-4 bg-white border border-slate-200 rounded-2xl flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-11 h-11 bg-teal-50 rounded-xl flex items-center justify-center text-teal-600 font-semibold text-lg">
                                {{ $initials }}</div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Terapis</p>
                                <p class="text-lg font-semibold text-slate-800">{{ $therapist->nama_karyawan }}</p>
                            </div>
                        </div>
                        <span
                            class="px-2.5 py-1 bg-teal-50 text-teal-700 text-xs font-semibold uppercase rounded-md border border-teal-100">Tersedia</span>
                    </div>

                    {{-- 3. TANGGAL & WAKTU --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Pilih Tanggal & Waktu
                            </h3>
                        </div>

                        <div class="space-y-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                            <div class="space-y-2" x-data="{ isOpen: false }">
                                <label class="text-xs font-semibold text-teal-700 uppercase tracking-widest ml-1">Tanggal
                                    Perjanjian</label>

                                <div class="relative">
                                    <!-- Trigger -->
                                    <button type="button" @click="isOpen = !isOpen" @click.away="isOpen = false"
                                        class="w-full flex items-center justify-between bg-slate-50 p-4 rounded-xl text-lg font-semibold text-slate-700 outline-none border border-slate-100 hover:border-teal-200 focus:border-teal-500 transition-all text-left">
                                        <span x-text="selectedDate ? formatDate(selectedDate) : 'Pilih Tanggal'"
                                            :class="!selectedDate ? 'text-slate-400' : ''"></span>
                                        <svg class="w-5 h-5 text-slate-400 transition-transform duration-200"
                                            :class="isOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="isOpen" x-transition.opacity.duration.200ms style="display: none;"
                                        class="absolute z-10 mt-2 w-full bg-white border border-slate-100 rounded-xl shadow-xl shadow-slate-200/50 max-h-64 overflow-y-auto">
                                        <div class="p-2 space-y-1">
                                            <template x-for="date in availableDates" :key="date">
                                                <button type="button"
                                                    @click="
                                                    selectedDate = date; isOpen = false; 
                                                    timeType = 'pagi'; 
                                                    selectedTime = ''; 
                                                    selectedSessionId = null;
                                                    if(isPeriodDisabled(timeType)) {
                                                        if(!isPeriodDisabled('pagi')) timeType = 'pagi';
                                                        else if(!isPeriodDisabled('siang')) timeType = 'siang';
                                                        else if(!isPeriodDisabled('malam')) timeType = 'malam';
                                                    }"
                                                    class="w-full text-left px-4 py-3 rounded-lg text-base font-semibold transition-all"
                                                    :class="selectedDate === date ? 'bg-teal-50 text-teal-700' :
                                                        'text-slate-700 hover:bg-slate-50'">
                                                    <div class="flex items-center justify-between">
                                                        <span x-text="formatDate(date)"></span>
                                                        <svg x-show="selectedDate === date" class="w-5 h-5 text-teal-600"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="2.5">
                                                            <path d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                </button>
                                            </template>

                                            <div x-show="availableDates.length === 0"
                                                class="px-4 py-3 text-sm text-slate-500 text-center font-medium">
                                                Tidak ada tanggal tersedia
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-teal-700 uppercase tracking-widest ml-1">Waktu
                                    Kunjungan</label>
                                <div class="flex p-1 bg-slate-100 rounded-xl">
                                    <!-- TOMBOL PAGI -->
                                    <button type="button" @click="if(!isPeriodDisabled('pagi')) timeType = 'pagi'"
                                        :disabled="isPeriodDisabled('pagi')"
                                        :class="{
                                            'bg-white text-teal-700 shadow-sm': timeType === 'pagi',
                                            'text-slate-500': timeType !== 'pagi' && !isPeriodDisabled('pagi'),
                                            'opacity-30 grayscale cursor-not-allowed text-slate-300': isPeriodDisabled(
                                                'pagi')
                                        }"
                                        class="flex-1 py-3 text-sm font-semibold uppercase tracking-widest rounded-lg transition-all">
                                        Pagi
                                    </button>

                                    <!-- TOMBOL SIANG -->
                                    <button type="button" @click="if(!isPeriodDisabled('siang')) timeType = 'siang'"
                                        :disabled="isPeriodDisabled('siang')"
                                        :class="{
                                            'bg-white text-teal-700 shadow-sm': timeType === 'siang',
                                            'text-slate-500': timeType !== 'siang' && !isPeriodDisabled('siang'),
                                            'opacity-30 grayscale cursor-not-allowed text-slate-300': isPeriodDisabled(
                                                'siang')
                                        }"
                                        class="flex-1 py-3 text-sm font-semibold uppercase tracking-widest rounded-lg transition-all">
                                        Siang
                                    </button>

                                    <!-- TOMBOL MALAM -->
                                    <button type="button" @click="if(!isPeriodDisabled('malam')) timeType = 'malam'"
                                        :disabled="isPeriodDisabled('malam')"
                                        :class="{
                                            'bg-white text-teal-700 shadow-sm': timeType === 'malam',
                                            'text-slate-500': timeType !== 'malam' && !isPeriodDisabled('malam'),
                                            'opacity-30 grayscale cursor-not-allowed text-slate-300': isPeriodDisabled(
                                                'malam')
                                        }"
                                        class="flex-1 py-3 text-sm font-semibold uppercase tracking-widest rounded-lg transition-all">
                                        Malam
                                    </button>
                                </div>
                                <input type="hidden" name="time_type" :value="timeType" :value="old('time_type')">
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <template x-for="slot in timeSlotsForSelectedDate[timeType]" :key="slot.id">
                                    <button type="button"
                                        @click="
                                        if(slot.slots > 0 && !slot.isPast) { 
                                            if (slots > slot.slots) {
                                                if (typeof Swal !== 'undefined') {
                                                    Swal.fire({
                                                        icon: 'warning',
                                                        title: 'Slot Tidak Cukup',
                                                        text: `Waktu ${formatTime(slot.time)} hanya ada ${slot.slots} slot tersisa. Silakan pilih waktu lain.`,
                                                        confirmButtonColor: '#0f766e',
                                                        confirmButtonText: 'Mengerti',
                                                        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl shadow-md' }
                                                    });
                                                } else {
                                                    alert(`Waktu ${formatTime(slot.time)} hanya ada ${slot.slots} slot tersisa.`);
                                                }
                                            } else {
                                                selectedTime = slot.time; selectedSessionId = slot.id; 
                                            }
                                        }
                                    "
                                        :disabled="slot.slots === 0 || slot.isPast"
                                        :class="{
                                            'border-teal-500 bg-teal-50 ring-1 ring-teal-500': selectedTime === slot
                                                .time,
                                            'border-slate-100 bg-white hover:border-teal-200': selectedTime !== slot
                                                .time && slot.slots > 0 && !slot.isPast,
                                            'opacity-40 bg-slate-50 cursor-not-allowed border-transparent': slot
                                                .slots === 0 || slot.isPast
                                        }"
                                        class="p-4 border-2 rounded-xl text-left transition-all relative overflow-hidden group">

                                        <div class="flex flex-col">
                                            <span class="text-lg font-semibold tracking-tight"
                                                :class="selectedTime === slot.time ? 'text-teal-700' : 'text-slate-700'"
                                                x-text="formatTime(slot.time)"></span>

                                            <div class="flex items-center gap-1.5 mt-1">
                                                <div class="w-1.5 h-1.5 rounded-full"
                                                    :class="(slot.slots > 0 && !slot.isPast) ? 'bg-emerald-500' : 'bg-rose-500'">
                                                </div>
                                                <span class="text-xs font-semibold uppercase tracking-tighter"
                                                    :class="(slot.slots > 0 && !slot.isPast) ? 'text-slate-400' :
                                                    'text-rose-400'"
                                                    x-text="slot.isPast ? 'Waktu Lewat' : (slot.slots > 0 ? 'Sisa ' + slot.slots + ' Slot' : 'Penuh')"></span>
                                            </div>
                                        </div>
                                        <div x-show="selectedTime === slot.time" class="absolute top-2 right-2">
                                            <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="3">
                                                <path d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- 4. PILIH LAYANAN --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Pilih Layanan Utama
                            </h3>
                        </div>

                        <input type="text" x-model="searchService" placeholder="Cari layanan..."
                            class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl text-base font-semibold shadow-sm focus:border-teal-500 outline-none transition-all">

                        <div class="space-y-3 max-h-[350px] overflow-y-auto pr-2"
                            style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
                            <template x-for="service in filteredServices" :key="service.id">
                                <button type="button" @click="toggleService(service.id)"
                                    :class="selectedServices.includes(service.id) ? 'border-teal-500 bg-teal-50/30' :
                                        'border-slate-200 bg-white'"
                                    class="w-full p-5 border rounded-2xl text-left transition-all relative group shadow-sm">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1 pr-4">
                                            <h4 class="text-base font-semibold text-slate-800" x-text="service.name"></h4>
                                            <p class="text-sm text-slate-400 mt-1 font-semibold" x-text="service.desc">
                                            </p>
                                        </div>
                                        <span class="text-base font-semibold text-teal-600"
                                            x-text="formatRupiah(service.price)"></span>
                                    </div>
                                    <div x-show="selectedServices.includes(service.id)"
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-teal-500 rounded-full flex items-center justify-center text-white shadow-lg border-2 border-white">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="3">
                                            <path d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </button>
                            </template>

                            <div x-show="filteredServices.length === 0" x-cloak
                                class="p-8 text-center bg-slate-50 border border-slate-100 rounded-2xl border-dashed">
                                <div
                                    class="w-16 h-16 mx-auto bg-white rounded-full flex items-center justify-center shadow-sm mb-4">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-base font-bold text-slate-700 tracking-tight">Layanan Tidak Ditemukan</h4>
                                <p class="text-sm text-slate-500 font-medium mt-1">Coba gunakan kata kunci pencarian yang
                                    lain.</p>
                            </div>
                        </div>
                    </div>

                    {{-- 5. MASUKKAN SESI (Stepper) --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Masukkan Sesi</h3>
                        </div>

                        <div
                            class="p-6 bg-white border border-slate-200 rounded-2xl flex items-center justify-between shadow-sm">
                            <div>
                                <h4 class="text-lg font-semibold text-slate-800"
                                    x-text="slots > 1 ? 'Sesi Grup' : 'Individual'"></h4>
                                <p class="text-sm text-slate-400 font-semibold uppercase tracking-tighter">Maksimal 5 orang
                                    per sesi</p>
                            </div>

                            <div class="flex items-center bg-slate-50 p-1.5 rounded-xl border border-slate-100">
                                <button type="button" @click="if(slots > 1) slots--"
                                    class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm text-slate-400 font-semibold text-xl">-</button>
                                <span class="w-12 text-center font-semibold text-slate-800 text-xl" x-text="slots"></span>
                                <button type="button"
                                    @click="
                                if(slots < 5) {
                                    let canIncrement = true;
                                    if(selectedSessionId) {
                                        let currentSession = sessions.find(s => s.id === selectedSessionId);
                                        if (currentSession && (slots + 1) > currentSession.kuota_sisa) {
                                            canIncrement = false;
                                            if (typeof Swal !== 'undefined') {
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Slot Tidak Cukup',
                                                    text: `Waktu ${formatTime(currentSession.waktu_mulai)} hanya ada ${currentSession.kuota_sisa} slot tersisa. Silakan pilih waktu lain.`,
                                                    confirmButtonColor: '#0f766e',
                                                    confirmButtonText: 'Mengerti',
                                                    customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl shadow-md' }
                                                });
                                            } else {
                                                alert(`Waktu ${formatTime(currentSession.waktu_mulai)} hanya ada ${currentSession.kuota_sisa} slot tersisa.`);
                                            }
                                        }
                                    }
                                    if (canIncrement) slots++;
                                }
                            "
                                    class="w-10 h-10 flex items-center justify-center bg-teal-800 rounded-xl shadow-sm text-white font-semibold text-xl hover:bg-teal-700 transition">+</button>
                            </div>
                        </div>
                    </div>

                    {{-- 6. DETAIL PASIEN LOGIC --}}
                    <div class="space-y-6 pt-4 border-t border-slate-100">

                        {{-- VIEW: SLOT = 1 (Simple Mode) --}}
                        <div x-show="slots === 1" class="space-y-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path
                                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Keluhan Saat Ini
                                </h3>
                            </div>
                            <textarea name="complaint_main" :disabled="slots > 1"
                                placeholder="Ceritakan apa yang Anda rasakan atau keluhan Anda hari ini..." required
                                class="w-full px-6 py-5 bg-white border border-slate-200 rounded-2xl text-lg font-semibold text-slate-700 h-40 shadow-sm focus:border-teal-500 outline-none transition-all resize-none">{{ old('complaint_main') }}</textarea>
                        </div>

                        {{-- VIEW: SLOT > 1 (Group Mode) --}}
                        <div x-show="slots > 1" class="space-y-6 animate-in fade-in duration-300">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Detail Pasien
                                    Grup</h3>
                            </div>

                            {{-- Pasien 1 (Utama) --}}
                            <div
                                class="p-6 bg-white border border-slate-200 rounded-2xl space-y-4 shadow-sm relative overflow-hidden">
                                <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                                    <span
                                        class="text-xs font-semibold text-teal-600 uppercase tracking-widest bg-teal-50 px-2 py-1 rounded">Pasien
                                        1 (Utama)</span>
                                </div>
                                <div>
                                    <p class="text-base font-semibold text-slate-800 uppercase tracking-widest">
                                        {{ $patientName }}</p>
                                    <p class="text-xs text-slate-400 font-semibold uppercase mt-1">ID:
                                        {{ $patientPublicId }}</p>
                                    <input type="hidden" name="patient_names[]" value="{{ $patientName }}"
                                        :disabled="slots === 1">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-semibold text-teal-700 uppercase tracking-widest">Keluhan
                                        Utama</label>
                                    <textarea name="patient_complaints[]" :disabled="slots === 1" placeholder="Ceritakan keluhan {{ $patientName }}..."
                                        required
                                        class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-base font-semibold h-24 focus:ring-2 focus:ring-teal-100 outline-none resize-none">{{ old('patient_complaints.0') }}</textarea>
                                </div>
                                {{-- Custom Layanan Spesifik Dropdown - Pasien 1 (Grup) --}}
                                <div class="space-y-2" x-data="{ open: false }">
                                    <label class="text-xs font-semibold text-teal-700 uppercase tracking-widest">Layanan
                                        Spesifik (Opsional)</label>
                                    <input type="hidden" name="patient_services[]" :value="patientServiceOverrides[0]">
                                    <div class="relative">
                                        <button type="button" @click="open = !open" @click.outside="open = false"
                                            class="w-full flex items-center justify-between px-4 py-3.5 bg-slate-50 rounded-xl text-base font-semibold text-slate-700 transition-all"
                                            :class="open ? 'ring-2 ring-teal-400' :
                                                'ring-1 ring-transparent hover:ring-slate-200'">
                                            <span
                                                x-text="patientServiceOverrides[0] ? (services.find(s => s.id === parseInt(patientServiceOverrides[0]))?.name ?? 'Sama dengan layanan utama') : 'Sama dengan layanan utama'"
                                                :class="patientServiceOverrides[0] ? 'text-slate-800' : 'text-slate-400'"></span>
                                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                                :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2.5">
                                                <path d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        <div x-show="open" x-transition:enter="transition ease-out duration-150"
                                            x-transition:enter-start="opacity-0 -translate-y-1"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-100"
                                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                            class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
                                            <div class="p-1.5 space-y-0.5">
                                                <button type="button"
                                                    @click="patientServiceOverrides[0] = ''; open = false"
                                                    class="w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-colors"
                                                    :class="!patientServiceOverrides[0] ? 'bg-teal-50 text-teal-700' :
                                                        'text-slate-400 hover:bg-slate-50'">—
                                                    Sama dengan layanan utama</button>
                                                <template x-for="service in services" :key="service.id">
                                                    <button type="button"
                                                        @click="patientServiceOverrides[0] = service.id; open = false"
                                                        class="w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-colors"
                                                        :class="parseInt(patientServiceOverrides[0]) === service.id ?
                                                            'bg-teal-50 text-teal-700' :
                                                            'text-slate-700 hover:bg-slate-50'"
                                                        x-text="service.name"></button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Pasien Tambahan --}}
                            <template x-for="i in Array.from({length: slots - 1}, (_, i) => i + 2)"
                                :key="i">
                                <div class="p-6 bg-white border border-slate-200 rounded-2xl space-y-5 shadow-sm">
                                    <div class="flex items-center gap-3 border-b border-slate-50 pb-3">
                                        <span
                                            class="text-xs font-semibold text-slate-400 uppercase tracking-widest bg-slate-50 px-2 py-1 rounded"
                                            x-text="'Pasien ' + i"></span>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="space-y-2">
                                            <label
                                                class="text-xs font-semibold text-teal-700 uppercase tracking-widest">Nama
                                                Lengkap Pasien</label>
                                            <input type="text" name="patient_names[]" :disabled="slots === 1"
                                                placeholder="Masukkan nama..."
                                                :value="'{{ old('patient_names') ? 'already_filled' : '' }}'
                                                === 'already_filled' ? @js(old('patient_names'))[i - 1] : ''"
                                                required
                                                class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-base font-semibold outline-none focus:ring-2 focus:ring-teal-100">
                                        </div>
                                        <div class="space-y-2">
                                            <label
                                                class="text-xs font-semibold text-teal-700 uppercase tracking-widest">Keluhan
                                                Utama</label>
                                            <textarea name="patient_complaints[]" :disabled="slots === 1" placeholder="Ceritakan keluhan..." required
                                                class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-base font-semibold h-24 outline-none focus:ring-2 focus:ring-teal-100 resize-none">@js(old('patient_complaints')) ? @js(old('patient_complaints'))[i-1] : ''</textarea>
                                        </div>
                                        {{-- Custom Layanan Spesifik Dropdown - Pasien Tambahan --}}
                                        <div class="space-y-2" x-data="{ open: false }">
                                            <label
                                                class="text-xs font-semibold text-teal-700 uppercase tracking-widest">Layanan
                                                Spesifik (Opsional)</label>
                                            <input type="hidden" name="patient_services[]"
                                                :value="patientServiceOverrides[i - 1]">
                                            <div class="relative">
                                                <button type="button" @click="open = !open"
                                                    @click.outside="open = false"
                                                    class="w-full flex items-center justify-between px-4 py-3.5 bg-slate-50 rounded-xl text-base font-semibold text-slate-700 transition-all"
                                                    :class="open ? 'ring-2 ring-teal-400' :
                                                        'ring-1 ring-transparent hover:ring-slate-200'">
                                                    <span
                                                        x-text="patientServiceOverrides[i - 1] ? (services.find(s => s.id === parseInt(patientServiceOverrides[i - 1]))?.name ?? 'Sama dengan layanan default') : 'Sama dengan layanan default'"
                                                        :class="patientServiceOverrides[i - 1] ? 'text-slate-800' :
                                                            'text-slate-400'"></span>
                                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                                        :class="open ? 'rotate-180' : ''" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" x-transition:enter="transition ease-out duration-150"
                                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                                    x-transition:enter-end="opacity-100 translate-y-0"
                                                    x-transition:leave="transition ease-in duration-100"
                                                    x-transition:leave-start="opacity-100"
                                                    x-transition:leave-end="opacity-0"
                                                    class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
                                                    <div class="p-1.5 space-y-0.5">
                                                        <button type="button"
                                                            @click="patientServiceOverrides[i - 1] = ''; open = false"
                                                            class="w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-colors"
                                                            :class="!patientServiceOverrides[i - 1] ?
                                                                'bg-teal-50 text-teal-700' :
                                                                'text-slate-400 hover:bg-slate-50'">—
                                                            Sama dengan layanan default</button>
                                                        <template x-for="service in services" :key="service.id">
                                                            <button type="button"
                                                                @click="patientServiceOverrides[i - 1] = service.id; open = false"
                                                                class="w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-colors"
                                                                :class="parseInt(patientServiceOverrides[i - 1]) === service
                                                                    .id ? 'bg-teal-50 text-teal-700' :
                                                                    'text-slate-700 hover:bg-slate-50'"
                                                                x-text="service.name"></button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- 7. ACTION --}}
                    <div class="space-y-4 pt-6">
                        <button type="button"
                            @click="
                            let step1Valid = true;
                            let requiredInputs = $refs.step1.querySelectorAll('input[required]:not(:disabled), textarea[required]:not(:disabled), select[required]:not(:disabled)');
                            for (let i = 0; i < requiredInputs.length; i++) {
                                if (!requiredInputs[i].checkValidity()) {
                                    requiredInputs[i].reportValidity();
                                    step1Valid = false;
                                    break;
                                }
                            }
                            
                            if (!step1Valid) return;

                            let missingService = false;
                            if (selectedServices.length === 0) {
                                for (let j = 0; j < slots; j++) {
                                    if (!patientServiceOverrides[j] || patientServiceOverrides[j] === '') {
                                        missingService = true;
                                        break;
                                    }
                                }
                            }

                            if (missingService) {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Peringatan',
                                        text: 'Layanan belum lengkap! Silakan pilih Layanan Utama atau pastikan setiap pasien memiliki Layanan Spesifik.',
                                        confirmButtonColor: '#0f766e',
                                        confirmButtonText: 'Mengerti',
                                        customClass: {
                                            popup: 'rounded-2xl',
                                            confirmButton: 'rounded-xl shadow-md'
                                        }
                                    });
                                } else {
                                    alert('Layanan belum lengkap! Silakan pilih Layanan Utama atau pastikan setiap pasien memiliki Layanan Spesifik.');
                                }
                                return;
                            }
                            
                            if (!selectedDate || !selectedTime) {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Peringatan',
                                        text: 'Tanggal dan Waktu harus dipilih!',
                                        confirmButtonColor: '#0f766e',
                                        confirmButtonText: 'Mengerti',
                                        customClass: {
                                            popup: 'rounded-2xl',
                                            confirmButton: 'rounded-xl shadow-md'
                                        }
                                    });
                                } else {
                                    alert('Tanggal dan Waktu harus dipilih!');
                                }
                                return;
                            }
                            
                            step = 2;
                        "
                            class="w-full text-center block py-5 bg-teal-800 text-white rounded-2xl text-lg font-semibold uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                            Lanjut
                        </button>
                        <div class="flex items-center justify-center gap-2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span class="text-xs font-semibold uppercase tracking-widest text-center">Pembayaran Aman &
                                Terenkripsi</span>
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
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Step 3 dari
                                5</span>
                        </div>
                        <h2 class="text-3xl font-semibold text-slate-800 tracking-tight leading-tight uppercase">Ringkasan
                            <br> Janji Temu
                        </h2>
                        <p class="text-base text-slate-500 font-semibold leading-relaxed">Cek kembali detail jadwal Anda
                            sebelum melakukan konfirmasi.</p>
                    </div>

                    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden p-6 space-y-8">

                        <div class="flex flex-col items-center text-center">
                            <p class="text-xs font-semibold text-teal-600 uppercase tracking-[0.2em] mb-4">Terapis Terpilih
                            </p>
                            <div class="relative mb-4">
                                <div
                                    class="w-20 h-20 bg-teal-50 rounded-full flex items-center justify-center border-2 border-white shadow-md text-teal-600 font-bold text-2xl">
                                    {{ $initials }}
                                </div>
                                <div
                                    class="absolute bottom-0 right-0 w-6 h-6 bg-teal-500 rounded-full border-2 border-white flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                        stroke-width="3" viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                            <h4 class="text-xl font-semibold text-slate-800">{{ $therapist->nama_karyawan }}</h4>
                            <p class="text-sm font-semibold text-slate-400 uppercase tracking-widest mt-1">Spesialis
                                Akupunktur</p>
                        </div>

                        <div class="bg-slate-50 rounded-[1.5rem] p-6 space-y-6">
                            <div class="flex gap-4">
                                <div
                                    class="w-10 h-10 shrink-0 bg-white rounded-xl shadow-sm flex items-center justify-center text-teal-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-1">
                                        Layanan Terpilih</p>
                                    {{-- Single slot: show global service name --}}
                                    <template x-if="slots === 1">
                                        <p class="text-base font-semibold text-slate-800 leading-tight"
                                            x-text="selectedServicesNames || 'Belum memilih layanan'"></p>
                                    </template>
                                    {{-- Group: show per-patient breakdown --}}
                                    <template x-if="slots > 1">
                                        <div class="space-y-2 mt-1">
                                            <template x-for="idx in Array.from({length: slots}, (_, k) => k)"
                                                :key="idx">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="text-[10px] font-semibold text-teal-600 bg-teal-50 rounded px-1.5 py-0.5 uppercase tracking-widest"
                                                        x-text="'P' + (idx + 1)"></span>
                                                    <span class="text-sm font-semibold text-slate-700"
                                                        x-text="getPatientServiceName(idx) || 'Belum dipilih'"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <p class="text-xs font-semibold text-slate-400 mt-1">Sesi Terapi</p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div
                                    class="w-10 h-10 shrink-0 bg-white rounded-xl shadow-sm flex items-center justify-center text-teal-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-1">
                                        Jadwal</p>
                                    <p class="text-base font-semibold text-slate-800 leading-tight"
                                        x-text="selectedDate ? formatDate(selectedDate) : ''"></p>
                                    <p class="text-xs font-semibold text-slate-400 mt-1"
                                        x-text="selectedTime ? formatTime(selectedTime) + ' WIB' : ''"></p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div
                                    class="w-10 h-10 shrink-0 bg-white rounded-xl shadow-sm flex items-center justify-center text-teal-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-1">
                                        Lokasi</p>
                                    <p class="text-base font-semibold text-slate-800 leading-tight">{{ $namaKolaborasi }}
                                    </p>
                                    <p class="text-xs font-semibold text-slate-400 mt-1 leading-relaxed">
                                        {{ $alamatKolaborasi }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 space-y-4">
                        {{-- Biaya Konsultasi --}}
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-slate-500">Biaya Konsultasi</span>
                            <span class="text-base font-semibold text-slate-800"
                                x-text="formatRupiah(totalConsultationCost)"></span>
                        </div>

                        {{-- Biaya Homecare (Hanya muncul jika > 0) --}}
                        <div x-show="biayaHomecare > 0" class="flex justify-between items-center animate-in fade-in">
                            <span class="text-sm font-semibold text-slate-500">Layanan Homecare</span>
                            <span class="text-base font-semibold text-slate-800"
                                x-text="formatRupiah(biayaHomecare)"></span>
                        </div>

                        {{-- Diskon (Hanya muncul jika ada diskon) --}}
                        <div x-show="diskon > 0"
                            class="flex justify-between items-center text-rose-600 animate-in fade-in">
                            <span class="text-sm font-semibold italic">Diskon Layanan (<span
                                    x-text="diskon"></span>%)</span>
                            <span class="text-base font-semibold" x-text="'-' + formatRupiah(discountAmount)"></span>
                        </div>

                        {{-- Admin --}}
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-slate-500">Biaya Admin</span>
                            <span class="text-base font-semibold text-slate-800">Rp 5.000</span>
                        </div>

                        <div class="pt-4 border-t border-slate-200 flex justify-between items-end">
                            <div>
                                <p class="text-[11px] font-semibold text-teal-600 uppercase tracking-widest">Grand Total
                                </p>
                                <p class="text-[10px] font-semibold text-slate-400 uppercase">Total Pembayaran</p>
                            </div>
                            <span class="text-2xl font-semibold text-slate-900 tracking-tight"
                                x-text="formatRupiah(grandTotal)"></span>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 text-center">
                        <button type="button" @click="step = 3"
                            class="text-center block w-full py-5 bg-teal-800 text-white rounded-2xl text-base font-semibold uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
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
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Step 4 dari
                                5</span>
                        </div>
                        <h2 class="text-3xl font-semibold text-slate-800 tracking-tight leading-tight uppercase">Verifikasi
                            <br> Pembayaran
                        </h2>
                        <p class="text-base text-slate-500 font-semibold leading-relaxed">Silakan transfer sesuai total
                            biaya ke rekening berikut untuk mengonfirmasi jadwal Anda.</p>
                    </div>

                    <div class="bg-[#2D7A78] rounded-[2rem] p-8 text-white shadow-xl shadow-teal-900/10 space-y-6">
                        <div class="flex items-center gap-3 border-b border-white/10 pb-4">
                            <svg class="w-5 h-5 text-teal-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="text-xs font-semibold uppercase tracking-[0.2em]">Rincian Pembayaran</span>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm font-medium opacity-90">
                                <span>Biaya Konsultasi</span>
                                <span x-text="formatRupiah(totalConsultationCost)"></span>
                            </div>
                            <div class="flex justify-between items-center text-sm font-medium opacity-90">
                                <span>Biaya Home Care</span>
                                <span x-text="formatRupiah(biayaHomecare)"></span>
                            </div>
                            <div x-show="diskon > 0"
                                class="flex justify-between items-center text-sm font-medium text-rose-300 opacity-100">
                                <span>Diskon Layanan (<span x-text="diskon"></span>%)</span>
                                <span x-text="'-' + formatRupiah(discountAmount)"></span>
                            </div>
                            <div class="flex justify-between items-center text-sm font-medium opacity-90">
                                <span>Biaya Admin</span>
                                <span>Rp 5.000</span>
                            </div>
                            <div class="pt-6 border-t border-white/20 flex justify-between items-end">
                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-widest opacity-60 mb-1">Grand
                                        Total</p>
                                    <p class="text-3xl font-semibold tracking-tight" x-text="formatRupiah(grandTotal)">
                                    </p>
                                </div>
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2rem] border border-slate-200 p-8 shadow-sm space-y-6">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-teal-600 border border-slate-100">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Bank Transfer
                                    Details</p>
                                <p class="text-base font-semibold text-slate-800">BCA (Bank Central Asia)</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest ml-1">Nomor
                                    Rekening</p>
                                <div
                                    class="flex items-center justify-between bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <span class="text-lg font-semibold text-slate-800 tracking-wider"
                                        x-text="accountNumber"></span>
                                    <button type="button" @click="copyToClipboard()"
                                        class="px-4 py-1.5 bg-white border border-slate-200 text-teal-600 text-[10px] font-semibold uppercase tracking-widest rounded-lg shadow-sm active:scale-95 transition-all">
                                        <span x-text="copied ? 'Tersalin!' : 'Copy'"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-1 ml-1">
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Nama Rekening
                                </p>
                                <p class="text-base font-semibold text-slate-800">Klinik Anjali</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2rem] border border-slate-200 p-8 shadow-sm text-center space-y-6">
                        <div class="flex items-center gap-4 text-left">
                            <div
                                class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-teal-600 border border-slate-100">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Bank Transfer
                                    Details</p>
                                <p class="text-base font-semibold text-slate-800">BCA (Bank Central Asia)</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest">QRIS - BCA</p>
                            <div class="mx-auto w-48 h-48 p-2 bg-white border-2 border-slate-50 rounded-2xl shadow-inner">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=AnjaliClinic"
                                    class="w-full h-full object-contain">
                            </div>
                            <div class="mt-4">
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Nama Rekening
                                </p>
                                <p class="text-base font-semibold text-slate-800">Klinik Anjali</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-2 px-1">
                            <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <h3 class="text-sm font-semibold text-slate-800 uppercase tracking-widest">Upload Bukti
                                Transfer</h3>
                        </div>

                        <label class="block group cursor-pointer">
                            <div class="bg-white border-2 border-dashed border-slate-200 rounded-[2rem] p-10 text-center space-y-4 group-hover:border-teal-500 transition-all shadow-sm relative overflow-hidden"
                                x-data="{ fileName: '' }">
                                <div
                                    class="w-14 h-14 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-slate-400 group-hover:text-teal-600 group-hover:bg-teal-50 transition-all">
                                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-base font-semibold text-slate-700"
                                        x-text="fileName ? fileName : 'Tap to select or take photo'"></p>
                                    <p class="text-xs font-medium text-slate-400 uppercase tracking-tighter"
                                        x-show="!fileName">JPEG, PNG, or PDF supported</p>
                                </div>
                                <input type="file" name="payment_proof"
                                    class="absolute inset-0 opacity-0 cursor-pointer"
                                    @change="fileName = $event.target.files[0]?.name ?? ''; paymentProofFileName = fileName;">
                            </div>
                        </label>
                    </div>

                    <div class="bg-teal-50/50 border border-teal-100 rounded-2xl p-5 flex gap-4">
                        <svg class="w-6 h-6 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-xs font-semibold text-teal-800 uppercase tracking-widest mb-1">Informasi
                                Verifikasi</p>
                            <p class="text-sm font-medium text-teal-700 leading-relaxed">Tim kami akan memverifikasi bukti
                                pembayaran Anda dalam waktu maksimal 2 jam.</p>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="button"
                            @click="
                        if (!paymentProofFileName) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Bukti Transfer Diperlukan',
                                    text: 'Silakan upload bukti transfer terlebih dahulu sebelum menyelesaikan booking.',
                                    confirmButtonColor: '#0f766e',
                                    confirmButtonText: 'Upload Sekarang',
                                    customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl shadow-md' }
                                });
                            } else {
                                alert('Silakan upload bukti transfer terlebih dahulu.');
                            }
                            return;
                        }
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Konfirmasi Pembayaran',
                                text: 'Apakah Anda yakin data dan bukti transfer sudah benar?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#0f766e',
                                cancelButtonColor: '#ef4444',
                                confirmButtonText: 'Ya, Selesaikan',
                                cancelButtonText: 'Periksa Lagi',
                                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-4 py-2', cancelButton: 'rounded-xl px-4 py-2' }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $el.closest('form').submit();
                                }
                            });
                        } else {
                            if (confirm('Apakah Anda yakin data dan bukti transfer sudah benar?')) {
                                $el.closest('form').submit();
                            }
                        }
                    "
                            class="text-center block w-full py-5 bg-teal-800 text-white rounded-2xl text-base font-bold uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                            Kirim & Selesaikan
                        </button>
                    </div>

                </div>

            </div>

        </form>

        <x-navigation.patient-navbar active="booking" />

    </x-layouts.mobile-app>

@endsection
