@extends('components.layouts.app')

@section('title', 'Ringkasan Sesi')

@section('content')

    <x-layouts.mobile-app class="bg-[#fcfcfc] min-h-screen" x-data="{
        photos: [],
        addPhotos(event) {
            const files = Array.from(event.target.files);
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => { this.photos.push(e.target.result); };
                reader.readAsDataURL(file);
            });
        },
        // Alpine Data for Dropdowns
        habits: {
            makanSuhu: ['Hangat'],
            makanRasa: ['Manis'],
            minumSuhu: ['Hangat'],
            minumTipe: ['Soda'],
            keringat: 'Normal',
            babKapan: 'Setiap Hari',
            babBentuk: 'Normal',
            bakFrekuensi: 'Normal',
            bakWarna: 'Kuning Muda'
        },
        toggleHabit(field, value) {
            if (this.habits[field].includes(value)) {
                this.habits[field] = this.habits[field].filter(v => v !== value);
            } else {
                this.habits[field].push(value);
            }
        },
        evaluation: {
            perbaikan: '0%',
            nyeri: 5
        },
        isDone: false
    }">

        {{-- 1. HEADER (STIKY GLASSY) --}}
        <div
            class="px-6 py-4 flex justify-between items-center bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <a href="{{ route('therapist.jadwal') }}" class="p-1 -ml-1 text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-sm font-semibold text-teal-800 uppercase tracking-widest">Rumah Terapi Anjali</h1>
            </div>
            <div class="w-9 h-9 rounded-xl border-2 border-orange-100 p-0.5 bg-white">
                <img src="https://i.pravatar.cc/100?u=therapist" class="w-full h-full rounded-lg object-cover">
            </div>
        </div>

        <div class="px-6 pt-8 pb-32 space-y-10">

            {{-- 2. HEADER TITLE --}}
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold text-teal-900 tracking-tight">Ringkasan Sesi</h2>
                <p class="text-sm text-slate-400 font-medium">Input rekam medis pasien: <span
                        class="text-slate-700 font-semibold">David Purnama</span></p>
            </div>

            {{-- 3. PATIENT PROFILE CARD --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-4">
                    {{-- <div
                        class="w-16 h-16 rounded-xl bg-slate-50 overflow-hidden border border-slate-100 shrink-0 shadow-inner">
                        <img src="https://i.pravatar.cc/150?u=david" class="w-full h-full object-cover">
                    </div> --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-slate-800 truncate">David Purnama</h3>
                        <p class="text-[12px] font-semibold text-slate-400 uppercase tracking-tight mt-0.5">42 Thn, Pria</p>

                        <div class="flex items-center gap-4 mt-2.5">
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-slate-300 uppercase tracking-tighter">Goldar</span>
                                <span class="text-[13px] font-black text-teal-600">O+</span>
                            </div>
                            <div class="w-px h-5 bg-slate-100"></div>
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-slate-300 uppercase tracking-tighter">Tinggi</span>
                                <span class="text-[13px] font-black text-slate-700">175<span
                                        class="text-[11px] font-semibold text-slate-400 ml-0.5">cm</span></span>
                            </div>
                            <div class="w-px h-5 bg-slate-100"></div>
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-slate-300 uppercase tracking-tighter">Berat</span>
                                <span class="text-[13px] font-black text-slate-700">70<span
                                        class="text-[11px] font-semibold text-slate-400 ml-0.5">kg</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="flex items-center justify-between px-3 py-2.5 bg-teal-50/50 rounded-xl border border-teal-100/30">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-teal-500"></div>
                        <span class="text-[9px] font-bold text-teal-700 uppercase tracking-widest">Jadwal Sesi</span>
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 uppercase">Sel, 13 Mei - 10:00 AM</span>
                </div>
            </div>


            {{-- 4. KELUHAN UTAMA --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 ml-1">
                    <div class="w-1.5 h-4 bg-teal-500 rounded-full"></div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Keluhan Utama Pasien</h3>
                </div>
                <div
                    class="p-5 bg-slate-50 rounded-2xl border border-slate-100 text-sm font-medium text-slate-600 leading-relaxed italic shadow-inner">
                    "Nyeri punggung bawah menjalar ke kaki kanan sejak 3 hari yang lalu. Terasa kaku saat bangun tidur di
                    pagi hari."
                </div>
            </div>

            {{-- 5. TENSI PROGNOSA (Special Triple Input) --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 ml-1">
                    <div class="w-1.5 h-4 bg-teal-500 rounded-full"></div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Pemeriksaan Fisik</h3>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-3 bg-teal-200 rounded-full group-hover:bg-teal-500 transition-colors">
                        </div>
                        <label class="block text-[12px] font-semibold text-slate-400 uppercase tracking-[0.2em]">Tensi
                            Prognosa</label>
                    </div>
                    <div class="flex items-center bg-slate-50 rounded-2xl p-2 border border-slate-100">
                        <input type="number" placeholder="SYS"
                            class="w-full bg-transparent p-3 text-sm font-bold text-slate-700 text-center outline-none">
                        <span class="text-slate-300 font-bold text-xl px-1">/</span>
                        <input type="number" placeholder="DIA"
                            class="w-full bg-transparent p-3 text-sm font-bold text-slate-700 text-center outline-none">
                        <span class="text-slate-300 font-bold text-xl px-1">/</span>
                        <input type="number" placeholder="PULSE"
                            class="w-full bg-transparent p-3 text-sm font-bold text-slate-700 text-center outline-none">
                    </div>
                </div>
            </div>

            {{-- 6. PEMERIKSAAN AREA (Vertical List) --}}
            <div class="space-y-4">
                <div
                    class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm divide-y divide-slate-50">
                    @foreach (['Tubuh', 'Leher', 'Dada', 'Perut', 'Tangan', 'Kaki', 'Punggung', 'Pinggang'] as $area)
                        <div class="p-5 space-y-2 group hover:bg-slate-50/50 transition-colors">
                            <div class="flex items-center gap-2">
                                <div class="w-1 h-3 bg-teal-200 rounded-full group-hover:bg-teal-500 transition-colors">
                                </div>
                                <label
                                    class="block text-[12px] font-semibold text-slate-400 uppercase tracking-[0.2em]">{{ $area }}</label>
                            </div>
                            <input type="text" placeholder="Deskripsi kondisi {{ strtolower($area) }}..."
                                class="w-full text-sm font-bold text-slate-700 outline-none bg-transparent placeholder:text-slate-300 placeholder:font-normal">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 7. KEBIASAAN & POLA HIDUP --}}
            <div class="space-y-6">
                <div class="flex items-center gap-3 ml-1">
                    <div class="w-1.5 h-4 bg-teal-500 rounded-full"></div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Kebiasaan & Pola Hidup</h3>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-8">
                    {{-- Makan --}}
                    <div class="space-y-5">
                        <div class="space-y-2">
                            <label class="text-[12px] font-semibold text-slate-400 uppercase tracking-widest ml-1">Makan
                                (Suhu)</label>
                            <div class="flex gap-2">
                                @foreach (['Dingin', 'Hangat', 'Panas'] as $opt)
                                    <button type="button" @click="toggleHabit('makanSuhu', '{{ $opt }}')"
                                        :class="habits.makanSuhu.includes('{{ $opt }}') ?
                                            'bg-teal-800 text-white border-teal-800' :
                                            'bg-white text-slate-400 border-slate-200'"
                                        class="flex-1 py-2.5 rounded-xl border text-sm font-semibold uppercase transition-all active:scale-95">
                                        {{ $opt }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-2" x-data="{ open: false }">
                            <label class="text-[12px] font-semibold text-slate-400 uppercase tracking-widest ml-1">Makan
                                (Rasa)</label>
                            <div class="relative">
                                <button @click="open = !open" type="button"
                                    class="w-full bg-slate-50 border border-slate-100 rounded-xl p-4 flex justify-between items-center group transition-all">
                                    <span class="text-sm font-bold text-slate-700 truncate pr-4"
                                        x-text="habits.makanRasa.length ? habits.makanRasa.join(', ') : 'Pilih Rasa...'"></span>
                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-teal-500 transition-colors shrink-0"
                                        :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-collapse
                                    class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-30 py-2 overflow-hidden">
                                    @foreach (['Asam', 'Manis', 'Pahit', 'Asin', 'Pedas'] as $rasa)
                                        <button type="button" @click="toggleHabit('makanRasa', '{{ $rasa }}')"
                                            class="w-full px-5 py-3 text-left text-sm font-semibold hover:bg-teal-50 transition-colors flex justify-between items-center"
                                            :class="habits.makanRasa.includes('{{ $rasa }}') ?
                                                'text-teal-700 bg-teal-50/50' : 'text-slate-600'">
                                            <span>{{ $rasa }}</span>
                                            <div x-show="habits.makanRasa.includes('{{ $rasa }}')"
                                                class="w-2 h-2 rounded-full bg-teal-500"></div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Minum --}}
                    <div class="space-y-2">
                        <label class="text-[12px] font-semibold text-slate-400 uppercase tracking-widest ml-1">Minum
                            (Suhu)</label>
                        <div class="flex gap-2">
                            @foreach (['Dingin', 'Normal', 'Hangat'] as $opt)
                                <button type="button" @click="toggleHabit('minumSuhu', '{{ $opt }}')"
                                    :class="habits.minumSuhu.includes('{{ $opt }}') ?
                                        'bg-teal-800 text-white border-teal-800' :
                                        'bg-white text-slate-400 border-slate-200'"
                                    class="flex-1 py-2.5 rounded-xl border text-sm font-semibold uppercase transition-all active:scale-95">
                                    {{ $opt }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-2" x-data="{ open: false }">
                        <label class="text-[12px] font-semibold text-slate-400 uppercase tracking-widest ml-1">Minum
                            (Tipe)</label>
                        <div class="relative">
                            <button @click="open = !open" type="button"
                                class="w-full bg-slate-50 border border-slate-100 rounded-xl p-4 flex justify-between items-center group transition-all">
                                <span class="text-sm font-bold text-slate-700 truncate pr-4"
                                    x-text="habits.minumTipe.length ? habits.minumTipe.join(', ') : 'Pilih Tipe...'"></span>
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-teal-500 transition-colors shrink-0"
                                    :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-collapse
                                class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-30 py-2 overflow-hidden">
                                @foreach (['Soda', 'Manis', 'Kopi', 'Teh'] as $tipe)
                                    <button type="button" @click="toggleHabit('minumTipe', '{{ $tipe }}')"
                                        class="w-full px-5 py-3 text-left text-sm font-semibold hover:bg-teal-50 transition-colors flex justify-between items-center"
                                        :class="habits.minumTipe.includes('{{ $tipe }}') ?
                                            'text-teal-700 bg-teal-50/50' : 'text-slate-600'">
                                        <span>{{ $tipe }}</span>
                                        <div x-show="habits.minumTipe.includes('{{ $tipe }}')"
                                            class="w-2 h-2 rounded-full bg-teal-500"></div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Keringat --}}
                    <div class="space-y-2" x-data="{ open: false }">
                        <label
                            class="text-[12px] font-semibold text-slate-400 uppercase tracking-widest ml-1">Keringat</label>
                        <div class="relative">
                            <button @click="open = !open" type="button"
                                class="w-full bg-slate-50 border border-slate-100 rounded-xl p-4 flex justify-between items-center group transition-all">
                                <span class="text-sm font-bold text-slate-700" x-text="habits.keringat"></span>
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-teal-500 transition-colors"
                                    :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-collapse
                                class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-30 py-2 overflow-hidden">
                                @foreach (['Normal', 'Sering', 'Jarang'] as $k)
                                    <button type="button" @click="habits.keringat = '{{ $k }}'; open = false"
                                        class="w-full px-5 py-3 text-left text-sm font-semibold hover:bg-teal-50 transition-colors"
                                        :class="habits.keringat === '{{ $k }}' ? 'text-teal-700 bg-teal-50/50' :
                                            'text-slate-600'">
                                        {{ $k }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- BAB --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2" x-data="{ open: false }">
                            <label
                                class="text-[12px] font-semibold text-slate-400 uppercase tracking-widest ml-1">BAB</label>
                            <div class="relative">
                                <button @click="open = !open" type="button"
                                    class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3.5 flex justify-between items-center group">
                                    <span class="text-xs font-bold text-slate-700" x-text="habits.babKapan"></span>
                                    <svg class="w-3 h-3 text-slate-300" :class="open ? 'rotate-180' : ''" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-30 py-1">
                                    @foreach (['Setiap Hari', 'Hari Sekali', 'Dua Hari Sekali', 'Tidak Tentu'] as $opt)
                                        <button type="button"
                                            @click="habits.babKapan = '{{ $opt }}'; open = false"
                                            class="w-full px-4 py-2 text-left text-xs font-semibold hover:bg-teal-50">{{ $opt }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2" x-data="{ open: false }">
                            <label class="text-[12px] font-semibold text-slate-400 uppercase tracking-widest ml-1">Bentuk
                                BAB</label>
                            <div class="relative">
                                <button @click="open = !open" type="button"
                                    class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3.5 flex justify-between items-center group">
                                    <span class="text-xs font-bold text-slate-700" x-text="habits.babBentuk"></span>
                                    <svg class="w-3 h-3 text-slate-300" :class="open ? 'rotate-180' : ''" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-30 py-1">
                                    @foreach (['Normal', 'Keras', 'Cair'] as $opt)
                                        <button type="button"
                                            @click="habits.babBentuk = '{{ $opt }}'; open = false"
                                            class="w-full px-4 py-2 text-left text-xs font-semibold hover:bg-teal-50">{{ $opt }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BAK --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2" x-data="{ open: false }">
                            <label class="text-[12px] font-semibold text-slate-400 uppercase tracking-widest ml-1">BAK
                                (Frekuensi)</label>
                            <div class="relative">
                                <button @click="open = !open" type="button"
                                    class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3.5 flex justify-between items-center group">
                                    <span class="text-xs font-bold text-slate-700" x-text="habits.bakFrekuensi"></span>
                                    <svg class="w-3 h-3 text-slate-300" :class="open ? 'rotate-180' : ''" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-30 py-1">
                                    @foreach (['Normal', 'Tersisa', 'Sedikit Tapi Sering'] as $opt)
                                        <button type="button"
                                            @click="habits.bakFrekuensi = '{{ $opt }}'; open = false"
                                            class="w-full px-4 py-2 text-left text-xs font-semibold hover:bg-teal-50">{{ $opt }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2" x-data="{ open: false }">
                            <label class="text-[12px] font-semibold text-slate-400 uppercase tracking-widest ml-1">BAK
                                (Warna)</label>
                            <div class="relative">
                                <button @click="open = !open" type="button"
                                    class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3.5 flex justify-between items-center group">
                                    <span class="text-xs font-bold text-slate-700" x-text="habits.bakWarna"></span>
                                    <svg class="w-3 h-3 text-slate-300" :class="open ? 'rotate-180' : ''" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-30 py-1">
                                    @foreach (['Kuning Bening', 'Kuning Pekat', 'Merah', 'Berbusa'] as $opt)
                                        <button type="button"
                                            @click="habits.bakWarna = '{{ $opt }}'; open = false"
                                            class="w-full px-4 py-2 text-left text-xs font-semibold hover:bg-teal-50">{{ $opt }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 8. EVALUASI HASIL TERAPI --}}
            <div class="space-y-6">
                <div class="flex items-center gap-3 ml-1">
                    <div class="w-1.5 h-4 bg-teal-500 rounded-full"></div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Evaluasi Hasil Terapi</h3>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-8">
                    {{-- Pain Scale (VAS) --}}
                    <div class="space-y-4">
                        <div class="flex justify-between items-end ml-1">
                            <label class="text-[12px] font-semibold text-slate-400 uppercase tracking-widest">Skala Nyeri (VAS)</label>
                            <span class="text-2xl font-black text-teal-600" x-text="evaluation.nyeri"></span>
                        </div>
                        <div class="flex gap-1.5">
                            @foreach(range(1, 5) as $val)
                            <button type="button" 
                                @click="evaluation.nyeri = {{ $val }}"
                                :class="evaluation.nyeri === {{ $val }} ? 'bg-teal-600 text-white border-teal-600 scale-110 z-10' : 'bg-slate-50 text-slate-400 border-slate-100'"
                                class="flex-1 h-10 rounded-lg border text-sm font-semibold transition-all flex items-center justify-center shadow-sm">
                                {{ $val }}
                            </button>
                            @endforeach
                        </div>
                        <div class="flex justify-between px-1 text-sm font-semibold text-slate-300 uppercase tracking-tighter">
                            <span>Tidak Nyeri</span>
                            <span>Nyeri Hebat</span>
                        </div>
                    </div>

                    <hr class="border-slate-300">

                    {{-- Improvement Percentage --}}
                    <div class="space-y-4">
                        <label class="text-[12px] font-semibold text-slate-400 uppercase tracking-widest ml-1">Tingkat Perbaikan</label>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach(['0%', '25%', '50%', '75%', '100%'] as $pct)
                            <button type="button" 
                                @click="evaluation.perbaikan = '{{ $pct }}'"
                                :class="evaluation.perbaikan === '{{ $pct }}' ? 'bg-teal-800 text-white border-teal-800' : 'bg-white text-slate-400 border-slate-200'"
                                class="py-2.5 rounded-xl border text-sm font-semibold uppercase transition-all active:scale-95">
                                {{ $pct }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- 9. GOAL & SARAN --}}
            <div class="space-y-6">
                <div class="space-y-2">
                    <label
                        class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1 border-l-2 border-teal-500 pl-3">Goal
                        / Target Terapi</label>
                    <textarea placeholder="Target jangka pendek/panjang..."
                        class="w-full bg-white border border-slate-200 rounded-2xl p-5 text-sm font-medium text-slate-700 h-32 focus:border-teal-500 outline-none resize-none shadow-sm"></textarea>
                </div>
                <div class="space-y-2">
                    <label
                        class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1 border-l-2 border-teal-500 pl-3">Saran
                        / Rekomendasi</label>
                    <textarea placeholder="Latihan mandiri atau pantangan..."
                        class="w-full bg-white border border-slate-200 rounded-2xl p-5 text-sm font-medium text-slate-700 h-32 focus:border-teal-500 outline-none resize-none shadow-sm"></textarea>
                </div>
                <div class="space-y-2">
                    <label
                        class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1 border-l-2 border-teal-500 pl-3">Catatan
                        Khusus</label>
                    <textarea placeholder="Catatan khusus ..."
                        class="w-full bg-white border border-slate-200 rounded-2xl p-5 text-sm font-medium text-slate-700 h-32 focus:border-teal-500 outline-none resize-none shadow-sm"></textarea>
                </div>
            </div>

            {{-- 10. DOKUMENTASI FOTO --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 ml-1">
                    <div class="w-1.5 h-4 bg-teal-500 rounded-full"></div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Dokumentasi Sesi</h3>
                </div>

                <div class="grid grid-cols-3 gap-3" x-show="photos.length > 0">
                    <template x-for="(photo, index) in photos" :key="index">
                        <div
                            class="relative aspect-square rounded-2xl overflow-hidden border border-slate-100 shadow-sm bg-white">
                            <img :src="photo" class="w-full h-full object-cover">
                            <button @click="photos.splice(index, 1)"
                                class="absolute top-1.5 right-1.5 bg-rose-500 text-white rounded-full p-1 shadow-md active:scale-90 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="3">
                                    <path d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <label class="block cursor-pointer">
                    <div
                        class="bg-slate-50/50 border-2 border-dashed border-slate-200 rounded-3xl p-10 text-center space-y-3 hover:border-teal-500 transition-all active:bg-slate-100/50">
                        <div
                            class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center mx-auto text-slate-400 shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Unggah Dokumentasi</p>
                        <p class="text-[10px] font-medium text-slate-400 italic">Maksimal 5 foto hasil terapi (JPG, PNG)
                        </p>
                    </div>
                    <input type="file" multiple accept="image/*" class="hidden" @change="addPhotos($event)">
                </label>
            </div>

            {{-- 11. ACTIONS --}}
            <div class="pt-6 space-y-6">
                {{-- Session Completion Toggle --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between group active:scale-[0.98] transition-all cursor-pointer"
                    @click="isDone = !isDone">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-colors"
                            :class="isDone ? 'bg-teal-100 text-teal-600' : 'bg-slate-100 text-slate-400'">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="space-y-0.5">
                            <h4 class="text-sm font-bold text-slate-800">Sesi Selesai?</h4>
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-tight" x-text="isDone ? 'Sesi Akan Ditandai Selesai' : 'Centang Jika Sesi Berakhir'"></p>
                        </div>
                    </div>
                    <div class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all"
                        :class="isDone ? 'bg-teal-500 border-teal-500' : 'border-slate-200 bg-white'">
                        <svg x-show="isDone" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <div class="space-y-4">
                    <x-ui.button class="w-full py-5 bg-teal-800 text-white rounded-2xl text-base font-bold uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                        Simpan Rekam Medis
                    </x-ui.button>
                    <button type="button" onclick="history.back()"
                        class="w-full py-4 text-slate-400 text-sm font-bold uppercase tracking-widest active:scale-95 transition-all hover:text-slate-700">
                        Batalkan
                    </button>
                </div>
            </div>
        </div>

        <x-navigation.therapist-navbar active="jadwal" />

    </x-layouts.mobile-app>

    <style>
        /* Premium Dropdown Styling */
        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='3'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-position: right 1.25rem center;
            background-repeat: no-repeat;
            background-size: 1.15rem;
        }
    </style>

@endsection
