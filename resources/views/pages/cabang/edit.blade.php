@extends('components.layouts.app')

@section('title', 'Edit Cabang')

@section('content')
    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen">

        {{-- HEADER --}}
        <div class="sticky top-0 z-50 bg-white/85 backdrop-blur-xl border-b border-slate-100/80 shadow-sm">
            <div class="h-1 w-full bg-gradient-to-r from-teal-500 via-teal-700 to-emerald-500"></div>
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="javascript:void(0)" onclick="window.history.back()" class="p-2 -ml-2 text-slate-400 hover:text-teal-600 hover:bg-slate-50 rounded-xl active:scale-95 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">
                            {{ $kolaborasi->nama_kolaborasi }}
                        </span>
                        <h1 class="text-xs font-black text-slate-800 uppercase tracking-wider leading-none">
                            Pengaturan Cabang & Biaya
                        </h1>
                    </div>
                </div>
                {{-- Right Slot: Simple Decorative Brand Icon --}}
                <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center text-teal-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- FORM BODY --}}
        <div class="px-6 py-8 space-y-6 pb-32">

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-600 text-sm font-semibold space-y-1 animate-in fade-in">
                    @foreach ($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin-global.cabang.update', $kolaborasi->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Card Section --}}
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm space-y-5">

                    {{-- Nama Kolaborasi --}}
                    <div class="space-y-2">
                        <label for="nama_kolaborasi" class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            Nama Cabang/Kolaborasi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nama_kolaborasi" name="nama_kolaborasi"
                               value="{{ old('nama_kolaborasi', $kolaborasi->nama_kolaborasi) }}"
                               required
                               class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-100 focus:bg-white transition-all outline-none">
                    </div>

                    {{-- Alamat --}}
                    <div class="space-y-2">
                        <label for="alamat_kolaborasi" class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            Alamat Lengkap
                        </label>
                        <textarea id="alamat_kolaborasi" name="alamat_kolaborasi" rows="3"
                                  class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-100 focus:bg-white transition-all outline-none resize-none">{{ old('alamat_kolaborasi', $kolaborasi->alamat_kolaborasi) }}</textarea>
                    </div>

                    {{-- Kota --}}
                    <div class="space-y-2">
                        <label for="kota_kolaborasi" class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            Kota
                        </label>
                        <input type="text" id="kota_kolaborasi" name="kota_kolaborasi"
                               value="{{ old('kota_kolaborasi', $kolaborasi->kota_kolaborasi) }}"
                               class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-100 focus:bg-white transition-all outline-none">
                    </div>

                    {{-- No Telp --}}
                    <div class="space-y-2">
                        <label for="no_telp_kolaborasi" class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            No. Telepon / WhatsApp
                        </label>
                        <input type="text" id="no_telp_kolaborasi" name="no_telp_kolaborasi"
                               value="{{ old('no_telp_kolaborasi', $kolaborasi->no_telp_kolaborasi) }}"
                               class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-100 focus:bg-white transition-all outline-none">
                    </div>

                    {{-- Email --}}
                    <div class="space-y-2">
                        <label for="email_kolaborasi" class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            Alamat Email
                        </label>
                        <input type="email" id="email_kolaborasi" name="email_kolaborasi"
                               value="{{ old('email_kolaborasi', $kolaborasi->email_kolaborasi) }}"
                               class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-100 focus:bg-white transition-all outline-none">
                    </div>

                </div>

                {{-- Biaya Homecare Card --}}
                <div class="bg-blue-50/50 p-6 rounded-[2rem] border border-blue-100/60 shadow-sm space-y-4"
                     x-data="{ 
                         rawValue: '{{ old('homecare_harga', $kolaborasi->homecare_harga ? intval($kolaborasi->homecare_harga) : 0) }}',
                         get formattedValue() {
                             if (!this.rawValue || this.rawValue === '0') return '';
                             return new Intl.NumberFormat('id-ID').format(this.rawValue);
                         },
                         setFormattedValue(val) {
                             let clean = val.replace(/[^0-9]/g, '');
                             this.rawValue = clean;
                         }
                     }">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-blue-700 uppercase tracking-widest">Biaya Layanan Home Care</h4>
                            <p class="text-[10px] text-slate-400 font-semibold uppercase mt-0.5">Flat rate per booking</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="homecare_harga_display" class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            Tarif Homecare (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">Rp</span>
                            <input type="text" id="homecare_harga_display"
                                   :value="formattedValue"
                                   @input="setFormattedValue($event.target.value)"
                                   placeholder="0" required
                                   class="w-full pl-12 pr-5 py-4 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-100 focus:border-blue-300 transition-all outline-none">
                            <input type="hidden" name="homecare_harga" :value="rawValue">
                        </div>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="pt-4">
                    <button type="submit"
                            class="w-full py-5 bg-slate-900 text-white rounded-2xl text-base font-bold uppercase tracking-[0.2em] shadow-xl shadow-slate-900/10 active:scale-95 transition-all">
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>

    </x-layouts.mobile-app>
@endsection
