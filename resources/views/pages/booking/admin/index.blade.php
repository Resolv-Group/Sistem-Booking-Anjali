@extends('components.layouts.app')

@section('title', 'List Janji Temu')

@section('content')

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen">

        {{-- 1. TOPBAR --}}
<nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-100/80 px-6 py-4">
            <div class="flex items-center justify-between">

                {{-- Left: Navigation & Context --}}
                <div class="flex items-center gap-4">
                    <div class="flex flex-col">
                        {{-- Nama Cabang/Kolaborasi --}}
                        <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">
                            {{ auth()->user()->karyawan->kolaborasi->nama_kolaborasi ?? 'Rumah Terapi Anjali' }}
                        </span>
                        <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none uppercase">
                            Booking
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
                <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm space-y-1">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest leading-tight">
                        Status<br>Pending</p>
                    <h3 class="text-3xl font-semibold text-orange-500">{{ $pendingCount }}</h3>
                </div>
                {{-- Total Card --}}
                <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm space-y-1">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest leading-tight">
                        Total<br>Hari Ini</p>
                    <div class="flex items-baseline gap-1">
                        <h3 class="text-3xl font-semibold text-teal-600">{{ $totalTodayCount }}</h3>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Pasien</span>
                    </div>
                </div>
            </div>

            {{-- 4. SECTION HEADER --}}
            <div class="flex justify-between items-end px-1">
                <h3 class="text-lg font-semibold text-slate-800 tracking-tight">Antrian Saat Ini</h3>
                <a href="{{ route('admin-cabang.booking.history') }}"
                    class="text-xs font-semibold text-teal-600 flex items-center gap-1 hover:underline">
                    Lihat Histori
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path d="M13 7l5 5-5 5M6 7l5 5-5 5" />
                    </svg>
                </a>
            </div>

            {{-- 5. APPOINTMENT LIST --}}
            <div x-data='{
    activeTab: "pending",
    limit: 3,
    loading: false,
    items: @json($mappedBookings),

    get filteredItems() {
        let tab = this.activeTab.toLowerCase();

        return this.items.filter(i => {
            let status = i.booking_status.toLowerCase();

            if (tab === "history") return status === "completed";
            if (tab === "cancelled") return status === "cancelled" || status === "rejected";

            if (tab === "approved") return status === "approved" || status === "disetujui";

            return status === tab;
        });
    },

    get finished() { return this.limit >= this.filteredItems.length; },

    loadMore() {
        this.loading = true;
        setTimeout(() => { this.loading = false; this.limit += 3; }, 500);
    },

    resetLimit() { this.limit = 3; },

    // REJECT LOGIC
    handleReject(id_raw) {
        Swal.fire({
            title: "Tolak Janji Temu",
            text: "Silakan masukkan alasan penolakan:",
            input: "textarea",
            inputPlaceholder: "Tulis alasan di sini...",
            showCancelButton: true,
            confirmButtonText: "Tolak Janji",
            cancelButtonText: "Kembali",
            confirmButtonColor: "#ef4444",
            cancelButtonColor: "#64748b",
            customClass: { popup: "rounded-2xl" },
            inputValidator: (value) => {
                if (!value) return "Alasan harus diisi!";
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Find and submit the specific form by ID
                const form = document.getElementById("form-reject-" + id_raw);
                form.querySelector("input[name=alasan_status]").value = result.value;
                form.submit();
            }
            // If result.isDismissed (Kembali), logic ends here. Page stays as is.
        });
    },

    // CANCEL APPROVAL LOGIC
    handleCancel(id_raw) {
        Swal.fire({
            title: "Batalkan Approval",
            html: `
                <div class="text-left font-sans">
                    <p class="text-xs font-semibold text-slate-500 mb-3">Pilih alasan pembatalan:</p>
                    <div class="space-y-2" id="swal-radio-group">
                        <label class="flex items-center gap-2 p-2 border rounded-xl cursor-pointer">
                            <input type="radio" name="batal_reason" value="Pasien meminta pembatalan" checked>
                            <span class="text-xs">Pasien meminta pembatalan</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 border rounded-xl cursor-pointer">
                            <input type="radio" name="batal_reason" value="Terapis berhalangan hadir">
                            <span class="text-xs">Terapis berhalangan hadir</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 border rounded-xl cursor-pointer">
                            <input type="radio" name="batal_reason" value="others">
                            <span class="text-xs">Lainnya...</span>
                        </label>
                    </div>
                    <textarea id="batal-custom-reason" class="hidden w-full mt-3 p-2 border rounded-xl text-xs" placeholder="Tulis alasan..."></textarea>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: "Batalkan Janji",
            cancelButtonText: "Kembali",
            confirmButtonColor: "#ef4444",
            didOpen: () => {
                const radios = Swal.getHtmlContainer().querySelectorAll("input[name=batal_reason]");
                const txt = Swal.getHtmlContainer().querySelector("#batal-custom-reason");
                radios.forEach(r => r.addEventListener("change", (e) => {
                    txt.classList.toggle("hidden", e.target.value !== "others");
                }));
            },
            preConfirm: () => {
                const selected = Swal.getHtmlContainer().querySelector("input[name=batal_reason]:checked").value;
                if (selected === "others") {
                    const custom = Swal.getHtmlContainer().querySelector("#batal-custom-reason").value;
                    if (!custom) { Swal.showValidationMessage("Tulis alasan Anda!"); return false; }
                    return custom;
                }
                return selected;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById("form-cancel-" + id_raw);
                form.querySelector("input[name=alasan_status]").value = result.value;
                form.submit();
            }
        });
    }
}'
                class="space-y-6">

                {{-- TAB NAVIGATION (Segmented Control) --}}
                <div class="grid grid-cols-3 gap-3">
                    <!-- Card Menunggu -->
                    <button @click="activeTab = 'pending'; resetLimit()"
                        :class="activeTab === 'pending' ?
                            'bg-orange-500 text-white shadow-lg shadow-orange-200 ring-2 ring-orange-200' :
                            'bg-white text-slate-400 border-slate-100'"
                        class="p-4 rounded-2xl border text-left transition-all duration-300 relative overflow-hidden group active:scale-95">
                        <p class="text-[9px] font-black uppercase tracking-wider opacity-80">Pending</p>
                        <h3 class="text-xl font-black mt-1"
                            x-text="items.filter(i => i.booking_status === 'pending').length"></h3>
                        <div class="absolute -right-1 -bottom-1 opacity-20">
                            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z" />
                                <path d="M13 7h-2v6h6v-2h-4z" />
                            </svg>
                        </div>
                    </button>

                    <!-- Card Disetujui -->
                    <button @click="activeTab = 'approved'; resetLimit()"
                        :class="activeTab === 'approved' ?
                            'bg-teal-700 text-white shadow-lg shadow-teal-200 ring-2 ring-teal-100' :
                            'bg-white text-slate-400 border-slate-100'"
                        class="p-4 rounded-2xl border text-left transition-all duration-300 relative overflow-hidden group active:scale-95">
                        <p class="text-[9px] font-black uppercase tracking-wider opacity-80">Disetejui</p>
                        <h3 class="text-xl font-black mt-1"
                            x-text="items.filter(i => i.booking_status === 'approved' || i.booking_status === 'disetujui').length">
                        </h3>
                        <div class="absolute -right-1 -bottom-1 opacity-20">
                            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm-1.99 15.174l-3.462-3.463 1.415-1.414 2.047 2.047 4.547-4.548 1.414 1.414-5.961 5.964z" />
                            </svg>
                        </div>
                    </button>

                    <!-- Card Batal -->
                    <button @click="activeTab = 'cancelled'; resetLimit()"
                        :class="activeTab === 'cancelled' ?
                            'bg-rose-600 text-white shadow-lg shadow-rose-200 ring-2 ring-rose-100' :
                            'bg-white text-slate-400 border-slate-100'"
                        class="p-4 rounded-2xl border text-left transition-all duration-300 relative overflow-hidden group active:scale-95">
                        <p class="text-[9px] font-black uppercase tracking-wider opacity-80">Cancel/Ditolak</p>
                        <h3 class="text-xl font-black mt-1"
                            x-text="items.filter(i => i.booking_status === 'cancelled' || i.booking_status === 'rejected').length">
                        </h3>
                        <div class="absolute -right-1 -bottom-1 opacity-20">
                            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm4.207 12.793l-1.414 1.414L12 13.414l-2.793 2.793-1.414-1.414L10.586 12 7.793 9.207l1.414-1.414L12 10.586l2.793-2.793 1.414 1.414L13.414 12l2.793 2.793z" />
                            </svg>
                        </div>
                    </button>
                </div>

                <div class="space-y-6">
                    <template x-for="(item, index) in filteredItems.slice(0, limit)" :key="item.id_raw">
                        <div class="bg-white rounded-xl border p-7 shadow-sm space-y-6 relative overflow-hidden group transition-all duration-300"
                            :class="{
                                `border-orange-200 ring-4 ring-orange-500/5`: activeTab === `pending`,
                                `border-slate-200`: activeTab !== `pending`
                            }">

                            {{-- Session Type Badge --}}
                            <div class="absolute top-0 left-0">
                                <div :class="item.tipe === `Personal` ? `bg-teal-500` : `bg-orange-500`"
                                    class="text-white text-[9px] font-black uppercase tracking-[0.2em] px-4 py-2 rounded-br-2xl flex items-center gap-1.5 shadow-sm">
                                    <span x-text="item.tipe === `Personal` ? `Personal Session` : `Group Session` "></span>
                                </div>
                            </div>

                            {{-- Status Badge Overlay --}}
                            <div class="absolute top-6 right-6">
                                <template x-if="item.booking_status === 'pending'">
                                    <span
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 text-orange-600 rounded-full border border-orange-100 text-[9px] font-bold uppercase tracking-widest">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                        Perlu Review
                                    </span>
                                </template>
                                <template x-if="item.booking_status === 'approved'">
                                    <span
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-full border border-emerald-100 text-[9px] font-bold uppercase tracking-widest">
                                        ✓ Disetujui
                                    </span>
                                </template>
                                <template x-if="item.booking_status === 'cancelled'">
                                    <span
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 rounded-full border border-rose-100 text-[9px] font-bold uppercase tracking-widest">
                                        ✗ Dibatalkan
                                    </span>
                                </template>
                                <template x-if="item.booking_status === 'rejected'">
                                    <span
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-full border border-red-100 text-[9px] font-bold uppercase tracking-widest">
                                        ✗ Ditolak
                                    </span>
                                </template>
                                <template x-if="item.booking_status === 'completed'">
                                    <span
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 text-teal-600 rounded-full border border-teal-100 text-[9px] font-bold uppercase tracking-widest">
                                        ✓ Selesai
                                    </span>
                                </template>
                            </div>

                            {{-- Patient Info --}}
                            <div class="space-y-1">
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Pasien Utama
                                </p>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-lg font-black tracking-tight"
                                        :class="activeTab === `pending` ? `text-slate-800` : `text-slate-500`"
                                        x-text="item.nama"></h4>
                                    <template x-if="item.tipe === 'Group'">
                                        <button @click.stop="item.showPeserta = !item.showPeserta"
                                            class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-bold rounded-full border border-slate-200 active:scale-90 transition-all">
                                            <span x-text="'+' + item.extra"></span>
                                        </button>
                                    </template>
                                </div>
                                <p class="text-[11px] font-medium text-slate-400 font-mono tracking-tighter"
                                    x-text="'#' + item.id"></p>
                            </div>

                            {{-- Expanded Participants --}}
                            <template x-if="item.tipe === 'Group' && item.showPeserta">
                                <div x-show="item.showPeserta" x-transition
                                    class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-2">
                                    <template x-for="name in item.peserta">
                                        <div
                                            class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-slate-100 shadow-xs">
                                            <div class="w-1 h-1 rounded-full bg-orange-400"></div>
                                            <span class="text-[11px] font-semibold text-slate-600" x-text="name"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Specialist & Time --}}
                            <div class="grid grid-cols-2 gap-4 pt-2">
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Spesialis</p>
                                    <p class="text-xs font-bold text-slate-700" x-text="item.terapis"></p>
                                </div>
                                <div class="space-y-1 text-right">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Jadwal</p>
                                    <p class="text-xs font-bold text-slate-700" x-text="item.waktu"></p>
                                </div>
                            </div>

                            {{-- Buttons Actions --}}
                            <div class="space-y-3 pt-4 border-t border-slate-50">

                                {{-- Aksi khusus tab PENDING --}}
                                <template x-if="activeTab === 'pending'">
                                    <div class="space-y-3">
                                        <template x-if="item.status === 'paid'">
                                            <a :href="item.bukti_transfer_url" target="_blank"
                                                class="w-full py-3.5 bg-slate-900 text-white rounded-xl text-[11px] font-semibold uppercase hover:bg-slate-700 hover:text-white tracking-widest flex items-center justify-center hover:bg-slate-700 gap-2 shadow-lg active:scale-95 transition-all">
                                                Lihat Bukti Pembayaran
                                            </a>
                                        </template>

                                        <div class="flex gap-3">
                                            <form :action="'/admin-cabang/booking/' + item.id_raw + '/accept'"
                                                method="POST" class="flex-1">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full py-3.5 bg-emerald-500 text-white rounded-xl text-[11px] font-semibold uppercase hover:bg-emerald-400 hover:text-emerald-100 tracking-widest active:scale-95 transition-all shadow-md shadow-emerald-500/20">
                                                    Terima Janji
                                                </button>
                                            </form>
                                            <form :id="'form-reject-' + item.id_raw"
                                                :action="'/admin-cabang/booking/' + item.id_raw + '/reject'"
                                                method="POST" class="flex-1">
                                                @csrf
                                                <input type="hidden" name="alasan_status" value="">
                                                <button type="button" @click="handleReject(item.id_raw)"
                                                    class="w-full py-3.5 bg-white border-2 border-rose-100 text-rose-500 rounded-xl text-[11px] font-semibold uppercase hover:bg-rose-50 tracking-widest active:scale-95 transition-all">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </template>

                                {{-- Aksi khusus tab APPROVED --}}
                                <template x-if="activeTab === 'approved'">
                                    <div class="flex gap-3">
                                        <a :href="item.bukti_transfer_url" target="_blank"
                                            class="flex-1 py-3 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center justify-center hover:bg-slate-800 transition-all active:scale-95 text-center">
                                            Bukti Transfer
                                        </a>
                                        <form :id="'form-cancel-' + item.id_raw"
                                            :action="'/admin-cabang/booking/' + item.id_raw + '/cancel-approval'"
                                            method="POST" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="alasan_status" value="">
                                            <input type="hidden" name="batalkan_type" value="admin_request">
                                            <button type="button" @click="handleCancel(item.id_raw)"
                                                class="w-full py-3 bg-white text-rose-400 rounded-xl text-[10px] font-black uppercase tracking-widest border border-rose-100 hover:bg-rose-50 transition-all">
                                                Batal Approval
                                            </button>
                                        </form>
                                    </div>
                                </template>

                                {{-- Visual untuk tab Batal --}}
                                <template x-if="activeTab === 'cancelled'">
                                    <div class="space-y-3.5 pt-1">
                                        <div class="p-4 rounded-xl border flex flex-col gap-2.5"
                                            :class="item.booking_status === 'cancelled' ? 'bg-rose-50/50 border-rose-100' :
                                                'bg-red-50/50 border-red-100'">
                                            <div class="flex items-center gap-2">
                                                <span class="w-1.5 h-1.5 rounded-full"
                                                    :class="item.booking_status === 'cancelled' ? 'bg-rose-500' : 'bg-red-500'"></span>
                                                <p class="text-[10px] font-extrabold uppercase tracking-widest"
                                                    :class="item.booking_status === 'cancelled' ? 'text-rose-600' :
                                                        'text-red-600'"
                                                    x-text="item.booking_status === 'cancelled' ? 'Status: Dibatalkan' : 'Status: Ditolak'">
                                                </p>
                                            </div>
                                            <div class="space-y-1 pl-3.5 border-l-2 border-slate-200">
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                                    Alasan</p>
                                                <p class="text-xs font-semibold text-slate-700 leading-relaxed"
                                                    x-text="item.alasan_status || 'Tidak ada alasan yang dicantumkan'"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- Visual untuk tab lain (History) --}}
                                <template x-if="activeTab === 'history'">
                                    <div
                                        class="w-full py-3 text-center bg-slate-50 text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest border border-slate-100">
                                        Sesi Selesai & Terarsip
                                    </div>
                                </template>

                            </div>
                        </div>
                    </template>

                    {{-- Load More / Empty State --}}
                    <div class="pt-4 pb-12">
                        <button x-show="!finished" @click="loadMore()" :disabled="loading"
                            class="w-full py-4 bg-white border-2 border-dashed border-slate-200 rounded-xl text-xs font-bold text-slate-400 uppercase tracking-[0.2em] flex items-center justify-center gap-3 active:scale-95 transition-all disabled:opacity-50">
                            <template x-if="!loading">
                                <span x-text="`Muat Lebih Banyak (` + (filteredItems.length - limit) + `)`"></span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-teal-600" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4" fill="none"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Memuat...
                                </span>
                            </template>
                        </button>

                        <div x-show="filteredItems.length === 0" class="text-center py-20 space-y-4">
                            <div
                                class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto border border-slate-100">
                                <svg class="w-10 h-10 text-slate-200" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest"
                                x-text="`Tidak ada data ` + activeTab"></p>
                        </div>
                    </div>
                </div>

            </div>

            <div x-data="{ open: false }" class="fixed bottom-24 right-6 z-50 flex flex-col items-end gap-3">
                {{-- Speed Dial Items (Tucked away when closed) --}}
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-90"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-90" class="flex flex-col items-end gap-3 mb-2">

                    {{-- Tambah Cabang --}}
                    <div class="flex items-center gap-3">
                        <span
                            class="bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-100 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Tambah
                            Booking</span>
                        <a href="{{ route('admin-cabang.booking.form') }}"
                            class="w-12 h-12 bg-white text-teal-700 rounded-xl flex items-center justify-center shadow-lg border border-slate-100 active:scale-95 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <button @click="open = !open"
                    :class="open ? 'bg-slate-800 shadow-slate-900/40' : 'bg-teal-900 shadow-teal-900/40'"
                    class="w-14 h-14 text-white rounded-xl flex items-center justify-center shadow-2xl active:scale-90 transition-all duration-300 relative overflow-hidden">
                    <svg x-show="!open" x-transition:enter="transition duration-300"
                        x-transition:enter-start="opacity-0 scale-50 rotate-90"
                        x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-7 h-7" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15h2m-2 4h2" />
                    </svg>
                    <svg x-show="open" x-transition:enter="transition duration-300"
                        x-transition:enter-start="opacity-0 scale-50 -rotate-90"
                        x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-7 h-7" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- 6. BOTTOM NAVBAR --}}
            <x-navigation.admin-cabang-navbar active="booking" />

    </x-layouts.mobile-app>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-emerald-100',
                        title: 'text-sm font-black text-slate-800',
                        htmlContainer: 'text-xs font-medium text-slate-500'
                    }
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: "{{ session('error') }}",
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-rose-100',
                        title: 'text-sm font-black text-slate-800',
                        htmlContainer: 'text-xs font-medium text-slate-500'
                    }
                });
            });
        </script>
    @endif

@endsection
