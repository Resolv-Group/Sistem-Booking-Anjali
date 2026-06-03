@extends('components.layouts.app')

@section('title', 'Agenda Sesi Terapis')

@section('content')

    @php
        $totalPatients = collect($sessions)->flatMap(fn($s) => $s['patients'])->count();
        $donePatients = collect($sessions)->flatMap(fn($s) => $s['patients'])->where('is_done', true)->count();
        $pct = $totalPatients > 0 ? round(($donePatients / $totalPatients) * 100) : 0;
    @endphp

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{
        showSuccess: true
    }">

        {{-- 1. TOPBAR --}}
        <x-ui.topbar title="Rumah Terapi Anjali">
            <x-slot:right>
                <div class="h-10 w-10 rounded-full border border-orange-100 p-0.5 bg-white">
                    <img src="https://i.pravatar.cc/100?u=therapist" class="w-full h-full rounded-full object-cover">
                </div>
            </x-slot:right>
        </x-ui.topbar>

        <div class="px-6 pt-8 pb-32 space-y-8">

            {{-- SUCCESS NOTIFICATION --}}
            @if (session('success'))
                <div x-show="showSuccess" x-init="setTimeout(() => showSuccess = false, 3000)" x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                    class="bg-teal-600 text-white rounded-2xl p-4 text-sm font-bold text-center shadow-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- 2. TITLE SECTION --}}
            <div class="space-y-2">
                <h2 class="text-3xl font-semibold text-teal-900 tracking-tight">Agenda Sesi Anda</h2>
                <p class="text-base text-slate-500 font-medium leading-relaxed">Pantau seluruh jadwal konsultasi dan
                    persiapan pasien Anda hari ini dalam satu tampilan.</p>
            </div>

            {{-- 3. SUMMARY PROGRESS CARD --}}
            <div class="bg-slate-100/50 p-6 rounded-3xl border border-white shadow-sm space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-[0.2em] mb-1">Ringkasan Hari
                            Ini</p>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Total Sesi</h3>
                    </div>
                    <div class="text-right">
                        <span
                            class="text-3xl font-semibold text-teal-800 leading-none">{{ sprintf('%02d', $totalPatients) }}</span>
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-tighter">Janjian</p>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="space-y-2">
                    <div class="h-2.5 w-full bg-white rounded-full overflow-hidden shadow-inner border border-slate-200/50">
                        <div class="h-full bg-teal-600 rounded-full shadow-lg shadow-teal-500/20"
                            style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium italic leading-relaxed">
                        Anda telah menyelesaikan {{ $pct }}% dari janjian terjadwal hari ini.
                    </p>
                </div>
            </div>

            {{-- 4. CALENDAR SELECTOR --}}
            <div x-data="{
                selectedDate: '{{ $selectedDate }}',
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
                    window.location.href = '{{ route('therapist.jadwal') }}?date=' + dateStr;
                },
                getMonthYear() {
                    let d = new Date(this.selectedDate);
                    return d.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
                }
            }" class="space-y-4">
                <div class="flex justify-between items-end">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 leading-none" x-text="getMonthYear()">Mei 2026</h3>
                        <p class="text-xs font-semibold text-slate-400 mt-1 uppercase tracking-widest">{{ $totalPatients }}
                            Janjian Tanggal Ini</p>
                    </div>
                    <div class="relative">
                        <input type="date" x-model="selectedDate" @change="selectDate(selectedDate)"
                            class="absolute inset-0 opacity-0 cursor-pointer z-10">
                        <button class="p-2.5 bg-white border border-slate-200 rounded-xl text-teal-600 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Days Row --}}
                <div class="flex gap-2 pb-2">
                    <template x-for="d in days" :key="d.fullDate">
                        <button @click="selectDate(d.fullDate)"
                            class="flex-1 min-w-0 flex flex-col items-center justify-center py-3 rounded-2xl transition-all duration-300"
                            :class="d.fullDate === selectedDate ? 'bg-teal-800 text-white shadow-xl shadow-teal-900/20' :
                                'bg-white text-slate-400 border border-slate-100 shadow-sm'">
                            <span class="text-[10px] font-semibold uppercase tracking-widest"
                                :class="d.fullDate === selectedDate ? 'opacity-70' : ''" x-text="d.dayName"></span>
                            <span class="text-base font-semibold mt-1" x-text="d.dateNum"></span>
                            <template x-if="d.fullDate === selectedDate">
                                <div class="w-1 h-1 rounded-full bg-orange-400 mt-1 animate-pulse"></div>
                            </template>
                        </button>
                    </template>
                </div>
            </div>

            {{-- 5. DYNAMIC AGENDA LIST --}}
            <div class="space-y-8">
                @forelse($sessions as $session)
                    <div x-data="{ open: {{ $session['status'] === 'ongoing' ? 'true' : 'false' }} }" class="relative">
                        <div class="flex gap-4">
                            {{-- Timeline Marker --}}
                            <div class="flex flex-col items-center w-12 pt-1 shrink-0">
                                <span class="text-xs font-bold text-slate-700">{{ $session['time_start'] }}</span>
                                <span
                                    class="text-[9px] font-semibold text-slate-400 mt-0.5">{{ $session['time_end'] }}</span>
                                <div class="flex-1 w-px bg-slate-200 mt-3 mb-1 rounded-full"></div>
                            </div>

                            {{-- Session Group Content --}}
                            <div class="flex-1">
                                {{-- Header --}}
                                <div @click="open = !open"
                                    class="flex justify-between items-start cursor-pointer group pb-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full 
                                            {{ $session['status'] === 'ongoing' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]' : '' }}
                                            {{ $session['status'] === 'completed' ? 'bg-teal-600' : '' }}
                                            {{ $session['status'] === 'waiting' ? 'bg-slate-300' : '' }}">
                                            </span>
                                            <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                                {{ $session['status_text'] }}</h4>
                                        </div>
                                        {{-- Patient Preview --}}
                                        <div x-show="!open" class="flex items-center gap-1.5 ml-3.5">
                                            <p class="text-[11px] font-medium text-slate-400 truncate max-w-[180px]">
                                                @foreach ($session['patients'] as $p)
                                                    {{ explode(' ', $p['name'])[0] }}{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            </p>
                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                            <span
                                                class="text-[10px] font-bold text-teal-600/70">{{ count($session['patients']) }}
                                                Pasien</span>
                                        </div>
                                    </div>
                                    <div
                                        class="p-1.5 bg-slate-100 rounded-lg text-slate-400 group-hover:bg-slate-200 transition-colors">
                                        <svg class="w-4 h-4 transition-transform duration-300"
                                            :class="!open ? '-rotate-90' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Patient List --}}
                                <div x-show="open" x-collapse>
                                    <div class="space-y-4 pt-1 pb-6">
                                        @forelse($session['patients'] as $patient)
                                            <div
                                                class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm transition-all hover:shadow-md">
                                                <div class="flex flex-col space-y-4">

                                                    {{-- Morphing State Header --}}
                                                    <div
                                                        class="flex justify-between items-center border-b border-slate-50 pb-3">
                                                        @if ($patient['status_pasien'] === 'menunggu')
                                                            <div class="flex items-center gap-2">
                                                                <span
                                                                    class="text-xs font-black text-slate-400 bg-slate-100/70 px-2.5 py-1 rounded-lg uppercase tracking-widest">
                                                                    {{ $session['time_start'] }} — MENUNGGU
                                                                </span>
                                                            </div>
                                                        @elseif($patient['status_pasien'] === 'sedang_berjalan')
                                                            <div class="flex items-center gap-2">
                                                                <span
                                                                    class="text-xs font-black text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg uppercase tracking-widest flex items-center gap-1.5 animate-pulse">
                                                                    <span
                                                                        class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                                    {{ $session['time_start'] }} • SEDANG BERLANGSUNG
                                                                </span>
                                                            </div>
                                                        @elseif($patient['status_pasien'] === 'selesai')
                                                            <div class="flex items-center gap-2">
                                                                <span
                                                                    class="text-xs font-black text-teal-700 bg-teal-50 px-2.5 py-1 rounded-lg uppercase tracking-widest flex items-center gap-1">
                                                                    ✓ SELESAI
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Patient Profile Section --}}
                                                    <div class="space-y-1">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <h5
                                                                class="text-lg font-extrabold text-slate-800 tracking-tight leading-tight">
                                                                {{ $patient['name'] }}
                                                            </h5>
                                                            @if ($patient['is_group'])
                                                                <span
                                                                    class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-lg bg-violet-50 text-violet-600 border border-violet-100">
                                                                    Grup
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-lg bg-slate-100 text-slate-400 border border-slate-200">
                                                                    Individu
                                                                </span>
                                                            @endif
                                                        </div>
                                                        {{-- type now shows all layanan e.g. "Fisioterapi, Akupuntur" --}}
                                                        <p
                                                            class="text-xs font-semibold text-teal-600/80 uppercase tracking-widest">
                                                            {{ $patient['type'] }}</p>
                                                    </div>

                                                    {{-- Live Duration Timer Badge (sedang_berjalan only) --}}
                                                    @if ($patient['status_pasien'] === 'sedang_berjalan' && $patient['duration'])
                                                        <div
                                                            class="px-3.5 py-2.5 bg-emerald-50/60 text-emerald-700 text-xs font-black rounded-xl border border-emerald-100/50 flex items-center gap-2 w-fit">
                                                            <span class="animate-spin text-sm">⏱</span>
                                                            <span class="tracking-wide">{{ $patient['duration'] }}</span>
                                                        </div>
                                                    @endif

                                                    {{-- Complaint or Session Summary --}}
                                                    <div class="bg-slate-50 p-4 rounded-2xl border border-white space-y-1">
                                                        @if ($patient['status_pasien'] === 'selesai')
                                                            <p
                                                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                                                Ringkasan:</p>
                                                            <p
                                                                class="text-xs text-slate-600 leading-relaxed font-bold italic">
                                                                "{{ $patient['ringkasan_sesi'] ?: 'Terapi berjalan baik.' }}"
                                                            </p>
                                                        @else
                                                            <p
                                                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                                                Keluhan:</p>
                                                            <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                                                {{ $patient['complaint'] }}
                                                            </p>
                                                        @endif
                                                    </div>

                                                    {{-- Bottom status indicators --}}
                                                    @if ($patient['status_pasien'] !== 'selesai')
                                                        <div class="flex items-center gap-4 text-slate-400">
                                                            <div class="flex items-center gap-1.5">
                                                                <div
                                                                    class="w-5 h-5 rounded-full flex items-center justify-center {{ $patient['has_summary'] ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-300' }}">
                                                                    <svg class="w-3 h-3" fill="currentColor"
                                                                        viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd"
                                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                </div>
                                                                <span
                                                                    class="text-[10px] font-bold {{ $patient['has_summary'] ? 'text-emerald-700/70' : 'text-slate-400' }}">Catatan</span>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- Morphing State CTAs --}}
                                                    <div class="flex gap-3 pt-1 w-full">
                                                        @if ($patient['status_pasien'] === 'menunggu')
                                                            <form
                                                                action="{{ route('therapist.session.start', $patient['id']) }}"
                                                                method="POST" class="w-full">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="w-full py-4 bg-teal-800 text-white rounded-xl text-center text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-teal-900/10 active:scale-95 transition-all hover:bg-teal-900">
                                                                    Mulai Sesi
                                                                </button>
                                                            </form>
                                                        @elseif($patient['status_pasien'] === 'sedang_berjalan')
                                                            <div class="grid grid-cols-2 gap-3 w-full">
                                                                <a href="{{ route('therapist.ringkasan-sesi', ['id' => $patient['id']]) }}"
                                                                    class="py-4 bg-white border border-slate-200 text-slate-700 text-center rounded-xl text-[10px] font-black uppercase tracking-[0.1em] active:scale-95 transition-all shadow-sm hover:bg-slate-50">
                                                                    Buka Pencatatan
                                                                </a>
                                                                <a href="{{ route('therapist.ringkasan-sesi', ['id' => $patient['id']]) }}?complete=1"
                                                                    class="py-4 bg-teal-800 text-white text-center rounded-xl text-[10px] font-black uppercase tracking-[0.15em] active:scale-95 transition-all shadow-lg shadow-teal-950/10 hover:bg-teal-900">
                                                                    Selesaikan
                                                                </a>
                                                            </div>
                                                        @elseif($patient['status_pasien'] === 'selesai')
                                                            <a href="{{ route('therapist.ringkasan-sesi', ['id' => $patient['id']]) }}"
                                                                class="w-full py-4 bg-slate-100 text-slate-600 text-center rounded-xl text-[10px] font-black uppercase tracking-[0.2em] active:scale-[0.98] transition-all hover:bg-slate-200">
                                                                Lihat Catatan
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-xs text-slate-400 font-bold italic ml-3.5">Tidak ada pasien
                                                terjadwal di sesi ini.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 bg-white rounded-3xl border border-slate-100">
                        <div
                            class="w-16 h-16 mx-auto bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-400">Tidak ada sesi agenda terapis hari ini.</p>
                    </div>
                @endforelse
            </div>

        </div>

        {{-- SPEED DIAL --}}
        <div x-data="{ open: false }" class="fixed bottom-24 right-6 z-50 flex flex-col items-end gap-3">
            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 scale-90"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-90" class="flex flex-col items-end gap-3 mb-2"
                x-cloak>
                <div class="flex items-center gap-3">
                    <span
                        class="bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-100 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Atur
                        Jam Kerja</span>
                    <a href="{{ route('therapist.atur-jam-kerja') }}"
                        class="w-12 h-12 bg-white text-teal-700 rounded-2xl flex items-center justify-center shadow-lg border border-slate-100 active:scale-95 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </a>
                </div>
            </div>

            <button @click="open = !open"
                :class="open ? 'bg-slate-800 shadow-slate-900/40' : 'bg-teal-900 shadow-teal-900/40'"
                class="w-14 h-14 text-white rounded-2xl flex items-center justify-center shadow-2xl active:scale-90 transition-all duration-300 relative overflow-hidden">
                <svg x-show="!open" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <svg x-show="open" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- BOTTOM NAVBAR --}}
        <x-navigation.therapist-navbar active="jadwal" />

    </x-layouts.mobile-app>

    <style>
        /* Custom utility to hide scrollbar but keep functionality */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #F8FAFB;
        }
    </style>

@endsection
