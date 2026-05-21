@extends('components.layouts.app')

@section('title', 'Pengaturan Jadwal')

@section('content')

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{
        days: {{ json_encode($daysData) }},
        addSlot(index) {
            if (this.days[index].slots.length < 10) {
                this.days[index].slots.push({ id: 'temp_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9), start: '08:00', kuota: 10 });
                this.sortSlots(index);
            }
        },
        removeSlot(dayIndex, slotIndex) {
            this.days[dayIndex].slots.splice(slotIndex, 1);
        },
        sortSlots(dayIndex) {
            // Sort slots by start time in ascending order
            this.days[dayIndex].slots.sort((a, b) => {
                if (!a.start) return 1;
                if (!b.start) return -1;
                return a.start.localeCompare(b.start);
            });
        },
        hasDuplicateTime(dayIndex, slotIndex) {
            const day = this.days[dayIndex];
            const currentTime = day.slots[slotIndex]?.start;
            if (!currentTime) return false;
            return day.slots.some((s, i) => i !== slotIndex && s.start === currentTime);
        },
        validateForm() {
            let valid = true;
            this.days.forEach((day, dIdx) => {
                if (!day.active) return;
                const times = day.slots.map(s => s.start).filter(Boolean);
                const uniqueTimes = new Set(times);
                if (times.length !== uniqueTimes.size) {
                    valid = false;
                }
            });
            if (!valid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Waktu Duplikat',
                    text: 'Terdapat jam mulai yang sama dalam satu hari. Silakan perbaiki sebelum menyimpan.',
                    confirmButtonColor: '#0d9488'
                });
            }
            return valid;
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
                <p class="text-base text-slate-500 font-medium leading-relaxed">Atur jam mulai praktik dan kuota pasien yang
                    tersedia untuk setiap sesi.</p>
            </div>

            <form action="{{ route('therapist.atur-jam-kerja.store') }}" method="POST"
                @submit.prevent="if(validateForm()) $el.submit()">
                @csrf

                {{-- 3. DAYS CONFIGURATION LIST --}}
                <div class="space-y-6">
                    <template x-for="(day, index) in days" :key="index">
                        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm transition-all duration-300"
                            :class="!day.active && 'opacity-60 grayscale-[0.5]'">

                            <input type="hidden" :name="`days[${index}][${day.name}][active]`"
                                :value="day.active ? '1' : '0'">

                            {{-- Card Header: Day Name & Toggle --}}
                            <div class="px-7 py-6 flex justify-between items-center border-b border-slate-50">
                                <div class="flex items-center gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full"
                                        :class="day.active ? 'bg-teal-500 shadow-[0_0_8px_rgba(20,184,166,0.5)]' :
                                            'bg-slate-300'">
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-[0.2em]"
                                        x-text="day.name"></h3>
                                </div>

                                {{-- Custom Toggle Switch --}}
                                <div class="flex items-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        {{-- Menambahkan logic @change untuk reset data saat day.active bernilai false --}}
                                        <input type="checkbox" x-model="day.active"
                                            @change="if(!day.active) {
                                        day.slots.forEach(slot => {
                                            slot.start = '';
                                            slot.kuota = 0;
                                        })
                                    }"
                                            class="sr-only peer">
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
                                        <span class="text-[10px] font-bold text-teal-800 uppercase tracking-widest">Jam
                                            Operasional Klinik</span>
                                    </div>
                                    <span class="text-sm font-bold text-teal-700 ml-4" x-text="day.clinic_hours"></span>
                                </div>

                                {{-- Sesi/Slot List --}}
                                <div class="space-y-4">
                                    <template x-for="(slot, sIdx) in day.slots" :key="slot.id || sIdx">
                                        <div
                                            class="relative px-6 py-5 bg-slate-50 rounded-3xl border border-slate-100 animate-in fade-in zoom-in-95 duration-300">

                                            {{-- Sesi Title --}}
                                            <div
                                                class="flex items-center justify-between mb-4 border-b border-slate-200/60 pb-2">
                                                <span
                                                    class="text-[10px] font-black text-teal-600 uppercase tracking-[0.2em]"
                                                    x-text="'Sesi ' + (sIdx + 1)"></span>

                                                {{-- Tombol Hapus --}}
                                                <button type="button" @click="removeSlot(index, sIdx)"
                                                    x-show="day.slots.length > 1"
                                                    class="p-1.5 text-slate-300 hover:text-rose-500 transition-colors active:scale-90">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="3">
                                                        <path d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-2 gap-6">
                                                {{-- Input Jam Mulai --}}
                                                <div class="space-y-1.5">
                                                    <label
                                                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jam
                                                        Mulai</label>
                                                    <div class="relative">
                                                        <input type="time" x-model="slot.start" :disabled="!day.active"
                                                            :required="day.active" @change="sortSlots(index)"
                                                            :name="`days[${index}][${day.name}][slots][${sIdx}][start]`"
                                                            :class="hasDuplicateTime(index, sIdx) ?
                                                                'ring-2 ring-rose-400 border-rose-400' : ''"
                                                            class="w-full bg-white border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">
                                                    </div>
                                                    <p x-show="hasDuplicateTime(index, sIdx)"
                                                        class="text-[10px] font-bold text-rose-500 mt-1 ml-1 animate-pulse">
                                                        ⚠ Waktu duplikat!</p>
                                                </div>

                                                {{-- Input Kuota Pasien --}}
                                                <div class="space-y-1.5">
                                                    <label
                                                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kuota
                                                        Pasien</label>
                                                    <div class="relative">
                                                        <input type="number" x-model="slot.kuota" placeholder="0"
                                                            :disabled="!day.active" :required="day.active"
                                                            :min="day.active ? 1 : 0"
                                                            :name="`days[${index}][${day.name}][slots][${sIdx}][kuota]`"
                                                            class="w-full bg-white border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">
                                                        <span
                                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-300 uppercase">Orang</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                {{-- Tombol Tambah Slot --}}
                                <button type="button" @click="addSlot(index)" x-show="day.slots.length < 10"
                                    class="w-full py-4 border-2 border-dashed border-slate-200 rounded-2xl flex items-center justify-center gap-2 text-xs font-bold text-slate-400 hover:border-teal-400 hover:text-teal-600 transition-all active:scale-[0.98]">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="3">
                                        <path d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah Sesi Praktik
                                </button>
                            </div>

                            {{-- Day Off Message --}}
                            <div class="p-10 text-center" x-show="!day.active">
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest italic">Terapis
                                    Tidak Praktik</p>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Footer Buttons --}}
                <div class="space-y-4 pt-8">
                    <button type="button" @click="if(validateForm()) $el.closest('form').submit()"
                        class="w-full py-5 bg-teal-800 text-white rounded-2xl text-base font-semibold uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                        Simpan Perubahan
                    </button>
                    <button type="button" @click="window.history.back()"
                        class="w-full py-4 text-sm font-semibold text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">
                        Batalkan
                    </button>
                </div>
            </form>
            @if (session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top', // Menaruh di tengah atas
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
                            // Menambahkan sedikit styling custom agar lebih cantik
                            customClass: {
                                popup: 'rounded-2xl shadow-xl border border-emerald-100',
                                title: 'text-sm font-black text-slate-800',
                                htmlContainer: 'text-xs font-medium text-slate-500'
                            }
                        });
                    });
                </script>
            @endif
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
