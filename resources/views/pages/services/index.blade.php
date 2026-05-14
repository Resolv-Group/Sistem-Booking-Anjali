@extends('components.layouts.app')

@section('title', 'Pilih Layanan')

@section('content')

<x-layouts.mobile-app class="bg-slate-50 min-h-screen" 
    x-data="{ 
        search: '',
        selectedServices: [],
        categories: ['Semua', 'Akupunktur', 'Bekam', 'Fisioterapi', 'Refleksi'],
        activeCategory: 'Semua',
        services: [
            { id: 1, name: 'Akupunktur Medis', price: 350000, time: '60 Min', cat: 'Akupunktur', desc: 'Terapi jarum untuk keseimbangan energi.' },
            { id: 2, name: 'Bekam Steril', price: 200000, time: '45 Min', cat: 'Bekam', desc: 'Detoksifikasi darah kotor dengan standar medis.' },
            { id: 3, name: 'Fisioterapi Saraf', price: 450000, time: '90 Min', cat: 'Fisioterapi', desc: 'Pemulihan fungsi gerak dan saraf.' },
            { id: 4, name: 'Refleksi Kaki', price: 150000, time: '60 Min', cat: 'Refleksi', desc: 'Pijat titik saraf untuk relaksasi total.' },
            { id: 5, name: 'Bekam + Akupunktur', price: 500000, time: '120 Min', cat: 'Akupunktur', desc: 'Paket kombinasi untuk pemulihan maksimal.' },
        ],

        toggleService(service) {
            if (this.selectedServices.find(s => s.id === service.id)) {
                this.selectedServices = this.selectedServices.filter(s => s.id !== service.id);
            } else {
                this.selectedServices.push(service);
            }
        },

        get filteredServices() {
            return this.services.filter(s => {
                const matchesSearch = s.name.toLowerCase().includes(this.search.toLowerCase());
                const matchesCat = this.activeCategory === 'Semua' || s.cat === this.activeCategory;
                return matchesSearch && matchesCat;
            });
        },

        get totalPrice() {
            return this.selectedServices.reduce((sum, s) => sum + s.price, 0);
        },

        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }
    }">

    {{-- 1. HEADER & SEARCH --}}
    <div class="px-6 py-6 bg-white border-b border-slate-100 rounded-b-[2.5rem] shadow-sm sticky top-0 z-50">
        <div class="flex items-center gap-4 mb-6">
            <a href="#" class="p-2 -ml-2 text-slate-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-2xl font-semibold text-slate-800 tracking-tight">Pilih Layanan</h2>
        </div>

        {{-- Search Input --}}
        <div class="relative group mb-6">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-teal-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" x-model="search" placeholder="Cari layanan kesehatan..." 
                class="w-full pl-12 pr-5 py-4 bg-slate-50 border-none rounded-2xl text-base font-semibold focus:ring-4 focus:ring-teal-500/10 transition-all outline-none">
        </div>

        {{-- Swipeable Category Carousel --}}
        <div class="flex gap-3 overflow-x-auto no-scrollbar snap-x snap-mandatory -mx-6 px-6">
            <template x-for="cat in categories" :key="cat">
                <button @click="activeCategory = cat"
                    :class="activeCategory === cat ? 'bg-teal-800 text-white shadow-lg shadow-teal-900/20' : 'bg-slate-50 text-slate-500 border border-slate-100'"
                    class="snap-start shrink-0 px-8 py-2.5 rounded-xl text-sm font-semibold uppercase tracking-widest transition-all"
                    x-text="cat">
                </button>
            </template>
        </div>
    </div>

    {{-- 2. SERVICE SELECTOR LIST --}}
    <div class="px-6 py-8 space-y-4 pb-48">
        <template x-for="service in filteredServices" :key="service.id">
            <button @click="toggleService(service)" 
                :class="selectedServices.find(s => s.id === service.id) ? 'border-teal-500 bg-teal-50/30 ring-1 ring-teal-500' : 'border-slate-200 bg-white'"
                class="w-full text-left p-5 border-2 rounded-[2rem] transition-all relative group shadow-sm">
                
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1 pr-8">
                        <h4 class="text-lg font-semibold text-slate-800" x-text="service.name"></h4>
                        <div class="flex items-center gap-3 mt-1 text-slate-400">
                            <span class="text-xs font-semibold uppercase tracking-tighter" x-text="service.time"></span>
                            <div class="w-1 h-1 bg-slate-200 rounded-full"></div>
                            <span class="text-xs font-semibold uppercase tracking-tighter" x-text="service.cat"></span>
                        </div>
                    </div>
                    {{-- Price Display --}}
                    <div class="text-right">
                        <p class="text-base font-semibold text-teal-700" x-text="'Rp' + formatRupiah(service.price)"></p>
                    </div>
                </div>
                
                <p class="text-sm text-slate-500 font-medium leading-relaxed mt-2" x-text="service.desc"></p>

                {{-- Selection Indicator (Tick) --}}
                <div x-show="selectedServices.find(s => s.id === service.id)" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-50"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="absolute top-4 right-4">
                    <div class="w-7 h-7 bg-teal-500 rounded-full flex items-center justify-center text-white shadow-lg border-2 border-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </button>
        </template>

        {{-- Empty Search State --}}
        <div x-show="filteredServices.length === 0" class="py-20 text-center space-y-4">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <p class="text-slate-400 font-semibold uppercase tracking-widest text-sm">Layanan tidak ditemukan</p>
        </div>
    </div>

    {{-- 3. FLOATING GLASS SUMMARY BAR --}}
    <div x-show="selectedServices.length > 0"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-20"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        class="fixed bottom-24 left-6 right-6 z-50">
        
        <div class="bg-white/80 backdrop-blur-xl border border-teal-100 shadow-[0_20px_50px_rgba(0,0,0,0.15)] rounded-[2rem] p-5 flex items-center justify-between">
            <div class="pl-2">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-[0.2em] mb-1">Total Terpilih</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-xl font-semibold text-slate-800" x-text="'Rp' + formatRupiah(totalPrice)"></span>
                    <span class="text-xs font-bold text-teal-600" x-text="selectedServices.length + ' Item'"></span>
                </div>
            </div>
            
            <a href="#" 
                class="px-8 py-4 bg-teal-800 text-white rounded-2xl text-sm font-semibold uppercase tracking-widest shadow-lg shadow-teal-900/20 active:scale-95 transition-all">
                Lanjut
            </a>
        </div>
    </div>

    <x-navigation.guest-navbar active="layanan" />
</x-layouts.mobile-app>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

@endsection