@extends('components.layouts.app')

@section('title', 'Pengaturan Jadwal')

@section('content')

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{
        days: [
            { name: 'Senin', active: true, clinic_hours: '08:00 - 20:00', slots: [{ start: '08:30', end: '11:30' }, { start: '13:30', end: '15:30' }] },
            { name: 'Selasa', active: false, clinic_hours: '08:00 - 20:00', slots: [{ start: '08:30', end: '11:30' }] },
            { name: 'Rabu', active: true, clinic_hours: '08:00 - 20:00', slots: [{ start: '08:30', end: '11:30' }] },
            { name: 'Kamis', active: true, clinic_hours: '08:00 - 20:00', slots: [{ start: '08:30', end: '11:30' }] },
            { name: 'Jumat', active: true, clinic_hours: '08:00 - 20:00', slots: [{ start: '08:30', end: '11:30' }] },
            { name: 'Sabtu', active: true, clinic_hours: '08:00 - 17:00', slots: [{ start: '08:30', end: '11:30' }] },
            { name: 'Minggu', active: false, clinic_hours: 'Tutup', slots: [{ start: '08:30', end: '11:30' }] }
        ],
        addSlot(index) {
            if (this.days[index].slots.length < 3) {
                this.days[index].slots.push({ start: '00:00', end: '00:00' });
            }
        },
        removeSlot(dayIndex, slotIndex) {
            this.days[dayIndex].slots.splice(slotIndex, 1);
        }
    }">

        {{-- 1. TOPBAR --}}
        <x-ui.topbar title="Rumah Terapi Anjali">
            <x-slot:left>
                <a href="{{ route('therapist.jadwal') }}" class="p-1 -ml-1 text-slate-400 hover:text-teal-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            </x-slot:left>

            <x-slot:right>
                <div class="w-10 h-10 rounded-xl border-2 border-orange-100 p-0.5 bg-white">
                    <img src="https://i.pravatar.cc/100?u=therapist" class="w-full h-full rounded-lg object-cover">
                </div>
            </x-slot:right>
        </x-ui.topbar>

        <div class="px-6 pt-8 pb-32 space-y-10">

            {{-- 2. HEADER TITLE --}}
            <div class="space-y-2">
                <h2 class="text-3xl font-semibold text-teal-900 tracking-tight">Pengaturan Jam Kerja</h2>
                <p class="text-base text-slate-500 font-medium leading-relaxed">Atur jam kerja Anda dan sesi terapi yang
                    tersedia.</p>
            </div>

            {{-- 3. DAYS CONFIGURATION LIST --}}
            <div class="space-y-6">
                <template x-for="(day, index) in days" :key="index">
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm transition-all duration-300"
                        :class="!day.active && 'opacity-60 grayscale-[0.5]'">

                        {{-- Card Header: Day Name & Toggle --}}
                        <div class="px-7 py-6 flex justify-between items-center border-b border-slate-50">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full"
                                    :class="day.active ? 'bg-teal-500 shadow-[0_0_8px_rgba(20,184,166,0.5)]' : 'bg-slate-300'">
                                </div>
                                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-[0.2em]" x-text="day.name">
                                </h3>
                            </div>

                            {{-- Custom Toggle Switch --}}
                            <div class="flex items-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="day.active" class="sr-only peer">
                                    <div
                                        class="w-12 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-[20px] after:w-[20px] after:transition-all peer-checked:bg-teal-600">
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Card Body: Slots --}}
                        <div class="p-6 space-y-6" x-show="day.active" x-collapse>
                            {{-- Clinic Operational Hours Info --}}
                            <div
                                class="bg-teal-50/50 px-5 py-3.5 rounded-2xl border border-teal-100/50 flex flex-col gap-1.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-1.5 h-1.5 rounded-full bg-teal-500"></div>
                                    <span class="text-[10px] font-bold text-teal-800 uppercase tracking-widest">Jam Operasional Klinik</span>
                                </div>
                                <span class="text-sm font-bold text-teal-700 ml-4" x-text="day.clinic_hours"></span>
                            </div>
                            <template x-for="(slot, sIdx) in day.slots" :key="sIdx">
                                <div
                                    class="relative px-5 py-4 bg-slate-50 rounded-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-300">

                                    {{-- Tombol Hapus (Dipindah ke Pojok Kanan Atas agar tidak merusak baris) --}}
                                    <button type="button" @click="removeSlot(index, sIdx)" x-show="day.slots.length > 1"
                                        class="absolute -top-2 -right-2 w-8 h-8 bg-white border border-rose-100 text-rose-500 rounded-full flex items-center justify-center shadow-md active:scale-90 z-20">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="3">
                                            <path d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <div class="flex items-center justify-start gap-4">
                                        {{-- Input Jam Mulai --}}
                                        <div class="space-y-1.5 w-[105px]">
                                            <label
                                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mulai</label>
                                            <input type="time" x-model="slot.start"
                                                class="w-full bg-white border border-slate-200 rounded-xl py-3 px-3 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">
                                        </div>

                                        <div class="pt-5 text-slate-300 font-bold text-lg"></div>

                                        {{-- Input Jam Selesai --}}
                                        <div class="space-y-1.5 w-[105px]">
                                            <label
                                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Selesai</label>
                                            <input type="time" x-model="slot.end"
                                                class="w-full bg-white border border-slate-200 rounded-xl py-3 px-3 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Tombol Tambah Slot --}}
                            <button type="button" @click="addSlot(index)" x-show="day.slots.length < 3"
                                class="w-full py-3 border-2 border-dashed border-slate-200 rounded-2xl flex items-center justify-center gap-2 text-xs font-bold text-slate-400 hover:border-teal-400 hover:text-teal-600 transition-all active:scale-[0.98]">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="3">
                                    <path d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Jadwal Praktik
                            </button>
                        </div>

                        {{-- Day Off Message --}}
                        <div class="p-10 text-center" x-show="!day.active">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest italic">Terapis Tidak
                                Praktik</p>
                        </div>
                    </div>
                </template>
            </div>

            <div class="space-y-4 pt-4">
                <button
                    class="w-full py-5 bg-teal-800 text-white rounded-2xl text-base font-semibold uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                    Simpan Perubahan
                </button>
                <button type="button"
                    class="w-full py-4 text-sm font-semibold text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">
                    Batalkan
                </button>
            </div>

        </div>

        {{-- BOTTOM NAVBAR --}}
        <x-navigation.therapist-navbar active="jadwal" />

    </x-layouts.mobile-app>

    <style>
        /* Premium Scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Input Time customization for better touch */
        input[type="time"] {
            position: relative;
            z-index: 10;
            width: 100%;
            -webkit-tap-highlight-color: transparent;
        }

        /* Ensure the native picker icon is accessible */
        input[type="time"]::-webkit-calendar-picker-indicator {
            background: transparent;
            cursor: pointer;
            padding: 2px;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Animasi masuk untuk baris baru */
        .animate-in {
            animation-fill-mode: forwards;
        }
    </style>

@endsection
