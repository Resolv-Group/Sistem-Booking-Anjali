@extends('components.layouts.app')

@section('title', 'Pemilihan Layanan')

@section('content')

    <script>
        // Injects your real database collection into a global browser variable
        window.LayananData = @json($layanans);
        // Assuming your controller passes an array of already active IDs for this therapist, e.g., [1, 2]
        window.ActiveLayananIds = @json($activeLayananIds ?? []);
    </script>

    <form action="{{ route('admin-global.assign-layanan.store', [$kolaborasi, $karyawan]) }}" method="POST">
        @csrf

        <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{
            search: '',
            // Populate selected ids from the database defaults
            selectedIds: window.ActiveLayananIds,
            // Replace hardcoded mock data with the real script variable
            allServices: window.LayananData,
            toggle(id) {
                if (this.selectedIds.includes(id)) {
                    this.selectedIds = this.selectedIds.filter(i => i !== id);
                } else {
                    this.selectedIds.push(id);
                }
            },
            get filteredServices() {
                // Modified to safely check name based on your likely Eloquent column names
                return this.allServices.filter(s =>
                    (s.nama_layanan || s.name || '').toLowerCase().includes(this.search.toLowerCase())
                );
            }
        }">

            {{-- 1. TOPBAR --}}
            <div
                class="px-6 py-5 flex justify-between items-center bg-white/90 backdrop-blur-xl sticky top-0 z-50 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin-global.cabang.menu', $kolaborasi->id) }}"
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

                {{-- SUCCESS NOTIFICATION --}}
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-4"
                        class="bg-teal-500 text-white rounded-2xl p-4 text-sm font-bold text-center shadow-lg">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- 2. TITLE --}}
                <div class="space-y-3">
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none text-left">Manajemen <br>
                        Layanan</h2>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed text-left">
                        Silakan tentukan layanan apa saja yang dikuasai dan ditangani oleh terapis terpilih.
                    </p>
                </div>

                {{-- 3. THERAPIST MINI PROFILE --}}
                <div class="bg-white rounded-[1.5rem] p-5 border border-slate-100 shadow-sm flex items-center gap-5">
                    <div class="w-20 h-20 rounded-2xl border-4 border-slate-50 shadow-md overflow-hidden shrink-0">
                        <img src="{{ $therapist->fotoUrl() ?: 'https://i.pravatar.cc/150?u=' . $therapist->id }}"
                            class="w-full h-full object-cover">
                    </div>
                    <div class="space-y-1 text-left">
                        <h4 class="text-xl font-black text-slate-800 tracking-tight leading-none">
                            {{ $therapist->nama_karyawan ?? 'Terapis' }}</h4>
                        <p class="text-xs font-semibold text-teal-600 uppercase tracking-widest">
                            {{ $therapist->peran ?? 'Spesialis' }}</p>
                    </div>
                </div>

                {{-- 4. MANAJEMEN SECTION --}}
                <div class="space-y-6 pt-4 border-t border-slate-100">
                    <div class="flex justify-between items-end px-1">
                        <div>
                            <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.25em]">Manajemen</span>
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-widest mt-1">Kelola Layanan
                            </h3>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                            x-text="`${selectedIds.length} dari ${allServices.length} terpilih`"></span>
                    </div>

                    {{-- Inner Search --}}
                    <div class="relative group">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-300" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" x-model="search" placeholder="Cari nama layanan..."
                            class="w-full pl-12 pr-4 py-4 bg-slate-100/50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:bg-white transition-all outline-none">
                    </div>

                    {{-- 2. HIDDEN INPUTS HOLDER FOR FORMS --}}
                    <template x-for="id in selectedIds" :key="'input-' + id">
                        <input type="hidden" name="layanan_ids[]" :value="id">
                    </template>

                    {{-- Services Grid --}}
                    <div class="space-y-4">
                        <template x-for="s in filteredServices" :key="s.id">
                            <button type="button" @click="toggle(s.id)"
                                :class="selectedIds.includes(s.id) ? 'border-teal-500 bg-teal-50/20 ring-1 ring-teal-500' :
                                    'border-slate-100 bg-white'"
                                class="w-full text-left p-5 border-2 rounded-[1.5rem] transition-all relative group shadow-sm">

                                <div class="flex gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 shrink-0 border border-slate-100 transition-colors"
                                        :class="selectedIds.includes(s.id) &&
                                            'bg-white text-teal-600 border-teal-100 shadow-inner'">
                                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0 pr-6">
                                        <h4 class="text-base font-black text-slate-800 leading-tight"
                                            x-text="s.nama_layanan || s.name"></h4>
                                        <p class="text-xs font-medium text-slate-400 mt-1 leading-relaxed line-clamp-2"
                                            x-text="s.deskripsi || s.desc || '- '"></p>

                                        <div class="flex items-center gap-3 mt-3">
                                            <div class="flex items-center gap-1 text-slate-400">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-[10px] font-black uppercase tracking-tighter"
                                                    x-text="s.durasi || '60 Min'"></span>
                                            </div>
                                            <p class="text-xs font-black text-teal-700" x-text="`Rp ${s.harga}`"></p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Checkbox Indicator --}}
                                <div class="absolute top-4 right-4">
                                    <div class="w-6 h-6 rounded-full border-2 transition-all flex items-center justify-center"
                                        :class="selectedIds.includes(s.id) ? 'bg-teal-500 border-teal-500 shadow-lg' :
                                            'bg-white border-slate-200'">
                                        <svg x-show="selectedIds.includes(s.id)" class="w-4 h-4 text-white" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                            <path d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- 5. ACTIONS --}}
                <div class="space-y-3 pt-6 border-t border-slate-50">
                    <button type="submit"
                        class="w-full py-5 bg-[#005F5C] text-white rounded-[1.5rem] text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                        Simpan Perubahan Layanan
                    </button>
                    <button type="button" @click="window.history.back()"
                        class="w-full py-4 bg-slate-200/50 text-slate-500 rounded-[1.5rem] text-sm font-black uppercase tracking-[0.2em] active:scale-95 transition-all">
                        Batal
                    </button>
                </div>
            </div>

        </x-layouts.mobile-app>
    </form>
@endsection
