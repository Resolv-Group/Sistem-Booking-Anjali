@props([
    'active' => 'dashboard',
])

<div x-data="{ open: false }" class="fixed bottom-0 left-1/2 z-[100] w-full max-w-[430px] -translate-x-1/2">

    {{-- Main Navbar --}}
    <div class="bg-white border-t border-slate-100 px-4 pb-6 pt-2 shadow-[0_-10px_40px_rgba(0,0,0,0.04)]">
        <div class="grid grid-cols-5 items-center">

            <a href="{{ route('patient.dashboard') }}"
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'dashboard' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="{{ $active === 'dashboard' ? '2.5' : '2' }}">
                    <path
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Beranda</span>
            </a>

            <a href="#"
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'booking' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="{{ $active === 'booking' ? '2.5' : '2' }}">
                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Jadwal Saya</span>
            </a>

            {{-- Central Menu Button --}}
            <div class="flex flex-col items-center -mt-8 relative z-50">
                <a href="{{ route('patient.booking.index') }}"
                    class="w-14 h-14 bg-teal-800 text-white rounded-2xl shadow-xl shadow-teal-900/30 flex items-center justify-center transition-all active:scale-90">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                </a>
                <span class="text-[10px] font-black text-teal-800 uppercase tracking-widest mt-2">Booking</span>
            </div>

            <a href="{{ route('patient.therapist') }}"
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'therapists' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="{{ $active === 'therapists' ? '2.5' : '2' }}">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Terapis</span>
            </a>

            <a href="{{ route('patient.profile') }}"
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
