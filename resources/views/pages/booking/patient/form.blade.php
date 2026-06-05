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

        $photoPreview = $therapist->foto
            ? 'data:' . ($therapist->foto_mime ?? 'image/jpg') . ';base64,' . $therapist->foto
            : asset('images/logo_anjali.jpg'); 
    @endphp

    <script>
        window.bookingServices = @json($services);
        window.bookingSessions = @json($sessions);
        window.bookingPatients = @json($patients);
    </script>

    <x-layouts.mobile-app class="bg-slate-50 min-h-screen" x-data="{
        step: {{ old('current_step', 1) }},
        slots: {{ old('slots', 1) }},
        paymentProofFileName: '',
        services: window.bookingServices,
        sessions: window.bookingSessions,
        selectedDate: '{{ old('date', '') }}',
        timeType: '{{ old('time_type', 'pagi') }}',
        selectedTime: '{{ old('time', '') }}',
        selectedSessionId: '{{ old('terapis_sesi_id', '') }}',
        biayaHomecare: 0,
    
        init() {
            let dates = this.availableDates;
            if (dates.length > 0) {
                this.selectedDate = dates[0];
            } else {
                this.selectedDate = ''; // No available dates at all
            }
        },
    
        get availableDates() {
            let today = new Date();
            // Format today as YYYY-MM-DD
            let todayStr = today.getFullYear() + '-' +
                String(today.getMonth() + 1).padStart(2, '0') + '-' +
                String(today.getDate()).padStart(2, '0');
            let currentHour = today.getHours();
            let currentMinute = today.getMinutes();
    
            // Filter sessions first
            let validSessions = this.sessions.filter(s => {
                // 1. Must have at least 1 slot remaining
                if (s.kuota_sisa <= 0) return false;
    
                // 2. Ignore past dates entirely
                if (s.tanggal_sesi < todayStr) return false;
    
                // 3. If it's today, ignore past hours/minutes
                if (s.tanggal_sesi === todayStr) {
                    let [hour, minute] = s.waktu_mulai.split(':').map(Number);
                    if (hour < currentHour || (hour === currentHour && minute < currentMinute)) {
                        return false;
                    }
                }
    
                return true;
            });
    
            // Extract unique dates from the filtered valid sessions
            return [...new Set(validSessions.map(s => s.tanggal_sesi))].sort();
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
    
        patientSlots: [
            { complaint: '{{ old('patient_complaints.0', '') }}', services: [], type: 'utama', id: null, name: '', public_id: '', dob: '', search: '' }
        ],
    
        // Add these to the Alpine x-data object
        searchPatients: window.bookingPatients || [],
    
        // Modal state
        showNewPatientModal: false,
        newPatientTargetIndex: null,
        newPatientForm: { name: '', phone: '', dob: '' },
        newPatientSaving: false,
        newPatientSearchQuery: '',
    
        openNewPatientModal(index, prefillName) {
            this.newPatientTargetIndex = index;
            this.newPatientForm = { name: prefillName || '', phone: '', dob: '' };
            this.showNewPatientModal = true;
            document.body.style.overflow = 'hidden';
        },
    
        closeNewPatientModal() {
            this.showNewPatientModal = false;
            this.newPatientTargetIndex = null;
            document.body.style.overflow = '';
        },
    
        saveNewPatient() {
            if (!this.newPatientForm.name || !this.newPatientForm.phone || !this.newPatientForm.dob) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Lengkapi Data',
                    text: 'Nama, No HP, dan Tanggal Lahir wajib diisi.',
                    confirmButtonColor: '#0f766e',
                    customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl' }
                });
                return;
            }
    
            let idx = this.newPatientTargetIndex;
            this.patientSlots[idx].id = 'new_' + Date.now();
            this.patientSlots[idx].name = this.newPatientForm.name;
            this.patientSlots[idx].phone = this.newPatientForm.phone;
            this.patientSlots[idx].dob = this.newPatientForm.dob;
            this.patientSlots[idx].public_id = 'Pasien Baru';
            this.patientSlots[idx].type = 'baru';
            this.patientSlots[idx].search = '';
    
            this.closeNewPatientModal();
        },
    
        getFilteredPatients(query) {
            if (!query || query.length < 2) return [];
            return this.searchPatients.filter(p =>
                p.nama_pasien.toLowerCase().includes(query.toLowerCase()) ||
                p.pasien_public_id.toLowerCase().includes(query.toLowerCase())
            );
        },
    
        selectExistingPatient(slotIndex, p) {
            this.patientSlots[slotIndex].id = p.id;
            this.patientSlots[slotIndex].name = p.nama_pasien;
            this.patientSlots[slotIndex].public_id = p.pasien_public_id;
            this.patientSlots[slotIndex].dob = p.tanggal_lahir;
            this.patientSlots[slotIndex].type = 'terdaftar';
            this.patientSlots[slotIndex].search = '';
        },
    
        get allSelectedServices() {
            let ids = new Set();
            this.patientSlots.forEach(slot => (slot.services || []).forEach(id => ids.add(id)));
            return [...ids];
        },
    
        // Verifikasi data
        accountNumber: '87923998',
        copied: false,
        copyToClipboard() {
            navigator.clipboard.writeText(this.accountNumber);
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
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
    
        get diskon() {
            // Keep for display percentage — derive from first service found across slots
            let firstId = null;
            for (let slot of this.patientSlots) {
                if (slot.services && slot.services.length > 0) { firstId = slot.services[0]; break; }
            }
            if (!firstId) return 0;
            let s = this.services.find(sv => sv.id === firstId);
            return s ? s.discount : 0;
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
            if (this.isHomecare) {
                return {{ (int) ($therapist->kolaborasi->homecare_harga ?? 0) }};
            }
            return 0;
        },
    
        get grandTotal() {
            let anyServices = this.patientSlots.some(slot => slot.services && slot.services.length > 0);
            if (!anyServices) return 0;
            return (this.totalConsultationCost - this.discountAmount) + this.biayaHomecare + 5000;
        },
    
        get slots() {
            return this.patientSlots.length;
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
            <input type="hidden" name="services" :value="JSON.stringify(allSelectedServices)">
            <input type="hidden" name="date" :value="selectedDate" value="{{ old('date') }}">
            <input type="hidden" name="time" :value="selectedTime" value="{{ old('time') }}">
            <input type="hidden" name="slots" :value="patientSlots.length">
            <input type="hidden" name="terapis_sesi_id" :value="selectedSessionId" value="{{ old('terapis_sesi_id') }}">
            <input type="hidden" name="patients_data" :value="JSON.stringify(patientSlots)">

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
                            <img src="{{ $photoPreview }}"
                                alt="{{ $therapist->nama_karyawan }}"
                                class="w-11 h-11 bg-teal-50 rounded-xl flex items-center justify-center text-teal-600 font-semibold text-lg">
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
                                {{-- Stepper buttons --}}
                                <button type="button" @click="if(patientSlots.length > 1) patientSlots.pop()"
                                    class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm text-slate-400 font-semibold text-xl">-</button>
                                <span class="w-12 text-center font-semibold text-slate-800 text-xl"
                                    x-text="patientSlots.length"></span>
                                <button type="button"
                                    @click="
        if(patientSlots.length < 5) {
            let canIncrement = true;
            if(selectedSessionId) {
                let currentSession = sessions.find(s => s.id === selectedSessionId);
                if (currentSession && (patientSlots.length + 1) > currentSession.kuota_sisa) {
                    canIncrement = false;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Slot Tidak Cukup',
                        text: `Hanya ada ${currentSession.kuota_sisa} slot tersisa.`,
                        confirmButtonColor: '#0f766e',
                        confirmButtonText: 'Mengerti',
                        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl shadow-md' }
                    });
                }
            }
            if (canIncrement) patientSlots.push({ complaint: '', services: [], type: 'terdaftar', id: null, name: '', public_id: '', dob: '', search: '' });
        }
    "
                                    class="w-10 h-10 flex items-center justify-center bg-teal-800 rounded-xl shadow-sm text-white font-semibold text-xl hover:bg-teal-700 transition">+</button>
                            </div>
                        </div>
                    </div>

                    {{-- 6. DETAIL PASIEN LOGIC --}}
                    {{-- 6. DETAIL PASIEN --}}
                    <div class="space-y-6 pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Detail Pasien</h3>
                        </div>

                        <template x-for="(slot, index) in patientSlots" :key="index">
                            <div class="p-6 bg-white border border-slate-200 rounded-2xl space-y-5 shadow-sm">

                                {{-- Header --}}
                                <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                                    <span class="text-xs font-semibold uppercase tracking-widest px-2 py-1 rounded"
                                        :class="index === 0 ? 'text-teal-600 bg-teal-50' : 'text-slate-400 bg-slate-50'"
                                        x-text="index === 0 ? 'Pasien 1 (Utama)' : 'Pasien ' + (index + 1)"></span>
                                </div>

                                {{-- PATIENT 1: always the logged-in user, static display --}}
                                <div x-show="index === 0">
                                    <p class="text-base font-semibold text-slate-800 uppercase tracking-widest">
                                        {{ $patientName }}</p>
                                    <p class="text-xs text-slate-400 font-semibold uppercase mt-1">ID:
                                        {{ $patientPublicId }}</p>
                                </div>

                                {{-- PATIENTS 2+: search or registered display --}}
                                <div x-show="index > 0" class="space-y-3">

                                    {{-- If patient already selected: show card --}}
                                    <div x-show="slot.id"
                                        class="p-4 bg-teal-50 border border-teal-100 rounded-2xl flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-teal-600 font-bold text-xs shrink-0"
                                                x-text="slot.name ? slot.name.substring(0,2).toUpperCase() : '?'"></div>
                                            <div>
                                                <p class="text-sm font-bold text-[#0D4C4A]" x-text="slot.name"></p>
                                                <p class="text-[10px] text-teal-600 font-medium"
                                                    x-text="slot.public_id || slot.dob"></p>
                                            </div>
                                        </div>
                                        <button type="button"
                                            @click="slot.id = null; slot.name = ''; slot.public_id = ''; slot.dob = ''; slot.search = ''"
                                            class="text-[10px] font-bold text-rose-400 uppercase hover:text-rose-600 shrink-0">Ganti</button>
                                    </div>

                                    {{-- If no patient selected yet: search UI --}}
                                    <div x-show="!slot.id" class="space-y-2">
                                        <div class="relative">
                                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                            <input type="text" x-model="slot.search"
                                                placeholder="Cari nama atau ID pasien..."
                                                class="w-full pl-10 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-teal-100 transition-all">
                                        </div>

                                        {{-- Search results dropdown --}}
                                        <div x-show="slot.search.length >= 2" class="relative">
                                            <div
                                                class="w-full bg-white border border-slate-100 rounded-xl shadow-xl max-h-44 overflow-y-auto">

                                                {{-- Results --}}
                                                <template x-for="p in getFilteredPatients(slot.search)"
                                                    :key="p.id">
                                                    <button type="button" @click="selectExistingPatient(index, p)"
                                                        class="w-full text-left p-3 hover:bg-teal-50 border-b border-slate-50 last:border-0 transition-colors">
                                                        <p class="text-sm font-bold text-slate-700"
                                                            x-text="p.nama_pasien"></p>
                                                        <p class="text-[10px] text-slate-400 font-medium"
                                                            x-text="p.pasien_public_id"></p>
                                                    </button>
                                                </template>

                                                {{-- No results state --}}
                                                <div x-show="getFilteredPatients(slot.search).length === 0"
                                                    class="p-4 text-center space-y-3">
                                                    <p class="text-xs text-slate-400 font-semibold">Pasien "<span
                                                            x-text="slot.search" class="text-slate-600"></span>" tidak
                                                        ditemukan.</p>
                                                    <button type="button"
                                                        @click="openNewPatientModal(index, slot.search)"
                                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-teal-600 text-white rounded-xl text-xs font-bold shadow-sm hover:bg-teal-700 active:scale-95 transition-all">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24" stroke-width="3">
                                                            <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                        </svg>
                                                        Daftarkan Pasien Baru
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Hint when search is empty --}}
                                        <div x-show="slot.search.length < 2 && slot.search.length > 0"
                                            class="text-[10px] text-slate-400 font-medium px-1">
                                            Ketik minimal 2 karakter untuk mencari...
                                        </div>
                                    </div>
                                </div>

                                {{-- Keluhan --}}
                                <div class="space-y-2 pt-4 border-t border-slate-50">
                                    <label class="text-xs font-semibold text-teal-700 uppercase tracking-widest">Keluhan
                                        Utama</label>
                                    <textarea x-model="slot.complaint" placeholder="Ceritakan keluhan..."
                                        class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-base font-semibold h-24 outline-none focus:ring-2 focus:ring-teal-100 resize-none"></textarea>
                                </div>

                                {{-- Layanan Chip Selection — unchanged from before --}}
                                <div class="space-y-3 pt-4 border-t border-slate-100">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Layanan
                                        Dipilih</label>
                                    <div class="flex flex-wrap gap-2 min-h-[2rem]">
                                        <template x-for="id in (slot.services || [])" :key="id">
                                            <div
                                                class="flex items-center gap-2 px-3 py-2 bg-teal-50 border border-teal-200 rounded-xl text-xs font-bold text-teal-700 transition-all">
                                                <svg class="w-3 h-3 text-teal-500 shrink-0" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
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
                                            class="text-xs text-slate-400 font-medium italic py-1">Belum ada layanan
                                            dipilih</div>
                                    </div>
                                    <div class="relative" x-data="{ openLayanan: false }">
                                        <button type="button" @click="openLayanan = !openLayanan"
                                            @click.outside="openLayanan = false"
                                            class="flex items-center gap-2 px-4 py-2.5 bg-white border-2 border-dashed border-slate-200 rounded-xl text-xs font-bold text-slate-400 hover:border-teal-300 hover:text-teal-500 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" stroke-width="3">
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
                                                        @click="if (!slot.services) slot.services = []; if (slot.services.includes(service.id)) { slot.services = slot.services.filter(id => id !== service.id); } else { slot.services = [...slot.services, service.id]; }"
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
                                                                class="w-3 h-3 text-white" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor"
                                                                stroke-width="3">
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

                            let missingService = patientSlots.some(slot => !slot.services || slot.services.length === 0);
                            if (missingService) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Peringatan',
                                    text: 'Setiap pasien harus memiliki minimal satu layanan.',
                                    confirmButtonColor: '#0f766e',
                                    confirmButtonText: 'Mengerti',
                                    customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl shadow-md' }
                                });
                                return;
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
                                <div class="flex-1">
                                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-2">
                                        Layanan Terpilih</p>
                                    <div class="space-y-3">
                                        <template x-for="(slot, idx) in patientSlots" :key="idx">
                                            <div class="space-y-1">
                                                {{-- Patient name label --}}
                                                <p class="text-[10px] font-bold text-teal-600 uppercase tracking-widest"
                                                    x-text="idx === 0 ? '{{ $patientName }}' : (slot.name || 'Pasien ' + (idx + 1))">
                                                </p>

                                                {{-- Services list --}}
                                                <template x-if="slot.services && slot.services.length > 0">
                                                    <div class="space-y-0.5 pl-2 border-l-2 border-teal-100">
                                                        <template x-for="serviceId in slot.services"
                                                            :key="serviceId">
                                                            <div class="flex items-center justify-between gap-2">
                                                                <div class="flex items-center gap-1.5">
                                                                    <div class="w-1 h-1 rounded-full bg-teal-400 shrink-0">
                                                                    </div>
                                                                    <span class="text-xs font-semibold text-slate-700"
                                                                        x-text="services.find(s => s.id === serviceId)?.name || '-'"></span>
                                                                </div>
                                                                <span
                                                                    class="text-[10px] font-semibold text-slate-400 shrink-0"
                                                                    x-text="formatRupiah(services.find(s => s.id === serviceId)?.price || 0)"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>

                                                <template x-if="!slot.services || slot.services.length === 0">
                                                    <p class="text-xs text-rose-400 font-semibold pl-2">Belum ada layanan
                                                        dipilih</p>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <p class="text-[10px] font-semibold text-slate-400 mt-3">Sesi Terapi</p>
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

                        {{-- Per-patient per-service breakdown --}}
                        <div class="space-y-4">
                            <template x-for="(slot, idx) in patientSlots" :key="idx">
                                <div x-show="slot.services && slot.services.length > 0" class="space-y-1.5">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                        x-text="idx === 0 ? '{{ $patientName }}' : (slot.name || 'Pasien ' + (idx + 1))">
                                    </p>
                                    <template x-for="serviceId in (slot.services || [])" :key="serviceId">
                                        <div class="flex justify-between items-center pl-2">
                                            <span class="text-sm font-semibold text-slate-600"
                                                x-text="services.find(s => s.id === serviceId)?.name || '-'"></span>
                                            <span class="text-sm font-semibold text-slate-800"
                                                x-text="formatRupiah(services.find(s => s.id === serviceId)?.price || 0)"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <div class="border-t border-slate-200 pt-4 space-y-3">
                            {{-- Subtotal --}}
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-slate-500">Subtotal Konsultasi</span>
                                <span class="text-sm font-semibold text-slate-800"
                                    x-text="formatRupiah(totalConsultationCost)"></span>
                            </div>

                            {{-- Homecare --}}
                            <div x-show="biayaHomecare > 0" class="flex justify-between items-center animate-in fade-in">
                                <span class="text-sm font-semibold text-slate-500">Layanan Homecare</span>
                                <span class="text-sm font-semibold text-slate-800"
                                    x-text="formatRupiah(biayaHomecare)"></span>
                            </div>

                            {{-- Diskon --}}
                            <div x-show="discountAmount > 0"
                                class="flex justify-between items-center text-rose-600 animate-in fade-in">
                                <span class="text-sm font-semibold italic">Diskon Layanan</span>
                                <span class="text-sm font-semibold" x-text="'-' + formatRupiah(discountAmount)"></span>
                            </div>

                            {{-- Admin --}}
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-slate-500">Biaya Admin</span>
                                <span class="text-sm font-semibold text-slate-800">Rp 5.000</span>
                            </div>
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
                            {{-- Per-patient breakdown --}}
                            <div class="space-y-3 pb-2 border-b border-white/10">
                                <template x-for="(slot, idx) in patientSlots" :key="idx">
                                    <div x-show="slot.services && slot.services.length > 0" class="space-y-1">
                                        <p class="text-[10px] font-bold text-teal-200 uppercase tracking-widest"
                                            x-text="idx === 0 ? '{{ $patientName }}' : (slot.name || 'Pasien ' + (idx + 1))">
                                        </p>
                                        <template x-for="serviceId in (slot.services || [])" :key="serviceId">
                                            <div
                                                class="flex justify-between items-center pl-2 text-sm font-medium opacity-90">
                                                <span x-text="services.find(s => s.id === serviceId)?.name || '-'"></span>
                                                <span
                                                    x-text="formatRupiah(services.find(s => s.id === serviceId)?.price || 0)"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            {{-- Subtotal --}}
                            <div class="flex justify-between items-center text-sm font-medium opacity-90">
                                <span>Subtotal Konsultasi</span>
                                <span x-text="formatRupiah(totalConsultationCost)"></span>
                            </div>

                            {{-- Homecare --}}
                            <div class="flex justify-between items-center text-sm font-medium opacity-90">
                                <span>Biaya Home Care</span>
                                <span x-text="biayaHomecare ? formatRupiah(biayaHomecare) : 'Rp 0'"></span>
                            </div>

                            {{-- Diskon --}}
                            <div x-show="discountAmount > 0"
                                class="flex justify-between items-center text-sm font-medium text-rose-300">
                                <span>Diskon Layanan</span>
                                <span x-text="'-' + formatRupiah(discountAmount)"></span>
                            </div>

                            {{-- Admin --}}
                            <div class="flex justify-between items-center text-sm font-medium opacity-90">
                                <span>Biaya Admin</span>
                                <span>Rp 5.000</span>
                            </div>

                            {{-- Grand Total --}}
                            <div class="pt-4 border-t border-white/20 flex justify-between items-end">
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

        {{-- NEW PATIENT BOTTOM SHEET MODAL --}}
        <div x-show="showNewPatientModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 flex items-end justify-center"
            style="display: none; z-index: 9999;">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeNewPatientModal()"></div>

            {{-- Sheet --}}
            <div x-show="showNewPatientModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                class="relative w-full bg-white rounded-t-3xl shadow-2xl flex flex-col"
                style="z-index: 10000; max-height: 85dvh;" @click.stop>

                {{-- Fixed top: handle + header --}}
                <div class="px-5 pt-4 pb-3 shrink-0">
                    <div class="w-8 h-1 bg-slate-200 rounded-full mx-auto mb-4"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-[#0D4C4A]">Tambah Pasien Baru</h3>
                            <p class="text-[10px] text-slate-400 font-medium mt-0.5">Data disimpan saat booking
                                dikonfirmasi</p>
                        </div>
                        <button type="button" @click="closeNewPatientModal()"
                            class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-400 transition-colors shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="2.5">
                                <path d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Scrollable fields --}}
                <div class="px-5 overflow-y-auto flex-1 space-y-3 pb-3">

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Nama Lengkap <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="newPatientForm.name" placeholder="Masukkan nama lengkap"
                            class="w-full px-3.5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-teal-100 focus:border-teal-300 transition-all">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            No. HP <span class="text-rose-400">*</span>
                        </label>
                        <input type="tel" x-model="newPatientForm.phone" placeholder="08xxxxxxxxxx"
                            class="w-full px-3.5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-teal-100 focus:border-teal-300 transition-all">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Tanggal Lahir <span class="text-rose-400">*</span>
                        </label>
                        <input type="date" x-model="newPatientForm.dob"
                            class="w-full px-3.5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-teal-100 focus:border-teal-300 transition-all">
                        <p class="text-[10px] text-slate-400 font-medium px-0.5">Digunakan untuk verifikasi akun pasien</p>
                    </div>

                </div>

                {{-- Fixed bottom: CTA — sits above safe area --}}
                <div class="px-5 pt-3 pb-safe shrink-0 border-t border-slate-100"
                    style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom));">
                    <button type="button" @click="saveNewPatient()"
                        class="w-full py-3.5 bg-[#2D7A78] text-white rounded-2xl text-sm font-bold shadow-lg active:scale-95 transition-all">
                        Simpan & Gunakan
                    </button>
                </div>

            </div>
        </div>

        <x-navigation.patient-navbar active="booking" />


    </x-layouts.mobile-app>

@endsection
