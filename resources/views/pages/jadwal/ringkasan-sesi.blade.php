@extends('components.layouts.app')

@section('title', 'Pencatatan Rekam Medis')

@section('content')

    <script>
        window.InitialHabits = {
            makanSuhu: @json($rekamMedis->makan_suhu ?? ['Hangat']),
            makanRasa: @json($rekamMedis->makan_rasa ?? ['Manis']),
            minumSuhu: @json($rekamMedis->minum_suhu ?? ['Hangat']),
            minumTipe: @json($rekamMedis->minum_tipe ?? ['Soda']),
            keringat: '{{ $rekamMedis->keringat ?? 'Normal' }}',
            babKapan: '{{ $rekamMedis->bab_kapan ?? 'Setiap Hari' }}',
            babBentuk: '{{ $rekamMedis->bab_bentuk ?? 'Normal' }}',
            bakFrekuensi: '{{ $rekamMedis->bak_frekuensi ?? 'Normal' }}',
            bakWarna: '{{ $rekamMedis->bak_warna ?? 'Kuning Muda' }}'
        };
        window.InitialEvaluation = {
            perbaikan: '{{ $rekamMedis->tingkat_perbaikan ?? '0%' }}',
            nyeri: {{ $rekamMedis->skala_nyeri ?? 5 }}
        };

        window.photos = @json($existingPhotos ?? []);
    </script>

    <x-layouts.mobile-app class="bg-[#fcfcfc] min-h-screen" x-data="{
        photos: window.photos,
        deletedPhotoIds: [],
        addPhotos(event) {
            const files = Array.from(event.target.files);
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.photos.push({ id: null, url: e.target.result, file: file });
                    this.updateFileInput();
                };
                reader.readAsDataURL(file);
            });
        },
        removePhoto(index) {
            const removed = this.photos[index];
            if (removed.id) {
                this.deletedPhotoIds.push(removed.id);
            }
            this.photos.splice(index, 1);
            this.updateFileInput();
        },
        updateFileInput() {
            const fileInput = this.$refs.fileInput;
            if (fileInput) {
                const dataTransfer = new DataTransfer();
                this.photos.forEach(photo => {
                    if (photo.file) {
                        dataTransfer.items.add(photo.file);
                    }
                });
                fileInput.files = dataTransfer.files;
            }
        },
        habits: window.InitialHabits,
        toggleHabit(field, value) {
            if (this.habits[field].includes(value)) {
                this.habits[field] = this.habits[field].filter(v => v !== value);
            } else {
                this.habits[field].push(value);
            }
        },
        evaluation: window.InitialEvaluation,
        isDone: ('{{ $bp->status_pasien === 'selesai' ? 'true' : 'false' }}' === 'true') || (new URLSearchParams(window.location.search).get('complete') === '1'),
        isSelesai: '{{ $bp->status_pasien }}' === 'selesai',
    
        vitals: {
            goldar: '{{ old('golongan_darah', $bp->pasien->golongan_darah) }}',
            tinggi: '{{ old('tinggi_badan', $bp->pasien->tinggi_badan) }}',
            berat: '{{ old('berat_badan', $bp->pasien->berat_badan) }}'
        },
    
        openGoldar: false,
    }">

        {{-- 1. HEADER (STICKY GLASSY) --}}
        <nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-100/80 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="javascript:void(0)" onclick="window.history.back()"
                        class="group flex items-center justify-center w-10 h-10 bg-white border border-slate-100 rounded-xl shadow-sm hover:bg-teal-50 transition-all active:scale-90">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-600" fill="none" stroke="currentColor"
                            stroke-width="3" viewBox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">Rekam
                            Medis</span>
                        <h1
                            class="text-sm font-black text-slate-800 tracking-tight leading-none uppercase truncate max-w-[180px]">
                            Ringkasan Sesi
                        </h1>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-full border-2 border-white shadow-sm overflow-hidden">
                    <img src="{{ asset('images/logo_anjali.jpg') }}" class="w-full h-full object-cover">
                </div>
            </div>
        </nav>

        {{-- Main Form Wrapper --}}
        <form action="{{ route('therapist.ringkasan-sesi.store', $bp->id) }}" method="POST" enctype="multipart/form-data"
            class="px-6 pt-8 pb-32 space-y-10">

            {{-- READ-ONLY BANNER (shown when status is selesai) --}}
            <div x-show="isSelesai" x-cloak
                class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3.5 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-amber-800 uppercase tracking-widest">Sesi Sudah Selesai</p>
                    <p class="text-[11px] font-medium text-amber-600 mt-0.5">Rekam medis ini bersifat <span
                            class="font-bold">read only</span> dan tidak dapat diubah.</p>
                </div>
            </div>
            @csrf

            {{-- Hidden fields to pass Alpine JS states --}}
            <input type="hidden" name="action_type" id="action-type" value="draft">
            <template x-for="val in habits.makanSuhu">
                <input type="hidden" name="makan_suhu[]" :value="val">
            </template>
            <template x-for="val in habits.makanRasa">
                <input type="hidden" name="makan_rasa[]" :value="val">
            </template>
            <template x-for="val in habits.minumSuhu">
                <input type="hidden" name="minum_suhu[]" :value="val">
            </template>
            <template x-for="val in habits.minumTipe">
                <input type="hidden" name="minum_tipe[]" :value="val">
            </template>
            <input type="hidden" name="keringat" :value="habits.keringat">
            <input type="hidden" name="bab_kapan" :value="habits.babKapan">
            <input type="hidden" name="bab_bentuk" :value="habits.babBentuk">
            <input type="hidden" name="bak_frekuensi" :value="habits.bakFrekuensi">
            <input type="hidden" name="bak_warna" :value="habits.bakWarna">

            <input type="hidden" name="skala_nyeri" :value="evaluation.nyeri">
            <input type="hidden" name="tingkat_perbaikan" :value="evaluation.perbaikan">
            <template x-for="id in deletedPhotoIds">
                <input type="hidden" name="deleted_fotos[]" :value="id">
            </template>

            {{-- 2. HEADER TITLE --}}
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold text-teal-900 tracking-tight">Pencatatan Rekam Medis</h2>
                <p class="text-sm text-slate-400 font-medium">Input rekam medis pasien: <span
                        class="text-slate-700 font-semibold">{{ $bp->pasien->nama_pasien }}</span></p>
            </div>

            {{-- 3. PATIENT PROFILE CARD --}}
            <div class="bg-white rounded-[2rem] border border-slate-200 p-6 shadow-sm space-y-5">
    <div class="flex items-center gap-4">
        <div class="flex-1 min-w-0">
            {{-- Nama Pasien --}}
            <h3 class="text-xl font-bold text-slate-800 truncate tracking-tight">{{ $bp->pasien->nama_pasien }}</h3>
            
            {{-- Umur & Gender (Sub-header) --}}
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                {{ $bp->pasien->tanggal_lahir ? $bp->pasien->tanggal_lahir->age . ' Tahun' : 'Umur -' }} 
                <span class="mx-1 text-slate-200">•</span>
                {{ $bp->pasien->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
            </p>

            {{-- 3.5 DATA FISIK (DIUBAH GAYANYA) --}}
            @if ($bp->pasien->golongan_darah != null || $bp->pasien->tinggi_badan != null || $bp->pasien->berat_badan != null)
                <div class="flex items-center gap-6 mt-4 py-3 border-y border-slate-50">
                    {{-- Goldar --}}
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-tighter">Goldar</span>
                        <div class="flex items-center gap-1">
                            <i data-lucide="droplet" class="w-3 h-3 text-rose-500 fill-rose-500"></i>
                            <span class="text-[14px] font-black text-slate-700">{{ $bp->pasien->golongan_darah ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="w-px h-6 bg-slate-100"></div>

                    {{-- Tinggi --}}
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-tighter">Tinggi</span>
                        <span class="text-[14px] font-black text-slate-700">
                            {{ $bp->pasien->tinggi_badan ?? '-' }}<span class="text-[10px] font-bold text-slate-400 ml-0.5">cm</span>
                        </span>
                    </div>

                    <div class="w-px h-6 bg-slate-100"></div>

                    {{-- Berat --}}
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-tighter">Berat</span>
                        <span class="text-[14px] font-black text-slate-700">
                            {{ $bp->pasien->berat_badan ?? '-' }}<span class="text-[10px] font-bold text-slate-400 ml-0.5">kg</span>
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Footer Info --}}
    <div class="flex items-center justify-between px-4 py-3 bg-teal-50/50 rounded-2xl border border-teal-100/30">
        <div class="flex items-center gap-2">
            <i data-lucide="activity" class="w-3.5 h-3.5 text-teal-600"></i>
            <span class="text-[9px] font-bold text-teal-700 uppercase tracking-widest">Sesi Terapi</span>
        </div>
        <span class="text-[10px] font-bold text-slate-600 uppercase">
            {{ $bp->layanan ? $bp->layanan->nama : 'Umum' }} 
            <span class="mx-1 text-slate-300">•</span>
            {{ $bp->booking->session ? \Carbon\Carbon::parse($bp->booking->session->tanggal_sesi)->translatedFormat('d M Y') : '' }}
        </span>
    </div>
</div>


            {{-- DATA FISIK PASIEN --}}
            @if ($bp->pasien->golongan_darah == null || $bp->pasien->tinggi_badan == null || $bp->pasien->berat_badan == null)
                <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 ml-1">
                        <div class="w-1.5 h-4 bg-teal-500 rounded-full"></div>
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Data Fisik Pasien</h3>
                    </div>

                    <div class="space-y-6">
                        {{-- Golongan Darah (Dropdown Style) --}}
                        @if ($bp->pasien->golongan_darah == null)
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Golongan
                                    Darah</label>
                                <div class="relative">
                                    <button @click="!isSelesai && (openGoldar = !openGoldar)" type="button"
                                        :class="isSelesai ? 'cursor-not-allowed opacity-60' : 'hover:border-teal-300'"
                                        class="w-full bg-slate-50 border border-slate-100 rounded-xl p-4 flex justify-between items-center group transition-all">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-6 h-6 rounded-lg bg-white flex items-center justify-center text-rose-500 shadow-sm border border-rose-50">
                                                <i data-lucide="droplet" class="w-3.5 h-3.5"></i>
                                            </div>
                                            <span class="text-sm font-bold text-slate-700"
                                                x-text="vitals.goldar ? 'Golongan Darah ' + vitals.goldar : 'Pilih Golongan Darah...'"></span>
                                        </div>
                                        <svg class="w-4 h-4 text-slate-300 group-hover:text-teal-500 transition-colors shrink-0"
                                            :class="openGoldar ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    {{-- Dropdown Options --}}
                                    <div x-show="openGoldar" @click.away="openGoldar = false" x-collapse
                                        class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 py-2 overflow-hidden"
                                        x-cloak>
                                        <div class="grid grid-cols-2 gap-1 p-2">
                                            @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $opt)
                                                <button type="button"
                                                    @click="vitals.goldar = '{{ $opt }}'; openGoldar = false"
                                                    class="px-4 py-3 text-left text-xs font-bold rounded-xl transition-colors flex justify-between items-center"
                                                    :class="vitals.goldar === '{{ $opt }}' ?
                                                        'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50'">
                                                    <span>{{ $opt }}</span>
                                                    <div x-show="vitals.goldar === '{{ $opt }}'"
                                                        class="w-1.5 h-1.5 rounded-full bg-teal-500"></div>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    <input type="hidden" name="golongan_darah" :value="vitals.goldar">
                                </div>
                            </div>
                        @endif

                        {{-- Tinggi & Berat --}}
                        <div class="grid grid-cols-2 gap-4">
                            @if ($bp->pasien->tinggi_badan == null)
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tinggi
                                        Badan</label>
                                    <div class="relative flex items-center">
                                        <input type="number" name="tinggi_badan" x-model="vitals.tinggi"
                                            placeholder="0" :readonly="isSelesai"
                                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 outline-none shadow-inner focus:ring-2 focus:ring-teal-500/20 transition-all">
                                        <span
                                            class="absolute right-4 text-[10px] font-bold text-slate-300 uppercase">cm</span>
                                    </div>
                                </div>
                            @endif

                            @if ($bp->pasien->berat_badan == null)
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Berat
                                        Badan</label>
                                    <div class="relative flex items-center">
                                        <input type="number" name="berat_badan" x-model="vitals.berat" placeholder="0"
                                            :readonly="isSelesai"
                                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 outline-none shadow-inner focus:ring-2 focus:ring-teal-500/20 transition-all">
                                        <span
                                            class="absolute right-4 text-[10px] font-bold text-slate-300 uppercase">kg</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Info Note --}}
                    <div class="flex items-start gap-2 px-2 py-3 bg-amber-50/50 rounded-xl border border-amber-100/50">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-amber-500 mt-0.5"></i>
                        <p class="text-[9px] text-amber-700 font-medium leading-tight">Data fisik akan disimpan permanen ke
                            profil pasien.</p>
                    </div>
                </div>
            @endif

            {{-- 4. KELUHAN UTAMA --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 ml-1">
                    <div class="w-1.5 h-4 bg-teal-500 rounded-full"></div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Keluhan Utama Pasien</h3>
                </div>
                <div
                    class="p-5 bg-slate-50 rounded-2xl border border-slate-100 text-sm font-medium text-slate-600 leading-relaxed italic shadow-inner">
                    "{{ $bp->keluhan_pasien ?: 'Tidak ada keluhan tertulis.' }}"
                </div>
            </div>

            {{-- DEDICATED CATATAN TERAPIS TEXTAREA --}}
            <div class="space-y-3">
                <div class="flex items-center gap-3 ml-1">
                    <div class="w-1.5 h-4 bg-teal-500 rounded-full"></div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Catatan Terapis</h3>
                </div>
                <textarea name="catatan_terapis" placeholder="Tuliskan catatan medis atau diagnosa terapis..." :readonly="isSelesai"
                    :class="isSelesai ? 'bg-slate-50 text-slate-500 cursor-not-allowed resize-none' :
                        'bg-white focus:border-teal-500 resize-none'"
                    class="w-full border border-slate-200 rounded-2xl p-5 text-sm font-semibold text-slate-700 h-36 outline-none shadow-sm placeholder:text-slate-300 placeholder:font-normal leading-relaxed">{{ old('catatan_terapis', $rekamMedis->catatan_terapis) }}</textarea>
            </div>

            {{-- DEDICATED RINGKASAN SESI TEXTAREA --}}
            <div class="space-y-3">
                <div class="flex items-center gap-3 ml-1">
                    <div class="w-1.5 h-4 bg-teal-500 rounded-full"></div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Ringkasan Sesi</h3>
                </div>
                <textarea name="ringkasan_sesi" :required="isDone && !isSelesai" :readonly="isSelesai"
                    placeholder="Tuliskan ringkasan singkat terapi (diperlukan jika sesi selesai)..."
                    :class="isSelesai ? 'bg-slate-50 text-slate-500 cursor-not-allowed resize-none' :
                        'bg-white focus:border-teal-500 resize-none'"
                    class="w-full border border-slate-200 rounded-2xl p-5 text-sm font-semibold text-slate-700 h-28 outline-none shadow-sm placeholder:text-slate-300 placeholder:font-normal leading-relaxed">{{ old('ringkasan_sesi', $bp->ringkasan_sesi) }}</textarea>
            </div>

            {{-- 5. TENSI PROGNOSA (Special Triple Input) --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 ml-1">
                    <div class="w-1.5 h-4 bg-teal-500 rounded-full"></div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Pemeriksaan Fisik</h3>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-3 bg-teal-200 rounded-full group-hover:bg-teal-500 transition-colors"></div>
                        <label class="block text-[12px] font-semibold text-slate-400 uppercase tracking-[0.2em]">Tensi
                            Prognosa</label>
                    </div>
                    <div class="flex items-center bg-slate-50 rounded-2xl p-2 border border-slate-100">
                        <input type="number" name="tensi_sys" placeholder="SYS"
                            value="{{ old('tensi_sys', $rekamMedis->tensi_sys) }}" :readonly="isSelesai"
                            :class="isSelesai ? 'cursor-not-allowed text-slate-400' : ''"
                            class="w-full bg-transparent p-3 text-sm font-bold text-slate-700 text-center outline-none">
                        <span class="text-slate-300 font-bold text-xl px-1">/</span>
                        <input type="number" name="tensi_dia" placeholder="DIA"
                            value="{{ old('tensi_dia', $rekamMedis->tensi_dia) }}" :readonly="isSelesai"
                            :class="isSelesai ? 'cursor-not-allowed text-slate-400' : ''"
                            class="w-full bg-transparent p-3 text-sm font-bold text-slate-700 text-center outline-none">
                        <span class="text-slate-300 font-bold text-xl px-1">/</span>
                        <input type="number" name="tensi_pulse" placeholder="PULSE"
                            value="{{ old('tensi_pulse', $rekamMedis->tensi_pulse) }}" :readonly="isSelesai"
                            :class="isSelesai ? 'cursor-not-allowed text-slate-400' : ''"
                            class="w-full bg-transparent p-3 text-sm font-bold text-slate-700 text-center outline-none">
                    </div>
                </div>
            </div>

            {{-- 6. PEMERIKSAAN AREA (Vertical List) --}}
            <div class="space-y-4">
                <div
                    class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm divide-y divide-slate-50">
                    @foreach (['Tubuh', 'Leher', 'Dada', 'Perut', 'Tangan', 'Kaki', 'Punggung', 'Pinggang'] as $area)
                        @php
                            $fieldName = 'area_' . strtolower($area);
                        @endphp
                        <div class="p-5 space-y-2 group hover:bg-slate-50/50 transition-colors">
                            <div class="flex items-center gap-2">
                                <div class="w-1 h-3 bg-teal-200 rounded-full group-hover:bg-teal-500 transition-colors">
                                </div>
                                <label
                                    class="block text-[12px] font-semibold text-slate-400 uppercase tracking-[0.2em]">{{ $area }}</label>
                            </div>
                            <input type="text" name="{{ $fieldName }}"
                                placeholder="Deskripsi kondisi {{ strtolower($area) }}..."
                                value="{{ old($fieldName, $rekamMedis->$fieldName) }}" :readonly="isSelesai"
                                :class="isSelesai ? 'cursor-not-allowed text-slate-400' : 'text-slate-700'"
                                class="w-full text-sm font-bold outline-none bg-transparent placeholder:text-slate-300 placeholder:font-normal">
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
                                    <button type="button"
                                        @click="!isSelesai && toggleHabit('makanSuhu', '{{ $opt }}')"
                                        :class="habits.makanSuhu.includes('{{ $opt }}') ?
                                            'bg-teal-800 text-white border-teal-800' :
                                            'bg-white text-slate-400 border-slate-200'"
                                        :disabled="isSelesai"
                                        :style="isSelesai ? 'opacity:0.6;cursor:not-allowed;pointer-events:none;' : ''"
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
                                <button @click="!isSelesai && (open = !open)" type="button"
                                    :class="isSelesai ? 'cursor-not-allowed opacity-60' : ''"
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
                                    class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-30 py-2 overflow-hidden"
                                    x-cloak>
                                    @foreach (['Asam', 'Manis', 'Pahit', 'Asin', 'Pedas'] as $rasa)
                                        <button type="button"
                                            @click="!isSelesai && toggleHabit('makanRasa', '{{ $rasa }}')"
                                            class="w-full px-5 py-3 text-left text-sm font-semibold hover:bg-teal-50 transition-colors flex justify-between items-center"
                                            :class="habits.makanRasa.includes('{{ $rasa }}') ?
                                                'text-teal-700 bg-teal-50/50' : 'text-slate-600'"
                                            :disabled="isSelesai">
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
                                <button type="button"
                                    @click="!isSelesai && toggleHabit('minumSuhu', '{{ $opt }}')"
                                    :class="habits.minumSuhu.includes('{{ $opt }}') ?
                                        'bg-teal-800 text-white border-teal-800' :
                                        'bg-white text-slate-400 border-slate-200'"
                                    :disabled="isSelesai"
                                    :style="isSelesai ? 'opacity:0.6;cursor:not-allowed;pointer-events:none;' : ''"
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
                            <button @click="!isSelesai && (open = !open)" type="button"
                                :class="isSelesai ? 'cursor-not-allowed opacity-60' : ''"
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
                                class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-30 py-2 overflow-hidden"
                                x-cloak>
                                @foreach (['Soda', 'Manis', 'Kopi', 'Teh'] as $tipe)
                                    <button type="button"
                                        @click="!isSelesai && toggleHabit('minumTipe', '{{ $tipe }}')"
                                        class="w-full px-5 py-3 text-left text-sm font-semibold hover:bg-teal-50 transition-colors flex justify-between items-center"
                                        :class="habits.minumTipe.includes('{{ $tipe }}') ?
                                            'text-teal-700 bg-teal-50/50' : 'text-slate-600'"
                                        :disabled="isSelesai">
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
                            <button @click="!isSelesai && (open = !open)" type="button"
                                :class="isSelesai ? 'cursor-not-allowed opacity-60' : ''"
                                class="w-full bg-slate-50 border border-slate-100 rounded-xl p-4 flex justify-between items-center group transition-all">
                                <span class="text-sm font-bold text-slate-700" x-text="habits.keringat"></span>
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-teal-500 transition-colors"
                                    :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-collapse
                                class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-30 py-2 overflow-hidden"
                                x-cloak>
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
                                <button @click="!isSelesai && (open = !open)" type="button"
                                    :class="isSelesai ? 'cursor-not-allowed opacity-60' : ''"
                                    class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3.5 flex justify-between items-center group">
                                    <span class="text-xs font-bold text-slate-700" x-text="habits.babKapan"></span>
                                    <svg class="w-3 h-3 text-slate-300" :class="open ? 'rotate-180' : ''" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-30 py-1"
                                    x-cloak>
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
                                <button @click="!isSelesai && (open = !open)" type="button"
                                    :class="isSelesai ? 'cursor-not-allowed opacity-60' : ''"
                                    class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3.5 flex justify-between items-center group">
                                    <span class="text-xs font-bold text-slate-700" x-text="habits.babBentuk"></span>
                                    <svg class="w-3 h-3 text-slate-300" :class="open ? 'rotate-180' : ''" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-30 py-1"
                                    x-cloak>
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
                                <button @click="!isSelesai && (open = !open)" type="button"
                                    :class="isSelesai ? 'cursor-not-allowed opacity-60' : ''"
                                    class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3.5 flex justify-between items-center group">
                                    <span class="text-xs font-bold text-slate-700" x-text="habits.bakFrekuensi"></span>
                                    <svg class="w-3 h-3 text-slate-300" :class="open ? 'rotate-180' : ''" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-30 py-1"
                                    x-cloak>
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
                                <button @click="!isSelesai && (open = !open)" type="button"
                                    :class="isSelesai ? 'cursor-not-allowed opacity-60' : ''"
                                    class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3.5 flex justify-between items-center group">
                                    <span class="text-xs font-bold text-slate-700" x-text="habits.bakWarna"></span>
                                    <svg class="w-3 h-3 text-slate-300" :class="open ? 'rotate-180' : ''" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-30 py-1"
                                    x-cloak>
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
                            <label class="text-[12px] font-semibold text-slate-400 uppercase tracking-widest">Skala Nyeri
                                (VAS)</label>
                            <span class="text-2xl font-black text-teal-600" x-text="evaluation.nyeri"></span>
                        </div>
                        <div class="flex gap-1.5">
                            @foreach (range(1, 5) as $val)
                                <button type="button" @click="!isSelesai && (evaluation.nyeri = {{ $val }})"
                                    :class="evaluation.nyeri === {{ $val }} ?
                                        'bg-teal-600 text-white border-teal-600 scale-110 z-10' :
                                        'bg-slate-50 text-slate-400 border-slate-100'"
                                    :disabled="isSelesai" :style="isSelesai ? 'cursor:not-allowed;opacity:0.6;' : ''"
                                    class="flex-1 h-10 rounded-lg border text-sm font-semibold transition-all flex items-center justify-center shadow-sm">
                                    {{ $val }}
                                </button>
                            @endforeach
                        </div>
                        <div
                            class="flex justify-between px-1 text-sm font-semibold text-slate-300 uppercase tracking-tighter">
                            <span>Tidak Nyeri</span>
                            <span>Nyeri Hebat</span>
                        </div>
                    </div>

                    <hr class="border-slate-300">

                    {{-- Improvement Percentage --}}
                    <div class="space-y-4">
                        <label class="text-[12px] font-semibold text-slate-400 uppercase tracking-widest ml-1">Tingkat
                            Perbaikan</label>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach (['0%', '25%', '50%', '75%', '100%'] as $pct)
                                <button type="button"
                                    @click="!isSelesai && (evaluation.perbaikan = '{{ $pct }}')"
                                    :class="evaluation.perbaikan === '{{ $pct }}' ?
                                        'bg-teal-800 text-white border-teal-800' :
                                        'bg-white text-slate-400 border-slate-200'"
                                    :disabled="isSelesai" :style="isSelesai ? 'cursor:not-allowed;opacity:0.6;' : ''"
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
                    <textarea name="goal_terapi" placeholder="Target jangka pendek/panjang..." :readonly="isSelesai"
                        :class="isSelesai ? 'bg-slate-50 text-slate-500 cursor-not-allowed resize-none' :
                            'bg-white focus:border-teal-500 resize-none'"
                        class="w-full border border-slate-200 rounded-2xl p-5 text-sm font-medium text-slate-700 h-32 outline-none shadow-sm">{{ old('goal_terapi', $rekamMedis->goal_terapi) }}</textarea>
                </div>
                <div class="space-y-2">
                    <label
                        class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1 border-l-2 border-teal-500 pl-3">Saran
                        / Rekomendasi</label>
                    <textarea name="saran_rekomendasi" placeholder="Latihan mandiri atau pantangan..." :readonly="isSelesai"
                        :class="isSelesai ? 'bg-slate-50 text-slate-500 cursor-not-allowed resize-none' :
                            'bg-white focus:border-teal-500 resize-none'"
                        class="w-full border border-slate-200 rounded-2xl p-5 text-sm font-medium text-slate-700 h-32 outline-none shadow-sm">{{ old('saran_rekomendasi', $rekamMedis->saran_rekomendasi) }}</textarea>
                </div>
                <div class="space-y-2">
                    <label
                        class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1 border-l-2 border-teal-500 pl-3">Catatan
                        Khusus</label>
                    <textarea name="catatan_khusus" placeholder="Catatan khusus ..." :readonly="isSelesai"
                        :class="isSelesai ? 'bg-slate-50 text-slate-500 cursor-not-allowed resize-none' :
                            'bg-white focus:border-teal-500 resize-none'"
                        class="w-full border border-slate-200 rounded-2xl p-5 text-sm font-medium text-slate-700 h-32 outline-none shadow-sm">{{ old('catatan_khusus', $rekamMedis->catatan_khusus) }}</textarea>
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
                            <img :src="photo.url" class="w-full h-full object-cover">
                            <button x-show="!isSelesai" type="button" @click="removePhoto(index)"
                                class="absolute top-1.5 right-1.5 bg-rose-500 text-white rounded-full p-1 shadow-md active:scale-90 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="3">
                                    <path d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <label x-show="!isSelesai" :class="isSelesai ? 'cursor-not-allowed pointer-events-none' : 'cursor-pointer block'"
                    class="block">
                    <div :class="isSelesai ? 'opacity-50' : 'hover:border-teal-500 active:bg-slate-100/50'"
                        class="bg-slate-50/50 border-2 border-dashed border-slate-200 rounded-3xl p-10 text-center space-y-3 transition-all">
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
                    <input type="file" x-ref="fileInput" name="foto[]" multiple accept="image/*" class="hidden"
                        :disabled="isSelesai" @change="!isSelesai && addPhotos($event)">
                </label>
            </div>

            {{-- 11. ACTIONS --}}
            <div class="pt-6 space-y-6">

                {{-- Session Completion Toggle (hidden when selesai) --}}
                <div x-show="!isSelesai" x-cloak
                    class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between group active:scale-[0.98] transition-all cursor-pointer"
                    @click="isDone = !isDone">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-colors"
                            :class="isDone ? 'bg-teal-100 text-teal-600' : 'bg-slate-100 text-slate-400'">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="space-y-0.5">
                            <h4 class="text-sm font-bold text-slate-800">Sesi Selesai?</h4>
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-tight"
                                x-text="isDone ? 'Sesi Akan Ditandai Selesai' : 'Centang Jika Sesi Berakhir'"></p>
                        </div>
                    </div>
                    <div class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all"
                        :class="isDone ? 'bg-teal-500 border-teal-500' : 'border-slate-200 bg-white'">
                        <svg x-show="isDone" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                {{-- Actual action fields --}}
                <input type="hidden" name="status_pasien_action" :value="isDone ? 'complete' : 'draft'">

                {{-- Action Buttons: hidden when selesai --}}
                <div x-show="!isSelesai" x-cloak class="space-y-4">
                    {{-- Selesaikan Sesi --}}
                    <button x-show="isDone" type="submit" @click="document.getElementById('action-type').value = 'complete'"
                        class="w-full py-5 bg-teal-800 text-white rounded-2xl text-base font-bold uppercase tracking-[0.2em] shadow-xl shadow-teal-900/10 active:scale-95 transition-all">
                        Selesaikan Sesi
                    </button>

                    {{-- Simpan Draft --}}
                    <button type="submit" @click="document.getElementById('action-type').value = 'draft'"
                        class="w-full py-4 bg-white border border-slate-200 text-slate-700 rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-sm active:scale-95 transition-all hover:bg-slate-50">
                        Simpan Draft
                    </button>

                    {{-- Batal --}}
                    <a href="{{ route('therapist.jadwal') }}"
                        class="block w-full py-4 text-slate-400 text-xs font-bold uppercase tracking-[0.2em] active:scale-95 transition-all hover:text-slate-700 text-center">
                        Batalkan
                    </a>
                </div>

                {{-- Read-only footer notice when selesai --}}
                <div x-show="isSelesai" x-cloak class="space-y-3">
                    <div
                        class="flex items-center justify-center gap-2 py-4 bg-slate-50 border border-slate-200 rounded-2xl">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sesi telah dikunci</p>
                    </div>
                    <a href="javascript:void(0)" onclick="window.history.back()"
                        class="block w-full py-4 bg-teal-800 text-white text-xs font-black uppercase tracking-[0.2em] rounded-2xl text-center shadow-lg shadow-teal-900/10 active:scale-95 transition-all">
                        Kembali ke halaman sebelumnya
                    </a>
                </div>

            </div>
        </form>


        <x-navigation.therapist-navbar active="jadwal" />

    </x-layouts.mobile-app>

    <style>
        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='3'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-position: right 1.25rem center;
            background-repeat: no-repeat;
            background-size: 1.15rem;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

@endsection
