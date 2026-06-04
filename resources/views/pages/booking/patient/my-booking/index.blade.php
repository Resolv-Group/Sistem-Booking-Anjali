@extends('components.layouts.app')

@section('title', 'Jadwal Sesi')

<script>
    window.mappedBookings = @js($mappedBookings);
</script>

@section('content')
    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32" x-data="{
        activeTab: 'mendatang',
        historyFilter: 'semua',
        items: window.mappedBookings,
    
    
        get upcomingItems() {
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
        }
    }">

        {{-- 1. TOPBAR --}}
        <x-ui.topbar title="Anjali">
        </x-ui.topbar>

        <div class="px-6 pt-8 space-y-8">

            {{-- 2. HERO TITLE --}}
            <div class="space-y-2 px-1">
                <h2 class="text-3xl font-bold text-teal-900 tracking-tight leading-tight">Jadwal Sesi</h2>
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
            <div x-show="activeTab === 'riwayat'" x-cloak x-transition class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
                <button @click="historyFilter = 'semua'"
                    :class="historyFilter === 'semua' ? 'bg-teal-800 text-white border-transparent' :
                        'bg-white text-slate-500 border-slate-100'"
                    class="shrink-0 px-6 py-2.5 rounded-full border text-[13px] font-black uppercase tracking-widest transition-all">
                    Semua
                </button>
                <button @click="historyFilter = 'selesai'"
                    :class="historyFilter === 'selesai' ? 'bg-teal-800 text-white border-transparent' :
                        'bg-white text-slate-500 border-slate-100'"
                    class="shrink-0 px-6 py-2.5 rounded-full border text-[13px] font-black uppercase tracking-widest transition-all">
                    Selesai
                </button>
                <button @click="historyFilter = 'ditolak'"
                    :class="historyFilter === 'ditolak' ? 'bg-teal-800 text-white border-transparent' :
                        'bg-white text-slate-500 border-slate-100'"
                    class="shrink-0 px-6 py-2.5 rounded-full border text-[13px] font-black uppercase tracking-widest transition-all">
                    Dibatalkan/Ditolak
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
        'bg-emerald-50 text-emerald-600 border-emerald-100': item.status_key === 'approved',
        'bg-amber-50 text-amber-600 border-amber-100': item.status_key === 'pending',
        'bg-blue-50 text-blue-600 border-blue-100': item.status_key === 'sedang_berjalan'
    }"
    class="px-3 py-1 text-[11px] font-black uppercase tracking-widest rounded-full border flex items-center gap-1.5">
    <div class="w-1 h-1 rounded-full animate-pulse"
        :class="{
            'bg-emerald-500': item.status_key === 'approved',
            'bg-amber-500': item.status_key === 'pending',
            'bg-blue-500': item.status_key === 'sedang_berjalan'
        }"></div>
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
                                <a :href="'/patient/my-booking/' + item.id_raw"
                                    class="block w-full py-4 bg-teal-800 text-white text-center rounded-2xl text-[13px] font-black uppercase tracking-[0.2em] shadow-lg shadow-teal-900/20 active:scale-95 transition-all">
                                    Lihat Detail
                                </a>
                                <button
                                    class="w-full py-4 bg-teal-50 text-teal-700 rounded-2xl text-[13px] font-black uppercase tracking-[0.2em] active:scale-95 transition-all border border-teal-100">
                                    Ubah Jadwal
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- Empty State Mendatang --}}
                    <template x-if="upcomingItems.length === 0">
                        <div class="text-center py-12 opacity-60">
                            <i data-lucide="calendar-x" class="w-12 h-12 mx-auto text-slate-300 mb-3"></i>
                            <p class="text-sm font-bold text-slate-400">Tidak ada jadwal mendatang.</p>
                        </div>
                    </template>
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

                            <div class="space-y-3">
                                <template x-if="item.status_key === 'completed'">
                                    <div class="space-y-3">
                                        <a :href="'/patient/my-booking/' + item.id_raw"
                                            class="block w-full py-4 bg-teal-800 text-white text-center rounded-2xl text-[13px] font-black uppercase tracking-[0.2em] shadow-lg active:scale-95 transition-all">Lihat
                                            Detail</a>
                                        {{-- Perbaikan: Menggunakan terapis_id sesuai Controller --}}
                                        <a :href="'/patient/booking?therapist_id=' + item.terapis_id"
                                            class="block w-full py-4 bg-teal-50 text-teal-700 text-center rounded-2xl text-[13px] font-black uppercase tracking-[0.2em] active:scale-95 transition-all border border-teal-100 font-bold">Buat
                                            Janji Lagi</a>
                                    </div>
                                </template>

                                <template x-if="item.status_key !== 'completed'">
                                    {{-- Perbaikan: Menggunakan terapis_id sesuai Controller --}}
                                    <a :href="'/patient/booking?therapist_id=' + item.terapis_id"
                                        class="block w-full py-4 bg-[#0D4C4A] text-white text-center rounded-2xl text-[13px] font-black uppercase tracking-[0.2em] shadow-lg active:scale-95 transition-all font-bold">
                                        Reschedule
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- 6. MEDICAL GUARANTEE BANNER --}}
                <div class="p-6 bg-teal-50/40 border border-teal-100 rounded-3xl space-y-3">
                    <div class="flex items-center gap-3 text-teal-800">
                        <i data-lucide="shield-check" class="w-5 h-5 shrink-0"></i>
                        <h5 class="text-[11px] font-black uppercase tracking-widest">Jaminan Keamanan Medis</h5>
                    </div>
                    <p class="text-[13px] font-medium text-teal-700 leading-relaxed opacity-80">
                        Setiap sesi di Rumah Terapi Anjali terlindungi oleh standar privasi medis global yang ketat. Kami
                        memastikan seluruh data dan konsultasi Anda tetap menjadi rahasia pribadi yang paling utama.
                    </p>
                </div>

            </div>
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
