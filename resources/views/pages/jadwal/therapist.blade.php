@extends('components.layouts.app')

@section('title', 'Agenda Sesi Terapis')

@section('content')

<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen">

    {{-- 1. TOPBAR --}}
    <x-ui.topbar title="Rumah Terapi Anjali">

        <x-slot:right>
            <img
                src="https://i.pravatar.cc/100"
                class="h-10 w-10 rounded-full object-cover"
            >
        </x-slot:right>

    </x-ui.topbar>

    <div class="px-6 pt-8 pb-32 space-y-8">

        {{-- 2. TITLE SECTION --}}
        <div class="space-y-2">
            <h2 class="text-3xl font-semibold text-teal-900 tracking-tight">Agenda Sesi Anda</h2>
            <p class="text-base text-slate-500 font-medium leading-relaxed">Pantau seluruh jadwal konsultasi dan persiapan pasien Anda hari ini dalam satu tampilan.</p>
        </div>

        {{-- 3. SUMMARY PROGRESS CARD --}}
        <div class="bg-slate-100/50 p-6 rounded-3xl border border-white shadow-sm space-y-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-[0.2em] mb-1">Ringkasan Hari Ini</p>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Total Sesi</h3>
                </div>
                <div class="text-right">
                    <span class="text-3xl font-semibold text-teal-800 leading-none">08</span>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-tighter">Janjian</p>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="space-y-2">
                <div class="h-2.5 w-full bg-white rounded-full overflow-hidden shadow-inner border border-slate-200/50">
                    <div class="h-full bg-teal-600 rounded-full w-[70%] shadow-lg shadow-teal-500/20"></div>
                </div>
                <p class="text-[11px] text-slate-400 font-medium italic leading-relaxed">
                    Anda telah mencapai 70% dari kapasitas maksimum yang direkomendasikan.
                </p>
            </div>
        </div>

        {{-- 4. CALENDAR SELECTOR --}}
        <div x-data="{
            selectedDate: new Date().toLocaleDateString('en-CA'),
            days: [],
            init() {
                this.updateDays();
            },
            updateDays() {
                let date = new Date(this.selectedDate);
                let result = [];
                for (let i = -2; i <= 2; i++) {
                    let d = new Date(date);
                    d.setDate(d.getDate() + i);
                    result.push({
                        fullDate: d.toISOString().split('T')[0],
                        dayName: d.toLocaleDateString('id-ID', { weekday: 'short' }).replace('.', ''),
                        dateNum: d.getDate(),
                    });
                }
                this.days = result;
            },
            selectDate(dateStr) {
                this.selectedDate = dateStr;
                this.updateDays();
            },
            getMonthYear() {
                let d = new Date(this.selectedDate);
                return d.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
            }
        }" class="space-y-4">
            <div class="flex justify-between items-end">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 leading-none" x-text="getMonthYear()">Mei 2026</h3>
                    <p class="text-xs font-semibold text-slate-400 mt-1 uppercase tracking-widest">12 Janjian Hari Ini</p>
                </div>
                <div class="relative">
                    <input 
                        type="date" 
                        x-model="selectedDate" 
                        @change="updateDays()"
                        class="absolute inset-0 opacity-0 cursor-pointer z-10"
                    >
                    <button class="p-2.5 bg-white border border-slate-200 rounded-xl text-teal-600 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </button>
                </div>
            </div>

            {{-- Days Row (Fits perfectly within margins) --}}
            <div class="flex gap-2 pb-2">
                <template x-for="d in days" :key="d.fullDate">
                    <button 
                        @click="selectDate(d.fullDate)"
                        class="flex-1 min-w-0 flex flex-col items-center justify-center py-3 rounded-2xl transition-all duration-300"
                        :class="d.fullDate === selectedDate ? 'bg-teal-800 text-white shadow-xl shadow-teal-900/20' : 'bg-white text-slate-400 border border-slate-100 shadow-sm'"
                    >
                        <span 
                            class="text-[10px] font-semibold uppercase tracking-widest" 
                            :class="d.fullDate === selectedDate ? 'opacity-70' : ''"
                            x-text="d.dayName"
                        ></span>
                        <span class="text-base font-semibold mt-1" x-text="d.dateNum"></span>
                        <template x-if="d.fullDate === selectedDate">
                            <div class="w-1 h-1 rounded-full bg-orange-400 mt-1 animate-pulse"></div>
                        </template>
                    </button>
                </template>
            </div>
        </div>

        {{-- 5. WARM & SIMPLE ACCESSIBLE AGENDA --}}
        <div class="space-y-8">
            @php
                $sessions = [
                    [
                        'time_start' => '10:30',
                        'time_end' => '11:30',
                        'status' => 'ongoing',
                        'status_text' => 'Sedang Berlangsung',
                        'patients' => [
                            [
                                'id' => '8829',
                                'name' => 'David Purnama',
                                'type' => 'Akupunktur',
                                'complaint' => 'Nyeri punggung bawah sejak 3 hari lalu, sulit tidur karena sakit.',
                                'has_summary' => true,
                                'is_done' => false,
                            ],
                            [
                                'id' => '8830',
                                'name' => 'Siti Aminah',
                                'type' => 'Akupunktur',
                                'complaint' => 'Migrain berulang di sisi kanan kepala, sensitif cahaya.',
                                'has_summary' => false,
                                'is_done' => false,
                            ],
                        ],
                    ],
                    [
                        'time_start' => '12:00',
                        'time_end' => '12:45',
                        'status' => 'waiting',
                        'status_text' => 'Antrian Berikutnya',
                        'patients' => [
                            [
                                'id' => '9012',
                                'name' => 'Yuliani',
                                'type' => 'Bekam Medis',
                                'complaint' => 'Pegal-pegal di area bahu dan leher setelah bekerja lembur.',
                                'has_summary' => false,
                                'is_done' => false,
                            ],
                        ],
                    ],
                ];
            @endphp

            @foreach($sessions as $session)
                <div x-data="{ open: {{ $session['status'] === 'ongoing' ? 'true' : 'false' }} }" class="relative">
                    <div class="flex gap-4">
                        {{-- Minimalist Timeline Marker --}}
                        <div class="flex flex-col items-center w-12 pt-1 shrink-0">
                            <span class="text-xs font-bold text-slate-700">{{ $session['time_start'] }}</span>
                            <span class="text-[9px] font-semibold text-slate-400 mt-0.5">{{ $session['time_end'] }}</span>
                            <div class="flex-1 w-px bg-slate-200 mt-3 mb-1 rounded-full"></div>
                        </div>

                        {{-- Session Group Content --}}
                        <div class="flex-1">
                            {{-- Header (Warm & Simple) --}}
                            <div @click="open = !open" class="flex justify-between items-start cursor-pointer group pb-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $session['status'] === 'ongoing' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]' : 'bg-slate-300' }}"></span>
                                        <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $session['status_text'] }}</h4>
                                    </div>
                                    {{-- Patient Preview (Shows when collapsed to feel less empty) --}}
                                    <div x-show="!open" class="flex items-center gap-1.5 ml-3.5">
                                        <p class="text-[11px] font-medium text-slate-400 truncate max-w-[180px]">
                                            @foreach($session['patients'] as $p)
                                                {{ explode(' ', $p['name'])[0] }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </p>
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span class="text-[10px] font-bold text-teal-600/70">{{ count($session['patients']) }} Pasien</span>
                                    </div>
                                </div>
                                <div class="p-1.5 bg-slate-100 rounded-lg text-slate-400 group-hover:bg-slate-200 transition-colors">
                                    <svg class="w-4 h-4 transition-transform duration-300" :class="!open ? '-rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>

                            {{-- Patient List --}}
                            <div x-show="open" x-collapse>
                                <div class="space-y-4 pt-1 pb-6">
                                    @foreach($session['patients'] as $patient)
                                        <div class="bg-white/70 backdrop-blur-md rounded-2xl p-5 border border-slate-100 shadow-sm shadow-slate-200/20">
                                            <div class="flex flex-col space-y-4">
                                                
                                                <div class="flex justify-between items-start">
                                                    <div class="space-y-0.5">
                                                        <div class="flex items-center gap-2">
                                                            <h5 class="text-base font-bold text-slate-800">{{ $patient['name'] }}</h5>
                                                            @if($patient['is_done'])
                                                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                            @endif
                                                        </div>
                                                        <p class="text-[10px] font-bold text-teal-600/80 uppercase tracking-wider">{{ $patient['type'] }}</p>
                                                    </div>
                                                    <span class="text-[8px] font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-lg border border-slate-100 uppercase tracking-tighter">ID: {{ $patient['id'] }}</span>
                                                </div>

                                                <div class="bg-slate-50/50 p-4 rounded-xl border border-white/50">
                                                    <p class="text-xs text-slate-600 leading-relaxed font-medium line-clamp-2">
                                                        {{ $patient['complaint'] }}
                                                    </p>
                                                </div>

                                                <div class="flex items-center gap-4">
                                                    <div class="flex items-center gap-1.5">
                                                        <div class="w-5 h-5 rounded-full flex items-center justify-center {{ $patient['has_summary'] ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-300' }}">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        </div>
                                                        <span class="text-[10px] font-bold {{ $patient['has_summary'] ? 'text-emerald-700/70' : 'text-slate-400' }}">Ringkasan</span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5">
                                                        <div class="w-5 h-5 rounded-full flex items-center justify-center {{ $patient['is_done'] ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-300' }}">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
                                                        </div>
                                                        <span class="text-[10px] font-bold {{ $patient['is_done'] ? 'text-emerald-700/70' : 'text-slate-400' }}">Selesai</span>
                                                    </div>
                                                </div>

                                                <div class="flex gap-3 pt-1">
                                                    <a href="{{ route('therapist.ringkasan-sesi', ['id' => $patient['id']]) }}" class="text-center block flex-1 py-3 bg-teal-900 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-teal-900/10 active:scale-95 transition-all">
                                                        Ringkasan
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        </div>

    </div>

    {{-- 6. SPEED DIAL FLOATING ACTION BUTTON --}}
    <div x-data="{ open: false }" class="fixed bottom-24 right-6 z-50 flex flex-col items-end gap-3">
        {{-- Speed Dial Items (Tucked away when closed) --}}
        <div 
            x-show="open" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 scale-90"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-90"
            class="flex flex-col items-end gap-3 mb-2"
        >
            {{-- Edit Jadwal Operasional --}}
            <div class="flex items-center gap-3">
                <span class="bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-100 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Atur Jam Kerja</span>
                <a href="{{ route('therapist.atur-jam-kerja') }}" class="w-12 h-12 bg-white text-teal-700 rounded-2xl flex items-center justify-center shadow-lg border border-slate-100 active:scale-95 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </a>
            </div>

            {{-- Tambah Janjian --}}
            {{-- <div class="flex items-center gap-3">
                <span class="bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-100 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Tambah Janjian</span>
                <button class="w-12 h-12 bg-white text-teal-700 rounded-2xl flex items-center justify-center shadow-lg border border-slate-100 active:scale-95 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </button>
            </div> --}}
        </div>

        <button 
            @click="open = !open" 
            :class="open ? 'bg-slate-800 shadow-slate-900/40' : 'bg-teal-900 shadow-teal-900/40'"
            class="w-14 h-14 text-white rounded-2xl flex items-center justify-center shadow-2xl active:scale-90 transition-all duration-300 relative overflow-hidden"
        >
            <svg x-show="!open" 
                 x-transition:enter="transition duration-300"
                 x-transition:enter-start="opacity-0 scale-50 rotate-90"
                 x-transition:enter-end="opacity-100 scale-100 rotate-0"
                 class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15h2m-2 4h2" />
            </svg>
            <svg x-show="open" 
                 x-transition:enter="transition duration-300"
                 x-transition:enter-start="opacity-0 scale-50 -rotate-90"
                 x-transition:enter-end="opacity-100 scale-100 rotate-0"
                 class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- BOTTOM NAVBAR --}}
    <x-navigation.therapist-navbar active="jadwal" />

</x-layouts.mobile-app>

<style>
    /* Custom utility to hide scrollbar but keep functionality */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    body {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        background-color: #F8FAFB;
    }
</style>

@endsection