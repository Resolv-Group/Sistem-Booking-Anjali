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
            <a href="{{ route('admin-cabang.patient.list') }}" class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'pasien' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="{{ $active === 'pasien' ? '2.5' : '2' }}">
                    <path d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.25 0 1 1-5.25 0 2.625 2.25 0 0 1 5.25 0Z" />
                </svg>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Pasien</span>
            </a>
            <a href="{{ route('admin-cabang.layanan.index') }}" class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'layanan' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="{{ $active === 'layanan' ? '2.5' : '2' }}">
                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Layanan</span>
            </a>
            <a href="{{ route('admin-cabang.kolaborasi.profile') }}" class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90 
                {{ $active === 'profil-kolaborasi' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="{{ $active === 'profil-kolaborasi' ? '2.5' : '2' }}">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Profil
                    Kolaborasi</span>
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

            <a href="{{ route('admin-cabang.dashboard') }}"
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'dashboard' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="{{ $active === 'dashboard' ? '2.5' : '2' }}">
                    <path
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Beranda</span>
            </a>

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

            <a href="{{ route('admin-cabang.therapist.list') }}"
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'terapis' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="{{ $active === 'terapis' ? '2.5' : '2' }}">
                    <path d="M15.477 12.89l1.515 8.526a.5.5 0 01-.81.47l-3.58-2.687a1 1 0 00-1.197 0l-3.586 2.686a.5.5 0 01-.81-.469l1.514-8.526"/><circle cx="12" cy="8" r="6"/>
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Terapis</span>
            </a>

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
