@extends('components.layouts.app')

@section('title', 'Tambah Layanan Baru')

@section('content')

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{
        namaLayanan: '',
        deskripsi: '',
        harga: {{ old('base_harga', 0) }},
        diskon: {{ old('diskon_persentase', 0) }},
        homeCare: {{ old('homecare_harga', 0) }},
        statusAktif: true,
    
        get totalHarga() {
            let potongan = (this.harga * this.diskon) / 100;
            return (parseInt(this.harga || 0) - potongan) + parseInt(this.homeCare || 0);
        },
    
        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        },

        get formattedHarga() {
            if (!this.harga || this.harga === 0) return '';
            return new Intl.NumberFormat('id-ID').format(this.harga);
        },
        set formattedHarga(value) {
            let clean = value.replace(/[^0-9]/g, '');
            this.harga = parseInt(clean) || 0;
        },
        get formattedHomeCare() {
            if (!this.homeCare || this.homeCare === 0) return '';
            return new Intl.NumberFormat('id-ID').format(this.homeCare);
        },
        set formattedHomeCare(value) {
            let clean = value.replace(/[^0-9]/g, '');
            this.homeCare = parseInt(clean) || 0;
        }
    }">

        {{-- 1. TOPBAR --}}
        <div
            class="px-6 py-5 flex justify-between items-center bg-white/90 backdrop-blur-xl sticky top-0 z-50 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin-global.layanan', $kolaborasi->id) }}"
                    class="p-1 -ml-1 text-slate-400 hover:text-teal-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-lg font-bold text-teal-800 uppercase tracking-widest leading-none">
                    {{ $kolaborasi->nama_kolaborasi }}</h1>
            </div>
        </div>

        <div class="px-6 pt-8 pb-32 space-y-8">

            {{-- 2. TITLE SECTION --}}
            <div class="space-y-3 px-1">
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Tambah Layanan</h2>
                <p class="text-sm font-medium text-slate-500 leading-relaxed">
                    Lengkapi informasi di bawah untuk menambahkan layanan baru ke katalog.
                </p>
            </div>

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="text-xs font-bold text-red-600">• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- 3. FORM --}}
            <form action="{{ route('admin-global.layanan.store', $kolaborasi->id) }}" method="POST" class="space-y-6">
                @csrf

                {{-- Card 1: Identitas Layanan --}}
                <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm space-y-6">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Nama
                            Layanan</label>
                        <input type="text" name="nama" x-model="namaLayanan" value="{{ old('nama') }}" required
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner"
                            placeholder="Contoh: Akupunktur">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Deskripsi
                            Singkat</label>
                        <textarea name="deskripsi" x-model="deskripsi" rows="6"
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-medium text-slate-600 leading-relaxed focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner resize-none"
                            placeholder="Jelaskan layanan ini secara singkat...">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                {{-- Card 2: Pricing --}}
                <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Harga Layanan</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-4 text-[10px] font-black text-slate-400">Rp.</span>
                                <input type="text" x-model="formattedHarga" required
                                    class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-xl text-sm font-black text-slate-800 focus:ring-2 focus:ring-teal-500/20">
                                <input type="hidden" name="base_harga" :value="harga">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Diskon</label>
                            <div class="relative flex items-center">
                                <input type="number" name="diskon_persentase" x-model="diskon"
                                    value="{{ old('diskon_persentase', 0) }}"
                                    class="w-full pl-4 pr-10 py-3 bg-slate-50 border-none rounded-xl text-sm font-black text-slate-800 text-right focus:ring-2 focus:ring-teal-500/20">
                                <span class="absolute right-4 text-[11px] font-black text-slate-400">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-50">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Harga Home Care (Opsional)</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-[11px] font-bold text-slate-300">Rp</span>
                            <input type="text" x-model="formattedHomeCare"
                                class="w-full pl-12 pr-4 py-4 bg-slate-50 border-none rounded-2xl text-sm font-black text-slate-800 focus:ring-2 focus:ring-teal-500/20">
                            <input type="hidden" name="homecare_harga" :value="homeCare">
                        </div>
                    </div>

                    <div class="p-5 bg-teal-50/50 rounded-2xl border border-teal-100/50 space-y-1">
                        <p class="text-[10px] font-black text-teal-600 uppercase tracking-widest">Total Harga Layanan</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-xs font-bold text-teal-500">Rp</span>
                            <span class="text-2xl font-black text-teal-800 tracking-tighter"
                                x-text="formatRupiah(totalHarga)"></span>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Switch Status --}}
                <div class="bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm">
                    <div class="flex items-center justify-between px-3">
                        <span class="text-sm font-bold text-slate-700">Aktifkan layanan</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="statusAktif" class="sr-only peer">
                            <div
                                class="w-12 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-[20px] after:w-[20px] after:transition-all peer-checked:bg-teal-700">
                            </div>
                        </label>
                    </div>
                    {{-- Hidden input to send actual status value --}}
                    <input type="hidden" name="status" :value="statusAktif ? 'Tersedia' : 'Tidak Tersedia'">
                </div>

                {{-- 4. FOOTER ACTIONS --}}
                <div class="space-y-3 pt-4">
                    <button type="submit"
                        class="w-full py-5 bg-teal-800 text-white rounded-[1.5rem] text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                        Tambah Layanan
                    </button>
                    <a href="{{ route('admin-global.layanan', $kolaborasi->id) }}"
                        class="block w-full py-4 bg-slate-200/50 text-slate-500 rounded-[1.5rem] text-sm font-black uppercase tracking-[0.2em] active:scale-95 transition-all text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- BOTTOM NAVBAR --}}
        <x-navigation.admin-global-navbar active="cabang" />

    </x-layouts.mobile-app>

    <style>
        /* Prevent number arrows for premium look */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>

@endsection
