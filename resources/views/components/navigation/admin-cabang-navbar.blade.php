@props([
    'active' => 'dashboard',
])

<div x-data="{ open: false }" class="fixed bottom-0 left-1/2 z-[100] w-full max-w-[430px] -translate-x-1/2">

    {{-- Menu Overlay --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-10"
        class="absolute bottom-24 left-6 right-6 bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 p-8 z-[101]"
        x-cloak>
        <div class="grid grid-cols-3 gap-6">
            <a href="#" class="flex flex-col items-center gap-3 group">
                <div
                    class="w-14 h-14 rounded-2xl bg-teal-50 flex items-center justify-center text-2xl group-active:scale-90 transition-all shadow-sm">
                    💰</div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Keuangan</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-3 group">
                <div
                    class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center text-2xl group-active:scale-90 transition-all shadow-sm">
                    📊</div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Laporan</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-3 group">
                <div
                    class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center text-2xl group-active:scale-90 transition-all shadow-sm">
                    🏢</div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Cabang</span>
            </a>
        </div>

        <button @click="open = false"
            class="mt-8 w-full py-4 bg-slate-50 text-slate-400 rounded-2xl text-xs font-bold uppercase tracking-widest active:bg-slate-100">
            Tutup Menu
        </button>
    </div>

    {{-- Backdrop --}}
    <div x-show="open" @click="open = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        class="fixed inset-0 bg-teal-900/20 backdrop-blur-sm -z-10" x-cloak></div>

    {{-- Main Navbar --}}
    <div class="bg-white border-t border-slate-100 px-4 pb-6 pt-2 shadow-[0_-10px_40px_rgba(0,0,0,0.04)]">
        <div class="grid grid-cols-5 items-center">

            <button
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'dashboard' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="{{ $active === 'dashboard' ? '2.5' : '2' }}">
                    <path
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Beranda</span>
            </button>

            <a href="{{ route('admin-cabang.booking.list') }}"
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'booking' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="{{ $active === 'booking' ? '2.5' : '2' }}">
                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Booking</span>
            </a>

            {{-- Central Menu Button --}}
            <div class="flex flex-col items-center -mt-8 relative z-50">
                <button @click="open = !open"
                    class="w-14 h-14 bg-teal-800 text-white rounded-2xl shadow-xl shadow-teal-900/30 flex items-center justify-center transition-all active:scale-90"
                    :class="open ? 'bg-orange-500 shadow-orange-500/30' : ''">
                    <svg x-show="!open" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <svg x-show="open" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="3" x-cloak>
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <span class="text-[10px] font-black text-teal-800 uppercase tracking-widest mt-2">Menu</span>
            </div>

            <button
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'terapis' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="{{ $active === 'terapis' ? '2.5' : '2' }}">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Terapis</span>
            </button>

            {{-- <button
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'pasien' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="{{ $active === 'pasien' ? '2.5' : '2' }}">
                    <path
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Pasien</span>
            </button> --}}

            <a href="{{ route('admin-cabang.profile') }}"
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'profile' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="{{ $active === 'profile' ? '2.5' : '2' }}">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Profil</span>
            </a>

        </div>
    </div>
</div>
