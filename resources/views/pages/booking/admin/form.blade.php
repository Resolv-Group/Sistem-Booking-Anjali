@extends('components.layouts.app')

@section('title', 'Booking Baru')

@section('content')

    @php
        // Logika Initial Name & Data sama seperti sebelumnya
        $patientName = auth()->user() ? auth()->user()->name : 'Fadi Budiman';
        $patientPublicId =
            auth()->user() && auth()->user()->pasien ? auth()->user()->pasien->pasien_public_id : 'PSN-12345-SM';
    @endphp

    <script>
        window.allTherapists = @json($therapists);
        window.patients = @json($patients);
    </script>

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{
        step: {{ old('current_step', 1) }},
        patientType: '{{ old('patient_type', 'terdaftar') }}',
        selectedTherapistId: {{ old('terapis_id') ? old('terapis_id') : 3 }},
        searchTherapist: '',
    
        therapists: window.allTherapists,
        patients: window.patients,
        searchPatientInput: '',
        complaint: '{{ old('complaint', '') }}',
        slots: {{ old('slots', 1) }},
        searchService: '',
        selectedDate: '{{ old('date', '') }}',
        timeType: '{{ old('time_type', '') }}',
        selectedTime: '{{ old('time', '') }}',
        selectedSessionId: '{{ old('terapis_sesi_id', '') }}',
        selectedServices: {{ old('services') ? old('services') : '[]' }},
    
        formatRupiah(amount) {
            return 'Rp' + amount.toLocaleString('id-ID');
        },
    
        get totalConsultationCost() {
            let total = 0;
            this.patientSlots.forEach(slot => {
                (slot.services || []).forEach(id => {
                    let s = this.services.find(sv => sv.id === id);
                    if (s) total += s.price;
                });
            });
            return total;
        },
    
        get discountAmount() {
            let total = 0;
            this.patientSlots.forEach(slot => {
                (slot.services || []).forEach(id => {
                    let s = this.services.find(sv => sv.id === id);
                    if (s) total += s.price * (s.discount / 100);
                });
            });
            return total;
        },
    
    
    
        get isHomecare() {
            let allIds = new Set();
            this.patientSlots.forEach(slot => (slot.services || []).forEach(id => allIds.add(id)));
            return [...allIds].some(id => {
                let s = this.services.find(sv => sv.id === id);
                return s && s.homecare_price > 0;
            });
        },
    
        get biayaHomecare() {
            if (this.isHomecare && this.currentTherapist) {
                return this.currentTherapist.homecare_price || 0;
            }
            return 0;
        },
    
        get grandTotal() {
            let anyServices = this.patientSlots.some(slot => slot.services && slot.services.length > 0);
            if (!anyServices) return 0;
            return (this.totalConsultationCost - this.discountAmount) + this.biayaHomecare + 5000;
        },
    
        get allSelectedServices() {
            let ids = new Set();
            this.patientSlots.forEach(slot => (slot.services || []).forEach(id => ids.add(id)));
            return [...ids];
        },
    
        init() {
            let dates = this.availableDates;
            if (dates.length > 0) {
                this.selectedDate = dates[0];
            } else {
                this.selectedDate = ''; // No available dates at all
            }
        },
    
        get currentTherapist() {
            return this.therapists.find(t => t.id === this.selectedTherapistId) || null;
        },
    
        // Filter daftar terapis berdasarkan input search
        get filteredTherapists() {
            if (!this.searchTherapist) return this.therapists;
            return this.therapists.filter(t => t.name.toLowerCase().includes(this.searchTherapist.toLowerCase()));
        },
    
        // Ambil sesi dari terapis terpilih
        get sessions() {
            return this.currentTherapist ? this.currentTherapist.sessions : [];
        },
    
        // Ambil layanan dari terapis terpilih
        get services() {
            return this.currentTherapist ? this.currentTherapist.services : [];
        },
    
        selectTherapist(id) {
            this.selectedTherapistId = id;
            // Reset pilihan jadwal & layanan jika ganti terapis
            this.selectedTime = '';
            this.selectedSessionId = null;
            this.selectedServices = [];
    
            let dates = this.availableDates;
            if (dates.length > 0) {
                this.selectedDate = dates[0];
            } else {
                this.selectedDate = '';
            }
            {{-- console.log(this.selectedTherapistId); --}}
        },
    
        // Logika availableDates sekarang mengambil dari 'this.sessions' (computed)
        get availableDates() {
            let today = new Date();
            let todayStr = today.getFullYear() + '-' +
                String(today.getMonth() + 1).padStart(2, '0') + '-' +
                String(today.getDate()).padStart(2, '0');
            let currentHour = today.getHours();
            let currentMinute = today.getMinutes();
    
            let validSessions = this.sessions.filter(s => {
                if (s.kuota_sisa <= 0) return false;
                if (s.tanggal_sesi < todayStr) return false;
                if (s.tanggal_sesi === todayStr) {
                    let [hour, minute] = s.waktu_mulai.split(':').map(Number);
                    if (hour < currentHour || (hour === currentHour && minute < currentMinute)) return false;
                }
                return true;
            });
            return [...new Set(validSessions.map(s => s.tanggal_sesi))].sort();
        },
    
        formatDate(dateStr) {
            if (!dateStr) return '';
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateStr).toLocaleDateString('id-ID', options);
        },
    
        formatTime(timeStr) {
            return timeStr ? timeStr.substring(0, 5) : '';
        },
    
        isPeriodDisabled(type) {
            const slots = this.timeSlotsForSelectedDate[type];
            if (!slots || slots.length === 0) return true;
            return slots.every(s => s.slots === 0 || s.isPast);
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
    
        toggleService(id) {
            if (this.selectedServices.includes(id)) {
                this.selectedServices = this.selectedServices.filter(x => x !== id);
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
    
        patientSlots: [
            { type: 'terdaftar', id: null, name: '', email: '', phone: '', dob: '', complaint: '', search: '', services: [] }
        ],
    
        addSlot() {
            if (!this.selectedSessionId) {
                Swal.fire('Peringatan', 'Pilih jadwal terlebih dahulu', 'warning');
                return;
            }
    
            let session = this.sessions.find(s => s.id == this.selectedSessionId);
            if (this.patientSlots.length < session.kuota_sisa && this.patientSlots.length < 5) {
                this.patientSlots.push({ type: 'terdaftar', id: null, name: '', email: '', phone: '', dob: '', complaint: '', search: '', services: [] });
            } else {
                Swal.fire('Penuh', 'Kuota sesi tidak mencukupi atau batas maksimal tercapai', 'error');
            }
        },
    
        removeSlot(index) {
            if (this.patientSlots.length > 1) {
                this.patientSlots.splice(index, 1);
            }
        },
    
        selectExistingPatient(slotIndex, p) {
            this.patientSlots[slotIndex].id = p.id;
            this.patientSlots[slotIndex].name = p.nama_pasien;
            this.patientSlots[slotIndex].email = p.email;
            this.patientSlots[slotIndex].phone = p.no_telp;
            this.patientSlots[slotIndex].dob = p.tanggal_lahir;
            this.patientSlots[slotIndex].search = ''; // clear search after select
        },
    
        getFilteredPatients(query) {
            if (!query) return [];
            return this.patients.filter(p =>
                p.nama_pasien.toLowerCase().includes(query.toLowerCase()) ||
                p.pasien_public_id.toLowerCase().includes(query.toLowerCase())
            );
        }
    
    }">

        {{-- TOPBAR --}}
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
                            Buat Janji Temu
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

            {{-- HERO SECTION --}}
            <div class="space-y-3">
                <h2 class="text-3xl font-bold text-[#0D4C4A] leading-tight">Buat Janji Temu <br> Baru</h2>
                <p class="text-sm text-slate-500 leading-relaxed font-medium">Bantu pasien menjadwalkan sesi dengan
                    spesialis terkait, silakan pilih layanan dan waktu yang tersedia.</p>
            </div>

            <form action="{{ route('admin-cabang.booking.store') }}" method="POST" class="space-y-6"
                enctype="multipart/form-data">
                @csrf

                {{-- ID Admin yang melakukan booking --}}
                <input type="hidden" name="admin_id" value="{{ auth()->id() }}">

                {{-- Data Utama --}}
                <input type="hidden" name="terapis_id" :value="selectedTherapistId">
                <input type="hidden" name="terapis_sesi_id" :value="selectedSessionId">
                <input type="hidden" name="services" :value="JSON.stringify(allSelectedServices)">
                <input type="hidden" name="date" :value="selectedDate">
                <input type="hidden" name="time" :value="selectedTime">

                {{-- Ambil jumlah slot dari panjang array patientSlots --}}
                <input type="hidden" name="slots" :value="patientSlots.length">

                {{-- INI KUNCI UTAMANYA: Mengirim data semua pasien (terdaftar maupun baru) --}}
                <input type="hidden" name="patients_data" :value="JSON.stringify(patientSlots)">

                {{-- 1. PEMILIHAN TERAPIS --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M19 21V19a7 7 0 00-7-7H9a7 7 0 00-7 7v2m0 0h14m-7 0a7 7 0 100 14 7 7 0 000-14z" />
                        </svg>
                        <div class="flex justify-between w-full gap-2">
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Pilih Terapis
                            </h3>
                            <span class="text-[10px] font-bold text-slate-300 uppercase">Step 1/5</span>
                        </div>
                    </div>

                    {{-- Search Terapis --}}
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" x-model="searchTherapist" placeholder="Cari nama dokter"
                            class="w-full pl-11 pr-4 py-3.5 bg-white border border-slate-100 rounded-xl text-sm focus:ring-2 focus:ring-teal-100 outline-none transition-all">
                    </div>

                    {{-- List Terapis Dinamis --}}
                    <div class="space-y-3">
                        <template x-for="therapist in filteredTherapists" :key="therapist.id">
                            <button type="button" @click="selectTherapist(therapist.id)"
                                :class="selectedTherapistId === therapist.id ? 'border-teal-500 ring-1 ring-teal-500' :
                                    'border-slate-100'"
                                class="w-full p-4 bg-white border-2 rounded-2xl flex items-center gap-4 text-left shadow-sm relative transition-all">

                                <div class="w-12 h-12 bg-slate-100 rounded-full overflow-hidden border border-slate-50">
                                    <img :src="therapist.image" class="w-full h-full object-cover">
                                </div>

                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-[#0D4C4A]" x-text="therapist.name"></h4>
                                    <p class="text-[11px] font-medium text-slate-400">Spesialis Akupunktur</p>
                                </div>

                                {{-- Checkmark Icon --}}
                                <div x-show="selectedTherapistId === therapist.id"
                                    class="w-5 h-5 bg-teal-500 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        stroke-width="3">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div class="flex justify-between w-full gap-2">
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Pilih Tanggal & Waktu
                            </h3>
                            <span class="text-[10px] font-bold text-slate-300 uppercase">Step 2/5</span>
                        </div>
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

                {{-- 4. PEMILIHAN PASIEN --}}
                {{-- 4 & 5. PEMILIHAN & KELUHAN PASIEN (LOOPED) --}}
                <template x-for="(slot, index) in patientSlots" :key="index">
                    <div
                        class="space-y-6 p-6 bg-white rounded-[2rem] border border-slate-100 shadow-sm relative overflow-visible">

                        {{-- Header Slot --}}
                        <div class="flex justify-between items-center">
                            <h3 class="text-xs font-bold text-[#0D4C4A] uppercase tracking-widest"
                                x-text="'Pasien ' + (index + 1)"></h3>
                            <button x-show="index > 0" @click="removeSlot(index)"
                                class="text-rose-500 text-[10px] font-bold uppercase">Hapus Slot</button>
                        </div>

                        {{-- Tabs --}}
                        <div class="flex p-1 bg-slate-50 rounded-xl">
                            <button type="button" @click="slot.type = 'terdaftar'"
                                :class="slot.type === 'terdaftar' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-400'"
                                class="flex-1 py-2.5 text-xs font-bold rounded-lg transition-all">Pasien Terdaftar</button>
                            <button type="button" @click="slot.type = 'baru'"
                                :class="slot.type === 'baru' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-400'"
                                class="flex-1 py-2.5 text-xs font-bold rounded-lg transition-all">Pasien Baru</button>
                        </div>

                        {{-- Content: Pasien Terdaftar --}}
                        <div x-show="slot.type === 'terdaftar'" class="space-y-4">
                            <div class="relative">
                                <input type="text" x-model="slot.search" placeholder="Cari nama atau ID pasien..."
                                    class="w-full pl-4 pr-4 py-3.5 bg-[#EDF1F3] border-none rounded-xl text-sm outline-none">

                                {{-- Dropdown Search Results --}}
                                <div x-show="slot.search.length > 1"
                                    class="absolute z-50 w-full mt-2 bg-white border border-slate-100 rounded-xl shadow-xl max-h-40 overflow-y-auto">
                                    <template x-for="p in getFilteredPatients(slot.search)" :key="p.id">
                                        <button @click="selectExistingPatient(index, p)"
                                            class="w-full text-left p-3 hover:bg-teal-50 border-b border-slate-50 last:border-0">
                                            <p class="text-sm font-bold text-slate-700" x-text="p.nama_pasien"></p>
                                            <p class="text-[10px] text-slate-400" x-text="p.pasien_public_id"></p>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            {{-- Selected Patient Info --}}
                            <div x-show="slot.id"
                                class="p-4 bg-teal-50 border border-teal-100 rounded-2xl flex items-center gap-4">
                                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-teal-600 font-bold text-xs"
                                    x-text="slot.name.substring(0,2).toUpperCase()"></div>
                                <div>
                                    <p class="text-sm font-bold text-[#0D4C4A]" x-text="slot.name"></p>
                                    <p class="text-[10px] text-teal-600 font-medium" x-text="slot.dob"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Content: Pasien Baru --}}
                        <div x-show="slot.type === 'baru'" class="space-y-4 animate-in fade-in duration-300">
                            <input type="text" x-model="slot.name" placeholder="Nama Lengkap"
                                class="w-full px-4 py-3 bg-[#EDF1F3] border-none rounded-xl text-sm font-medium outline-none">
                            <div class="grid grid-cols-2 gap-3">
                                <input type="email" x-model="slot.email" placeholder="Email"
                                    class="w-full px-4 py-3 bg-[#EDF1F3] border-none rounded-xl text-sm font-medium outline-none">
                                <input type="tel" x-model="slot.phone" placeholder="No. Telepon"
                                    class="w-full px-4 py-3 bg-[#EDF1F3] border-none rounded-xl text-sm font-medium outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Tanggal Lahir</label>
                                <input type="date" x-model="slot.dob" max="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-3 bg-[#EDF1F3] border-none rounded-xl text-sm font-medium outline-none">
                            </div>
                        </div>

                        {{-- Keluhan Section --}}
                        <div class="space-y-3 pt-4 border-t border-slate-50">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Keluhan
                                Pasien</label>
                            <div class="relative">
                                <textarea x-model="slot.complaint" placeholder="Ceritakan keluhan pasien..."
                                    class="w-full p-5 bg-[#EDF1F3] border-none rounded-2xl text-sm font-medium h-24 focus:ring-2 focus:ring-teal-100 outline-none resize-none"></textarea>
                            </div>
                        </div>

                        {{-- Custom Layanan Spesifik Dropdown (Opsional) --}}
                        {{-- Layanan Pasien (Chip Selection) --}}
                        <div class="space-y-3 pt-4 border-t border-slate-50">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Layanan
                                Dipilih</label>

                            {{-- Selected chips --}}
                            <div class="flex flex-wrap gap-2 min-h-[2rem]">
                                <template x-for="id in (slot.services || [])" :key="id">
                                    <div
                                        class="flex items-center gap-2 px-3 py-2 bg-teal-50 border border-teal-200 rounded-xl text-xs font-bold text-teal-700 transition-all">
                                        <svg class="w-3 h-3 text-teal-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="3">
                                            <path d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span x-text="services.find(s => s.id === id)?.name || ''"></span>
                                        <span class="text-teal-400 text-[10px] font-semibold"
                                            x-text="'· ' + formatRupiah(services.find(s => s.id === id)?.price || 0)"></span>
                                        <button type="button"
                                            @click="slot.services = (slot.services || []).filter(x => x !== id)"
                                            class="ml-1 w-4 h-4 rounded-full bg-teal-200 hover:bg-rose-200 flex items-center justify-center text-teal-600 hover:text-rose-600 transition-colors">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" stroke-width="3">
                                                <path d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                                <div x-show="!slot.services || slot.services.length === 0"
                                    class="text-xs text-slate-400 font-medium italic py-1">
                                    Belum ada layanan dipilih
                                </div>
                            </div>

                            {{-- Add layanan dropdown --}}
                            <div class="relative" x-data="{ openLayanan: false }">
                                <button type="button" @click="openLayanan = !openLayanan"
                                    @click.outside="openLayanan = false"
                                    class="flex items-center gap-2 px-4 py-2.5 bg-white border-2 border-dashed border-slate-200 rounded-xl text-xs font-bold text-slate-400 hover:border-teal-300 hover:text-teal-500 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        stroke-width="3">
                                        <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Tambah Layanan
                                </button>

                                <div x-show="openLayanan" x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                    class="absolute z-50 left-0 mt-2 w-72 bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
                                    <div class="p-2 space-y-1 max-h-56 overflow-y-auto">
                                        <template x-for="service in services" :key="service.id">
                                            <button type="button"
                                                @click="
                            if (!slot.services) slot.services = [];
                            if (slot.services.includes(service.id)) {
                                slot.services = slot.services.filter(id => id !== service.id);
                            } else {
                                slot.services = [...slot.services, service.id];
                            }
                        "
                                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-left transition-colors group"
                                                :class="slot.services && slot.services.includes(service.id) ?
                                                    'bg-teal-50 text-teal-700' :
                                                    'text-slate-700 hover:bg-slate-50'">
                                                <div>
                                                    <p class="text-xs font-bold" x-text="service.name"></p>
                                                    <p class="text-[10px] text-slate-400 font-medium"
                                                        x-text="formatRupiah(service.price)"></p>
                                                </div>
                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all shrink-0"
                                                    :class="slot.services && slot.services.includes(service.id) ?
                                                        'border-teal-500 bg-teal-500' :
                                                        'border-slate-200 group-hover:border-teal-300'">
                                                    <svg x-show="slot.services && slot.services.includes(service.id)"
                                                        class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="3">
                                                        <path d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Tombol Tambah Slot (Sekarang berfungsi) --}}
                <button type="button" @click="addSlot()"
                    class="w-full py-4 border-2 border-dashed border-slate-200 rounded-2xl flex items-center justify-center gap-2 text-slate-400 text-xs font-bold uppercase tracking-widest hover:border-teal-300 hover:text-teal-500 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Slot Pasien
                </button>

                {{-- SUMMARY CARD --}}
                <div class="bg-white rounded-[2rem] p-8 shadow-2xl shadow-teal-900/10 space-y-8 border border-slate-50">

                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-[#0D4C4A]">Rekap Janji</h3>
                        <span
                            class="px-3 py-1 bg-teal-50 text-teal-600 text-[10px] font-bold rounded-lg uppercase border border-teal-100"
                            x-text="patientSlots.length.toString().padStart(2, '0') + ' Pasien'"></span>
                    </div>

                    {{-- Detail Pasien di Rekap --}}
                    <div class="space-y-4">
                        <template x-for="(slot, i) in patientSlots" :key="i">
                            <div class="flex gap-4 p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-teal-500 text-xs font-bold"
                                    x-text="i+1"></div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-700" x-text="slot.name || 'Nama belum diisi'">
                                    </h4>
                                    <p class="text-[10px] font-medium text-slate-400"
                                        x-text="slot.type === 'terdaftar' ? 'Pasien Terdaftar' : 'Pasien Baru'"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Detail Harga --}}
                    <div x-show="patientSlots.some(slot => slot.services && slot.services.length > 0)"
                        class="border-t border-slate-100 pt-6 space-y-4 animate-in fade-in duration-300">
                        <div class="text-xs font-bold text-teal-800 uppercase tracking-widest">Rincian Biaya</div>

                        {{-- List Layanan yang Dipilih --}}
                        <div class="space-y-3">
                            <template x-for="(slot, i) in patientSlots" :key="i">
                                <div x-show="slot.services && slot.services.length > 0" class="space-y-1">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase" x-text="'Pasien ' + (i+1)">
                                    </p>
                                    <template x-for="id in (slot.services || [])" :key="id">
                                        <div
                                            class="flex justify-between items-center text-sm font-medium text-slate-500 pl-2">
                                            <span x-text="services.find(s => s.id === id)?.name || 'Layanan'"></span>
                                            <span class="text-slate-800"
                                                x-text="formatRupiah(services.find(s => s.id === id)?.price || 0)"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- Multiplier Pasien jika pasien > 1 --}}
                        <div x-show="patientSlots.length > 1"
                            class="flex justify-between items-center text-xs text-slate-400 font-semibold border-b border-dashed border-slate-100 pb-3">
                            <span>Jumlah Pasien</span>
                            <span x-text="'x ' + patientSlots.length + ' Pasien'"></span>
                        </div>

                        {{-- Subtotal Biaya Konsultasi --}}
                        <div class="flex justify-between items-center text-sm font-medium text-slate-600">
                            <span>Subtotal Konsultasi</span>
                            <span class="text-slate-800 font-bold" x-text="formatRupiah(totalConsultationCost)"></span>
                        </div>

                        {{-- Diskon --}}
                        <div x-show="discountAmount > 0"
                            class="flex justify-between items-center text-sm font-semibold text-rose-600">
                            <span>Diskon Layanan</span>
                            <span x-text="'-' + formatRupiah(discountAmount)"></span>
                        </div>

                        {{-- Biaya Homecare --}}
                        <div class="flex justify-between items-center text-sm font-medium text-slate-600">
                            <span>Biaya Homecare</span>
                            <span x-text="biayaHomecare ? formatRupiah(biayaHomecare) : 'Rp0'"></span>
                        </div>

                        {{-- Biaya Admin --}}
                        <div class="flex justify-between items-center text-sm font-medium text-slate-600">
                            <span>Biaya Admin</span>
                            <span class="text-slate-800 font-bold">Rp5.000</span>
                        </div>

                        {{-- Grand Total --}}
                        <div class="pt-4 border-t border-slate-100 flex justify-between items-end">
                            <div>
                                <p class="text-[11px] font-bold text-[#0D4C4A] uppercase tracking-widest">Total Pembayaran
                                </p>
                            </div>
                            <span class="text-2xl font-bold text-slate-900 tracking-tight"
                                x-text="formatRupiah(grandTotal)"></span>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4">
                        <button type="submit"
                            class="w-full py-5 bg-[#2D7A78] text-white rounded-2xl text-base font-bold shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                            Simpan Janji
                        </button>
                        <p class="text-[10px] text-center text-slate-400 leading-relaxed px-4 font-medium">
                            Mohon check kembali data sebelum menyimpan, klik Simpan Janji untuk melanjutkan ke proses
                            pembayaran.
                        </p>
                    </div>
                </div>
            </form>

            {{-- POLICY FOOTER --}}
            <div class="p-5 bg-teal-50 border border-teal-100 rounded-2xl flex gap-4">
                <div
                    class="w-10 h-10 shrink-0 bg-white rounded-xl flex items-center justify-center text-teal-600 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h5 class="text-[11px] font-bold text-teal-800 uppercase tracking-widest mb-0.5">Privasi Kesehatan
                        Pasien</h5>
                    <p class="text-[10px] text-teal-700 leading-relaxed font-medium">Data yang anda masukkan terjaga
                        kerahasiaannya dan hanya digunakan untuk keperluan medis di Klinik Anjali.</p>
                </div>
            </div>

        </div>

        <x-navigation.admin-cabang-navbar active="booking" />

    </x-layouts.mobile-app>

@endsection
