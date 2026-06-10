@extends('components.layouts.app')

@section('title', 'Profil Kolaborasi')

@section('content')

<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32" x-data="{
    logoPreview: '{{ $logoUrl }}',
    triggerFileInput() {
        this.$refs.fileInput.click();
    },
    handleFileChange(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.logoPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
}">

    {{-- TOPBAR GLASSY --}}
<nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-100/80 px-6 py-4">
            <div class="flex items-center justify-between">

                {{-- Left: Navigation & Context --}}
                <div class="flex items-center gap-4">
                    {{-- Tombol Back/Menu dengan Hitbox Luas --}}
                    <a href="javascript:void(0)" onclick="window.history.back()" 
                    class="group flex items-center justify-center w-10 h-10 bg-white border border-slate-100 rounded-xl shadow-sm hover:bg-teal-50 transition-all active:scale-90">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>

                    <div class="flex flex-col">
                        {{-- Nama Cabang/Kolaborasi --}}
                        <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">
                            {{-- {{ $sessions[0]['kolaborasi'] ?? 'Rumah Terapi Anjali' }} --}}
                            ANJALI SADINA MULYO
                        </span>
                        <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none uppercase">
                            Profil Kolaborasi
                        </h1>
                    </div>
                </div>

                {{-- Right: Profile with Status Indicator --}}
                <div class="flex items-center gap-3">
                    <div class="relative">
                        {{-- Avatar dengan Ring Status --}}
                        <div class="w-10 h-10 rounded-xl border-2 border-white shadow-md p-0.5">
                            <img src="{{ asset('images/logo_anjali.jpg') }}"
                                class="w-full h-full rounded-[10px] object-cover bg-white">
                        </div>
                    </div>
                </div>

            </div>
        </nav>

    <div class="px-6 pt-6 space-y-6">

        {{-- SUCCESS NOTIFICATION --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:leave="transition ease-in duration-300"
                class="bg-teal-600 text-white rounded-2xl p-4 text-xs font-black uppercase tracking-widest text-center shadow-lg shadow-teal-700/20">
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="text-xs font-bold text-rose-600">• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin-cabang.kolaborasi.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- LOGO UPLOAD CARD --}}
            <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm flex flex-col items-center text-center space-y-4">
                <div class="relative group cursor-pointer" @click="triggerFileInput()">
                    <div class="w-24 h-24 rounded-[2.2rem] p-1 bg-white border border-slate-100 shadow-lg overflow-hidden relative">
                        <img :src="logoPreview" class="w-full h-full rounded-[1.8rem] object-cover">
                        <div class="absolute inset-0 bg-black/40 rounded-[1.8rem] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <input type="file" name="logo" x-ref="fileInput" class="hidden" accept="image/*" @change="handleFileChange">

                <div class="space-y-1">
                    <h3 class="text-sm font-black text-slate-800 leading-tight">Logo Kolaborasi</h3>
                    <p class="text-[10px] text-slate-400 font-medium">Klik gambar untuk mengubah logo cabang Anda.</p>
                </div>
            </div>

            {{-- PROFILE DETAILS CARD --}}
            <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em] border-b border-slate-50 pb-2">Informasi Cabang</h3>

                <div class="space-y-3">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Kolaborasi</label>
                    <input type="text" name="nama_kolaborasi" value="{{ old('nama_kolaborasi', $kolaborasi->nama_kolaborasi) }}" required
                        class="w-full px-4 py-3.5 bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner">
                </div>

                <div class="space-y-3">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Alamat Lengkap</label>
                    <textarea name="alamat_kolaborasi" required rows="3"
                        class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-xs font-medium text-slate-600 leading-relaxed focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none resize-none shadow-inner">{{ old('alamat_kolaborasi', $kolaborasi->alamat_kolaborasi) }}</textarea>
                </div>

                <div class="space-y-3">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Kota</label>
                    <input type="text" name="kota_kolaborasi" value="{{ old('kota_kolaborasi', $kolaborasi->kota_kolaborasi) }}" required
                        class="w-full px-4 py-3.5 bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner">
                </div>

                <div class="space-y-3">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomor Telepon</label>
                    <input type="text" name="no_telp_kolaborasi" value="{{ old('no_telp_kolaborasi', $kolaborasi->no_telp_kolaborasi) }}" required
                        class="w-full px-4 py-3.5 bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner">
                </div>

                <div class="space-y-3">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Email Cabang</label>
                    <input type="email" name="email_kolaborasi" value="{{ old('email_kolaborasi', $kolaborasi->email_kolaborasi) }}" required
                        class="w-full px-4 py-3.5 bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner">
                </div>
            </div>

            {{-- PRICING CARD --}}
            <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em] border-b border-slate-50 pb-2">Biaya Layanan</h3>

                <div class="space-y-3">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Homecare Surcharge</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-[10px] font-black text-slate-400">Rp</span>
                        <input type="number" name="homecare_harga" value="{{ old('homecare_harga', (int)$kolaborasi->homecare_harga) }}" required
                            class="w-full pl-9 pr-4 py-3.5 bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-800 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner">
                    </div>
                    <p class="text-[9px] text-slate-400 font-medium ml-1">Biaya tambahan untuk layanan homecare di cabang ini.</p>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="space-y-3 pt-2">
                <button type="submit"
                    class="w-full py-5 bg-teal-800 text-white rounded-[1.5rem] text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin-cabang.profile') }}"
                    class="block w-full py-4 bg-slate-200/50 text-slate-500 rounded-[1.5rem] text-sm font-black uppercase tracking-[0.2em] active:scale-95 transition-all text-center">
                    Batal
                </a>
            </div>

        </form>

    </div>

    {{-- BOTTOM NAVBAR --}}
    <x-navigation.admin-cabang-navbar active="profil-kolaborasi" />

</x-layouts.mobile-app>

<style>
    /* Prevent number arrows */
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
