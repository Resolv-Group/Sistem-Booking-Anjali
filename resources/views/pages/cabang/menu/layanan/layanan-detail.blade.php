@extends('components.layouts.app')

@section('title', 'Detail Layanan Klinik')

@section('content')

<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{ 
    namaLayanan: 'Akupunktur',
    deskripsi: 'Metode stimulasi titik saraf untuk menyeimbangkan energi tubuh, mengurangi stres, dan meningkatkan kebugaran fisik serta mental secara menyeluruh.',
    durasi: 60,
    harga: 350000,
    diskon: 10,
    homeCare: 150000,
    statusAktif: true,

    get totalHarga() {
        let potongan = (this.harga * this.diskon) / 100;
        return (this.harga - potongan) + parseInt(this.homeCare || 0);
    },

    formatRupiah(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }
}">

    {{-- 1. TOPBAR --}}
    <div class="px-6 py-5 flex justify-between items-center bg-white/90 backdrop-blur-xl sticky top-0 z-50 border-b border-slate-100">
        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() }}" class="p-1 -ml-1 text-slate-400 hover:text-teal-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-sm font-bold text-teal-800 uppercase tracking-widest leading-none">Rumah Terapi Anjali</h1>
        </div>
        <img src="https://i.pravatar.cc/100?u=admin" class="w-10 h-10 rounded-xl border-2 border-orange-100 p-0.5 object-cover">
    </div>

    <div class="px-6 pt-8 pb-32 space-y-8">

        {{-- 2. TITLE SECTION --}}
        <div class="space-y-3 px-1">
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Detail Layanan</h2>
            <p class="text-sm font-medium text-slate-500 leading-relaxed">
                Lengkapi informasi di bawah untuk memperbarui katalog layanan Anda.
            </p>
        </div>

        {{-- 3. FORM GROUPS --}}
        <form action="#" method="POST" class="space-y-6">
            @csrf

            {{-- Card 1: Identitas Layanan --}}
            <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm space-y-6">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Nama Layanan</label>
                    <input type="text" x-model="namaLayanan" 
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner">
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Deskripsi Singkat</label>
                    <textarea x-model="deskripsi" rows="6" 
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-medium text-slate-600 leading-relaxed focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner resize-none"></textarea>
                </div>
            </div>

            {{-- Card 2: Durasi --}}
            <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div class="space-y-2 flex-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Durasi Sesi (Menit)</label>
                        <div class="flex items-center gap-3">
                            <input type="number" x-model="durasi" 
                                class="w-24 px-4 py-3 bg-slate-100 border-none rounded-xl text-center text-sm font-black text-slate-800 focus:ring-2 focus:ring-teal-500/20 outline-none">
                            <span class="text-sm font-bold text-slate-400 uppercase tracking-widest">Menit</span>
                        </div>
                    </div>
                    <div class="p-3 bg-teal-50 text-teal-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            {{-- Card 3: Pricing --}}
            <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Harga Layanan</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-[10px] font-black text-slate-400">Rp.</span>
                            <input type="number" x-model="harga" class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-xl text-sm font-black text-slate-800 focus:ring-2 focus:ring-teal-500/20">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Diskon</label>
                        <div class="relative flex items-center">
                            <input type="number" x-model="diskon" class="w-full pl-4 pr-10 py-3 bg-slate-50 border-none rounded-xl text-sm font-black text-slate-800 text-right focus:ring-2 focus:ring-teal-500/20">
                            <span class="absolute right-4 text-[11px] font-black text-slate-400">%</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-50">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Harga Home Care (Opsional)</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-4 text-[11px] font-bold text-slate-300">Rp</span>
                        <input type="number" x-model="homeCare" class="w-full pl-12 pr-4 py-4 bg-slate-50 border-none rounded-2xl text-sm font-black text-slate-800 focus:ring-2 focus:ring-teal-500/20">
                    </div>
                </div>

                <div class="p-5 bg-teal-50/50 rounded-2xl border border-teal-100/50 space-y-1">
                    <p class="text-[10px] font-black text-teal-600 uppercase tracking-widest">Total Harga Layanan</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-xs font-bold text-teal-500">Rp</span>
                        <span class="text-2xl font-black text-teal-800 tracking-tighter" x-text="formatRupiah(totalHarga)"></span>
                    </div>
                </div>
            </div>

            {{-- Card 4: Switch Status --}}
            <div class="bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between px-3">
                    <span class="text-sm font-bold text-slate-700">Aktifkan layanan</span>
                    {{-- Native-like Switch --}}
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="statusAktif" class="sr-only peer">
                        <div class="w-12 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-[20px] after:w-[20px] after:transition-all peer-checked:bg-teal-700"></div>
                    </label>
                </div>
            </div>

            {{-- 4. FOOTER ACTIONS --}}
            <div class="space-y-3 pt-4">
                <button type="submit" class="w-full py-5 bg-teal-800 text-white rounded-[1.5rem] text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                    Simpan Layanan
                </button>
                <button type="button" @click="window.history.back()" class="w-full py-4 bg-slate-200/50 text-slate-500 rounded-[1.5rem] text-sm font-black uppercase tracking-[0.2em] active:scale-95 transition-all">
                    Batal
                </button>
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