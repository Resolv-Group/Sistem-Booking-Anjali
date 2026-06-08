@extends('components.layouts.app')
@section('title', 'Jadwal Sesi')

<script>
    window.mappedBookings = @js($mappedBookings);
</script>
@section('content')
    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32" x-data="{
        activeTab: 'mendatang',
        historyFilter: 'semua',
        items: mappedBookings,
        rescheduleOpen: false,
        selectedBooking: null,
        availableSessions: [],
        loadingSessions: false,
        selectedSessionId: null,
        rescheduleStep: 1,
        toastMessage: '',
        showToast: false,
    
    
        get upcomingItems() {
                // Pastikan i.status_key sesuai dengan yang dikirim controller
                return this.items.filter(i => ['pending', 'approved', 'sedang_berjalan'].includes(i.status_key));
            },
    
        // Filter untuk Tab Riwayat (selesai, ditolak, dibatalkan)
        get historyItems() {
            return this.items.filter(i => {
                const isHistory = ['completed', 'rejected', 'cancelled'].includes(i.status_key);
                if (!isHistory) return false;
    
                if (this.historyFilter === 'semua') return true;
                if (this.historyFilter === 'selesai') return i.status_key === 'completed';
                if (this.historyFilter === 'ditolak') return ['rejected', 'cancelled'].includes(i.status_key);
                return true;
            });
        },
        openRescheduleModal(item) {
            this.selectedBooking = item;
            this.rescheduleOpen = true;
            this.rescheduleStep = 1;
            this.selectedSessionId = null;
            this.availableSessions = [];
            this.loadingSessions = true;
    
            fetch(`/booking/patient/my-booking/${item.booking_id}/available-sessions`)
                .then(res => res.json())
                .then(data => {
                    this.availableSessions = data;
                    this.loadingSessions = false;
                })
                .catch(err => {
                    console.error(err);
                    this.loadingSessions = false;
                });
        },
    
        get oldFormattedTime() {
            if (!this.selectedBooking) return '';
            try {
                let parts = this.selectedBooking.waktu.split(' • ');
                let datePart = parts[0].substring(parts[0].indexOf(', ') + 2).replace(' 2026', '');
                let timePart = parts[1].split(', ')[1];
                return datePart + ' ' + timePart;
            } catch (e) {
                return this.selectedBooking.waktu;
            }
        },
    
        get newFormattedTime() {
            if (!this.selectedSessionId) return '';
            let sess = this.availableSessions.find(s => s.id === this.selectedSessionId);
            if (!sess) return '';
            return sess.tanggal_formatted + ' ' + sess.waktu_mulai;
        },
    
        confirmReschedule() {
            if (!this.selectedSessionId) return;
    
            // Cari data sesi baru untuk ditampilkan di alert
            let newSess = this.availableSessions.find(s => s.id === this.selectedSessionId);
    
            Swal.fire({
                title: 'Konfirmasi Jadwal',
                html: `<div>Apakah Anda yakin ingin memindahkan jadwal ke <br> <b>${newSess.tanggal_formatted}</b> pukul <b>${newSess.waktu_mulai} WIB</b>?</div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah Jadwal',
                cancelButtonText: 'Kembali',
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#f1f5f9',
                customClass: {
                    popup: 'rounded-[2rem] p-8',
                    confirmButton: 'rounded-xl px-6 py-3 text-xs font-black uppercase tracking-widest',
                    cancelButton: 'rounded-xl px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-500'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/booking/patient/my-booking/${this.selectedBooking.booking_id}/reschedule`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ new_sesi_id: this.selectedSessionId })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.triggerToast('✓ Jadwal berhasil diperbarui');
                                this.rescheduleOpen = false;
                                setTimeout(() => { window.location.reload(); }, 1500);
                            } else {
                                Swal.fire('Gagal', data.error || 'Terjadi kesalahan.', 'error');
                            }
                        });
                }
            });
        },
    
        confirmCancel(item) {
            Swal.fire({
                title: 'Batalkan Janji?',
                text: 'Tindakan ini akan membatalkan sesi Anda secara permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Kembali',
                confirmButtonColor: '#be123c',
                cancelButtonColor: '#f1f5f9',
                customClass: {
                    popup: 'rounded-[2rem] p-8',
                    confirmButton: 'rounded-xl px-6 py-3 text-xs font-black uppercase tracking-widest',
                    cancelButton: 'rounded-xl px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-500'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/booking/patient/my-booking/${item.booking_id}/cancel`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.triggerToast('✓ Booking berhasil dibatalkan');
                                setTimeout(() => { window.location.reload(); }, 1500);
                            } else {
                                Swal.fire('Gagal', data.error || 'Terjadi kesalahan.', 'error');
                            }
                        });
                }
            });
        },
    
        triggerToast(msg) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-teal-100 bg-white/90 backdrop-blur-md mt-4',
                    title: 'text-xs font-black text-teal-800 uppercase tracking-widest'
                }
            });
            Toast.fire({
                icon: 'success',
                title: msg
            });
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
                            Agenda Sesi
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

        <div class="px-6 pt-8 space-y-8">
            {{-- 2. HERO TITLE --}}
            <div class="space-y-2 px-1">
                <h2 class="text-3xl font-bold text-teal-900 tracking-tight leading-tight">Booking Saya</h2>
                <p class="text-sm font-medium text-slate-500 leading-relaxed">
                    Lihat detail waktu dan lokasi untuk sesi konsultasi Anda berikutnya di sini.
                </p>
            </div>
            {{-- 3. MAIN TAB SWITCHER --}}
            <div class="p-1.5 bg-slate-100 rounded-2xl flex items-center shadow-inner">
                <button @click="activeTab = 'mendatang'"
                    :class="activeTab === 'mendatang' ? 'bg-white text-teal-800 shadow-sm' : 'text-slate-500'"
                    class="flex-1 py-3 text-xs font-bold rounded-xl transition-all duration-300">
                    Mendatang
                </button>
                <button @click="activeTab = 'riwayat'"
                    :class="activeTab === 'riwayat' ? 'bg-white text-teal-800 shadow-sm' : 'text-slate-500'"
                    class="flex-1 py-3 text-xs font-bold rounded-xl transition-all duration-300">
                    Selesai & Riwayat
                </button>
            </div>
            {{-- 4. SUB-FILTERS (Riwayat) --}}
            <div x-show="activeTab === 'riwayat'" x-cloak x-transition
                class="flex text-center justify-center gap-2 overflow-x-auto no-scrollbar pb-1">
                <button @click="historyFilter = 'semua'"
                    :class="historyFilter === 'semua' ? 'bg-teal-800 text-white border-transparent' :
                        'bg-white text-slate-500 border-slate-100'"
                    class="shrink-0 px-6 py-2.5 rounded-full border text-[12px] font-semibold uppercase tracking-widest transition-all">
                    Semua
                </button>
                <button @click="historyFilter = 'selesai'"
                    :class="historyFilter === 'selesai' ? 'bg-teal-800 text-white border-transparent' :
                        'bg-white text-slate-500 border-slate-100'"
                    class="shrink-0 px-6 py-2.5 rounded-full border text-[12px] font-semibold uppercase tracking-widest transition-all">
                    Selesai
                </button>
                <button @click="historyFilter = 'ditolak'"
                    :class="historyFilter === 'ditolak' ? 'bg-teal-800 text-white border-transparent' :
                        'bg-white text-slate-500 border-slate-100'"
                    class="shrink-0 px-6 py-2.5 rounded-full border text-[12px] font-semibold uppercase tracking-widest transition-all">
                    Dibatalkan
                </button>
            </div>
            {{-- 5. LIST CONTENT --}}
            <div class="space-y-6">
                {{-- --- TAB MENDATANG --- --}}
                <div x-show="activeTab === 'mendatang'" class="space-y-6">
                    <template x-for="item in upcomingItems" :key="item.id_raw">
                        <div
                            class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm space-y-6 relative overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-500">
                            <div class="flex justify-between items-start">
                                <span
                                    class="px-2.5 py-1 bg-slate-50 text-slate-400 text-[11px] font-bold rounded-lg border border-slate-100 uppercase tracking-tighter"
                                    x-text="item.id"></span>
                                {{-- Status Badge: green for approved, yellow for pending --}}
                                <span
                                    :class="{
                                        'bg-emerald-50 text-emerald-600 border-emerald-100': item
                                            .status_key === 'approved',
                                        'bg-amber-50 text-amber-600 border-amber-100': item.status_key === 'pending',
                                        'bg-blue-50 text-blue-600 border-blue-100': item
                                            .status_key === 'sedang_berjalan'
                                    }"
                                    class="px-3 py-1 text-[11px] font-black uppercase tracking-widest rounded-full border flex items-center gap-1.5">
                                    <div class="w-1 h-1 rounded-full animate-pulse"
                                        :class="{
                                            'bg-emerald-500': item.status_key === 'approved',
                                            'bg-amber-500': item.status_key === 'pending',
                                            'bg-blue-500': item.status_key === 'sedang_berjalan'
                                        }">
                                    </div>
                                    <span x-text="item.status_text"></span>
                                </span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-50 shadow-sm">
                                    <img :src="item.terapis_foto"
                                        class="w-full h-full object-cover transition-all duration-500"
                                        :class="activeTab === 'riwayat' && item.status_key !== 'completed' ? 'grayscale' : ''"
                                        :alt="item.terapis">
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-slate-800" x-text="item.terapis"></h4>
                                    <p class="text-xs font-semibold text-teal-600 uppercase tracking-widest"
                                        x-text="item.layanan"></p>
                                </div>
                            </div>
                            <div class="bg-slate-50 rounded-2xl p-4 space-y-3 border border-slate-100/50">
                                <div class="flex items-center gap-3 text-slate-600">
                                    <i data-lucide="calendar" class="w-4 h-4 text-teal-600"></i>
                                    {{-- Mengambil "Kamis, 04 Juni 2026" --}}
                                    <p class="text-[13px] font-semibold uppercase tracking-tighter"
                                        x-text="item.waktu.split(' • ')[0]"></p>
                                </div>
                                <div class="flex items-center gap-3 text-slate-600">
                                    <i data-lucide="clock" class="w-4 h-4 text-teal-600"></i>
                                    {{-- Mengambil "Siang, 14:00" --}}
                                    <p class="text-[13px] font-semibold uppercase tracking-tighter"
                                        x-text="item.waktu.split(' • ')[1]"></p>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <template x-if="item.status_key === 'approved'">
                                    <div class="grid grid-cols-2 gap-3">
                                        <button @click="openRescheduleModal(item)"
                                            class="py-4 bg-teal-50 text-teal-700 rounded-2xl text-[13px] font-black uppercase tracking-[0.2em] active:scale-95 transition-all border border-teal-100 font-bold">
                                            Ubah Jadwal
                                        </button>
                                        <button @click="confirmCancel(item)"
                                            class="py-4 bg-rose-50 text-rose-700 rounded-2xl text-[13px] font-black uppercase tracking-[0.2em] active:scale-95 transition-all border border-rose-100 font-bold">
                                            Batalkan
                                        </button>
                                    </div>
                                </template>
                                <template x-if="item.status_key === 'pending'">
                                    <button @click="confirmCancel(item)"
                                        class="w-full py-4 bg-rose-50 text-rose-700 rounded-2xl text-[13px] font-black uppercase tracking-[0.2em] active:scale-95 transition-all border border-rose-100 font-bold">
                                        Batalkan
                                    </button>
                                </template>
                            </div>
                    </template>

                    {{-- Pesan Jika Kosong --}}
                    <div x-show="upcomingItems.length === 0"
                        class="text-center py-20 px-10 space-y-4 animate-in fade-in zoom-in duration-500">
                        <div
                            class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto border border-slate-100 shadow-inner">
                            <i data-lucide="calendar-days" class="w-10 h-10 text-slate-200"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Jadwal Kosong</h3>
                            <p class="text-xs font-medium text-slate-400 leading-relaxed">
                                Anda belum memiliki janji temu yang aktif atau menunggu verifikasi.
                            </p>
                        </div>
                        <div class="pt-4">
                            <a href="{{ route('patient.therapist') }}"
                                class="inline-block px-8 py-3 bg-teal-50 text-teal-700 rounded-2xl text-[11px] font-black uppercase tracking-widest border border-teal-100 active:scale-95 transition-all">
                                Cari Terapis
                            </a>
                        </div>
                    </div>
                </div>
                {{-- --- TAB RIWAYAT --- --}}
                <div x-show="activeTab === 'riwayat'" class="space-y-6">
                    <template x-for="item in historyItems" :key="item.id_raw">
                        <div
                            class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                            <div class="flex justify-between items-center">
                                <span class="text-[13px] font-bold text-slate-300" x-text="item.id"></span>
                                <span
                                    :class="item.status_key === 'completed' ? 'bg-slate-100 text-slate-500 border-slate-200' :
                                        'bg-rose-50 text-rose-500 border-rose-100'"
                                    class="px-3 py-1 text-[11px] font-black uppercase tracking-widest rounded-full border">
                                    <span
                                        x-text="item.status_key === 'completed' ? '✓ Selesai' : '✗ ' + item.status_text"></span>
                                </span>
                            </div>
                            <div class="flex items-center gap-4"
                                :class="item.status_key !== 'completed' ? 'opacity-60' : ''">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 overflow-hidden shrink-0 shadow-sm">
                                    <img :src="item.terapis_foto" class="w-full h-full object-cover"
                                        :class="item.status_key !== 'completed' ? 'grayscale' : ''">
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-slate-800" x-text="item.terapis"></h4>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest"
                                        x-text="item.layanan"></p>
                                </div>
                            </div>
                            {{-- Info Box (Tampilkan Detail Penolakan jika ditolak/dibatalkan) --}}
                            <template x-if="item.status_key === 'rejected' || item.status_key === 'cancelled'">
                                <div
                                    class="bg-orange-50/50 rounded-2xl p-4 border border-orange-100 flex items-start gap-3">
                                    <i data-lucide="alert-circle" class="w-4 h-4 text-orange-500 shrink-0 mt-0.5"></i>
                                    <div>
                                        <p class="text-[11px] font-black text-orange-600 uppercase tracking-widest">Detail
                                            Penolakan</p>
                                        <p class="text-[11px] font-bold text-orange-800 mt-1 leading-relaxed"
                                            x-text="item.alasan_status || 'Tidak ada alasan spesifik.'"></p>
                                    </div>
                                </div>
                            </template>
                            <template x-if="item.status_key === 'completed'">
                                <div class="bg-slate-50 rounded-2xl p-4 space-y-2 border border-slate-100">
                                    <div class="flex items-center gap-3 text-slate-500">
                                        <i data-lucide="calendar" class="w-4 h-4"></i>
                                        <p class="text-[13px] font-bold uppercase tracking-tighter" x-text="item.waktu">
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Pesan Jika Kosong --}}
                    <div x-show="historyItems.length === 0"
                        class="text-center py-20 px-10 space-y-4 animate-in fade-in zoom-in duration-500">
                        <div
                            class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto border border-slate-100 shadow-inner">
                            <i data-lucide="archive-x" class="w-10 h-10 text-slate-200"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Riwayat Kosong</h3>
                            <p class="text-xs font-medium text-slate-400 leading-relaxed">
                                Tidak ada data riwayat janji temu untuk kategori <span class="text-teal-600 font-bold"
                                    x-text="historyFilter"></span>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Reschedule Modal (Reworked to Center Modal) -->
        <div x-show="rescheduleOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-6 pb-36" x-cloak>

            <!-- Overlay Glassy -->
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" @click="rescheduleOpen = false"></div>

            <!-- Modal Box -->
            <div class="relative bg-white rounded-[2.5rem] w-full max-w-sm shadow-2xl overflow-hidden flex flex-col max-h-full"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                <!-- Header -->
                <div class="px-8 pt-8 pb-4 flex justify-between items-center bg-white">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-[0.2em]">Ubah Jadwal</h3>
                    <button @click="rescheduleOpen = false"
                        class="w-8 h-8 flex items-center justify-center bg-slate-50 rounded-full text-slate-400 hover:text-rose-500 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="px-8 pb-8 space-y-6 overflow-y-auto max-h-[70vh] custom-scrollbar">

                    <!-- SECTION 1: Jadwal Saat Ini -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <div class="w-1 h-3 bg-rose-500 rounded-full"></div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Jadwal Saat Ini</p>
                        </div>
                        <div class="bg-rose-50/50 border border-rose-100 rounded-2xl p-4 flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-rose-500 shadow-sm border border-rose-50">
                                <i data-lucide="calendar-off" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-xs font-black text-rose-700 uppercase tracking-tight"
                                    x-text="oldFormattedTime.split(' ')[0] + ' ' + oldFormattedTime.split(' ')[1]"></p>
                                <p class="text-[10px] font-bold text-rose-500/70 uppercase"
                                    x-text="oldFormattedTime.split(' ')[2] || ''"></p>
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-slate-100 w-full"></div>

                    <!-- SECTION 2: Pilih Jadwal Baru -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <div class="w-1 h-3 bg-teal-500 rounded-full"></div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Jadwal Baru
                            </p>
                        </div>

                        <!-- Loading State -->
                        <div x-show="loadingSessions" class="py-10 text-center">
                            <div
                                class="w-6 h-6 border-2 border-teal-600 border-t-transparent rounded-full animate-spin mx-auto">
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div x-show="!loadingSessions && availableSessions.length === 0"
                            class="py-10 text-center space-y-2">
                            <i data-lucide="info" class="w-8 h-8 text-slate-200 mx-auto"></i>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Jadwal lain belum tersedia</p>
                        </div>

                        <!-- List Sessions -->
                        <div class="space-y-2.5" x-show="!loadingSessions && availableSessions.length > 0">
                            <template x-for="sess in availableSessions" :key="sess.id">
                                <button @click="selectedSessionId = sess.id"
                                    class="w-full group flex items-center justify-between p-4 rounded-2xl border-2 transition-all text-left"
                                    :class="selectedSessionId === sess.id ? 'border-teal-600 bg-teal-50/50' :
                                        'border-slate-50 bg-slate-50/30 hover:border-teal-200'">

                                    <div class="flex items-center gap-3">
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                            :class="selectedSessionId === sess.id ? 'border-teal-600 bg-teal-600' :
                                                'border-slate-200 bg-white'">
                                            <div class="w-1.5 h-1.5 rounded-full bg-white"
                                                x-show="selectedSessionId === sess.id"></div>
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-black uppercase tracking-tight"
                                                :class="selectedSessionId === sess.id ? 'text-teal-900' : 'text-slate-700'"
                                                x-text="sess.tanggal_formatted"></p>
                                            <p class="text-[10px] font-bold"
                                                :class="selectedSessionId === sess.id ? 'text-teal-600' : 'text-slate-400'"
                                                x-text="sess.waktu_mulai + ' WIB'"></p>
                                        </div>
                                    </div>

                                    <span class="text-[9px] font-black px-2 py-1 rounded-lg uppercase tracking-tighter"
                                        :class="selectedSessionId === sess.id ? 'bg-teal-600 text-white' :
                                            'bg-white text-slate-400'"
                                        x-text="sess.remaining_capacity + ' Slot'"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Footer Action -->
                <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 shrink-0">
                    <button :disabled="!selectedSessionId" @click="confirmReschedule()" {{-- Memanggil SweetAlert konfirmasi --}}
                        class="w-full py-4 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-3"
                        :class="selectedSessionId ? 'bg-teal-800 text-white shadow-xl shadow-teal-900/20 active:scale-95' :
                            'bg-slate-200 text-slate-400 cursor-not-allowed'">
                        <span>Konfirmasi</span>
                        <i data-lucide="arrow-right" class="w-4 h-4" x-show="selectedSessionId"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Success Toast -->
        <div x-show="showToast"
            class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50 bg-teal-900 text-white px-6 py-4 rounded-2xl shadow-xl flex items-center gap-3 border border-teal-800"
            x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4">
            <span class="text-xs font-black uppercase tracking-widest" x-text="toastMessage"></span>
        </div>
        <x-navigation.patient-navbar active="mybooking" />
    </x-layouts.mobile-app>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Memastikan icon lucide di-render ulang setiap kali Alpine melakukan manipulasi DOM
        document.addEventListener('alpine:init', () => {
            Alpine.effect(() => {
                lucide.createIcons();
            });
        });
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

        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection
