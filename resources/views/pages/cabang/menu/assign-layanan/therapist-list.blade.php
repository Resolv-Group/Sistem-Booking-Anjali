@extends('components.layouts.app')

@section('title', 'Manajemen Layanan')

@section('content')

    <script>
        window.TherapistData = @json($therapists);
    </script>

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{
        search: '',
        therapists: window.TherapistData,
        get filteredTherapists() {
            if (!this.search) return this.therapists;
            return this.therapists.filter(t =>
                t.name.toLowerCase().includes(this.search.toLowerCase())
            );
        }
    }">

        {{-- 1. TOPBAR --}}


        <x-ui.topbar title="Rumah Terapi Anjali">
            <x-slot:left>
                <a href="{{ route('admin-global.cabang.menu', $kolaborasiId) }}"
                    class="p-1 -ml-1 text-slate-400 hover:text-teal-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            </x-slot:left>

            <x-slot:right>
                <div class="w-10 h-10 rounded-xl border-2 border-orange-100 p-0.5 bg-white">
                    <img src="https://i.pravatar.cc/100?u=admin" class="w-full h-full rounded-lg object-cover">
                </div>
            </x-slot:right>
        </x-ui.topbar>

        <div class="px-6 pt-8 pb-32 space-y-8">
            {{-- 2. TITLE --}}
            <div class="space-y-3">
                <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none text-left">Manajemen <br> Layanan
                </h2>
                <p class="text-sm font-medium text-slate-500 leading-relaxed text-left">
                    Silakan pilih terapis di bawah ini untuk melihat dan mengelola jadwal layanan yang tersedia.
                </p>
            </div>

            {{-- 3. SEARCH --}}
            <div class="relative group">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-teal-500 transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" x-model="search" placeholder="Cari nama spesialis..."
                    class="w-full pl-12 pr-4 py-4 bg-slate-100 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none">
            </div>

            {{-- 4. THERAPIST CARDS --}}
            <div class="space-y-4">
                <template x-for="t in filteredTherapists" :key="t.id">
                    <div class="bg-white rounded-[1.5rem] p-6 border border-slate-100 shadow-sm space-y-5">

                        <div class="flex items-center gap-5">
                            <div
                                class="w-16 h-16 rounded-2xl bg-slate-100 border-2 border-white shadow-md overflow-hidden shrink-0">
                                <img :src="t.image" class="w-full h-full object-cover">
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-lg font-black text-slate-800 leading-none" x-text="t.name"></h4>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest" x-text="t.role">
                                </p>
                                <div class="flex items-center gap-1 text-amber-400 pt-0.5">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="text-xs font-black text-slate-700" x-text="t.rating"></span>
                                    <span class="text-[10px] font-bold text-slate-300 uppercase ml-0.5"
                                        x-text="`(${t.reviews} Ulasan)`"></span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-teal-600 shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <span class="text-xs font-black text-slate-600 uppercase tracking-widest"
                                x-text="`Mengelola ${t.count} Layanan`"></span>
                        </div>

                        {{-- Update URL dinamis menggunakan JavaScript agar ID sesuai --}}
                        <a :href="'{{ route('admin-global.assign-layanan', ['id_kolaborasi' => $kolaborasiId, 'id_karyawan' => 'TARGET_ID']) }}'
                        .replace('TARGET_ID', t.id)"
                            class="block w-full py-4 bg-[#005F5C] text-white text-center rounded-xl text-sm font-black uppercase tracking-[0.2em] shadow-lg shadow-teal-900/20 active:scale-95 transition-all">
                            Kelola Layanan
                        </a>
                    </div>
                </template>

                {{-- Empty State jika hasil pencarian tidak ada --}}
                <div x-show="filteredTherapists.length === 0" class="text-center py-10">
                    <p class="text-slate-400 font-bold">Terapis tidak ditemukan.</p>
                </div>
            </div>
        </div>
        </div>

        <x-navigation.admin-global-navbar active="cabang" />
    </x-layouts.mobile-app>
@endsection
