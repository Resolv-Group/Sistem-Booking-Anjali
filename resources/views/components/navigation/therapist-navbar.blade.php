@props([
    'active' => 'dashboard',
])

<div class="fixed bottom-0 left-1/2 z-[100] w-full max-w-[430px] -translate-x-1/2">
    
    {{-- Menu Overlay --}}
    {{-- <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-10"
         class="absolute bottom-24 left-6 right-6 bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 p-8 z-[101]"
         x-cloak>
        <div class="grid grid-cols-3 gap-6">
            <a href="#" class="flex flex-col items-center gap-3 group">
                <div class="w-14 h-14 rounded-2xl bg-teal-50 flex items-center justify-center text-2xl group-active:scale-90 transition-all shadow-sm">💆‍♂️</div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Layanan</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-3 group">
                <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center text-2xl group-active:scale-90 transition-all shadow-sm">👥</div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Pasien</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-3 group">
                <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center text-2xl group-active:scale-90 transition-all shadow-sm">⚙️</div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Setelan</span>
            </a>
        </div>
        
        <button @click="open = false" class="mt-8 w-full py-4 bg-slate-50 text-slate-400 rounded-2xl text-xs font-bold uppercase tracking-widest active:bg-slate-100">
            Tutup Menu
        </button>
    </div> --}}

    {{-- Backdrop --}}
    {{-- <div x-show="open" @click="open = false" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-teal-900/20 backdrop-blur-sm -z-10" x-cloak></div> --}}

    {{-- Main Navbar --}}
    <div class="bg-white border-t border-slate-100 px-4 pb-6 pt-2 shadow-[0_-10px_40px_rgba(0,0,0,0.04)]">
        <div class="grid grid-cols-5 items-center">

            <a href="{{ route('therapist.dashboard') }}"
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'dashboard' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $active === 'dashboard' ? '2.5' : '2' }}">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Beranda</span>
            </a>

            <a href="{{ route('therapist.layanan') }}"
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'layanan' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="{{ $active === 'layanan' ? '2.5' : '2' }}">
                    <path
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Layanan</span>
            </a>

            {{-- Central Menu Agenda Button --}}
            <div class="flex flex-col items-center -mt-8 relative z-50">
                <a href="{{ route('therapist.jadwal') }}"
                    class="w-14 h-14 bg-teal-600 text-white rounded-2xl shadow-md shadow-teal-600/30 flex items-center justify-center transition-all active:scale-90
                    {{ $active === 'jadwal' ? 'text-teal-600' : 'text-slate-400' }}">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </a>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mt-1">Agenda</span>
            </div>

            <a href="{{ route('therapist.pasien.list') }}"
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'pasien' ? 'text-teal-600' : 'text-slate-400' }}">
                
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $active === 'pasien' ? '2.5' : '2' }}">
                    {{-- Ikon Siluet Ganda (Representasi Database/List Pasien) --}}
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.25 0 1 1-5.25 0 2.625 2.25 0 0 1 5.25 0Z" />
                </svg>
                
                <span class="text-[10px] font-bold uppercase tracking-tighter">Pasien</span>
            </a>

            <a href="{{ route('therapist.profile') }}"
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'profil' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $active === 'profil' ? '2.5' : '2' }}">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Profil</span>
            </a>

        </div>
    </div>
</div>