@extends('components.layouts.app')

@section('title', 'List Janji Temu')

@section('content')

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen">

        {{-- 1. TOPBAR --}}
        <x-ui.topbar title="Rumah Terapi Anjali">
            <x-slot:right>
                <div class="w-10 h-10 rounded-xl border-2 border-orange-200 p-0.5 bg-white shadow-sm overflow-hidden">
                    <img src="https://i.pravatar.cc/100?u=admin" class="w-full h-full rounded-lg object-cover">
                </div>
            </x-slot:right>
        </x-ui.topbar>

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
                <a href="{{ route('therapist.booking.history') }}"
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
        if (this.activeTab === "history") return this.items.filter(i => i.booking_status === "completed");
        if (this.activeTab === "cancelled") return this.items.filter(i => i.booking_status === "cancelled" || i.booking_status === "rejected");
        return this.items.filter(i => i.booking_status === this.activeTab);
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
                <div class="px-1">
                    <div class="flex p-1.5 bg-slate-200/50 backdrop-blur-sm rounded-xl border border-slate-200/50">
                        <button @click="activeTab = `pending`; resetLimit()"
                            :class="activeTab === `pending` ? `bg-white text-orange-600 shadow-sm` : `text-slate-500`"
                            class="flex-1 py-3 text-[12px] font-semibold uppercase tracking-widest rounded-xl transition-all flex items-center justify-center gap-2">
                            Menunggu
                            <span class="px-1.5 py-0.5 bg-orange-100 text-orange-600 rounded-md text-[9px]"
                                x-text="items.filter(i => i.booking_status === `pending`).length"></span>
                        </button>
                        <button @click="activeTab = `approved`; resetLimit()"
                            :class="activeTab === `approved` ? `bg-white text-emerald-600 shadow-sm` : `text-slate-500`"
                            class="flex-1 py-3 text-[12px] font-semibold uppercase tracking-widest rounded-xl transition-all">
                            Disetujui
                        </button>
                        <button @click="activeTab = `cancelled`; resetLimit()"
                            :class="activeTab === `cancelled` ? `bg-white text-rose-600 shadow-sm` : `text-slate-500`"
                            class="flex-1 py-3 text-[12px] font-semibold uppercase tracking-widest rounded-xl transition-all">
                            Batal
                        </button>
                    </div>
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
                                                :action="'/admin-cabang/booking/' + item.id_raw + '/reject'" method="POST"
                                                class="flex-1">
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
                                        <button
                                            class="flex-1 py-3 bg-slate-50 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-slate-200 active:scale-95 transition-all">
                                            Lihat Detail
                                        </button>
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
