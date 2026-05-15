@props([
    'active' => 'dashboard',
])

<div x-data="{ open: false }" class="fixed bottom-0 left-1/2 z-[100] w-full max-w-[430px] -translate-x-1/2">
    
    {{-- Menu Overlay --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-10"
         class="absolute bottom-24 left-6 right-6 bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 p-8 z-[101]"
         x-cloak>
        <div class="grid grid-cols-3 gap-y-8 gap-x-6">
            <a href="#" class="flex flex-col items-center gap-3 group">
                <div class="w-14 h-14 rounded-2xl bg-teal-50 flex items-center justify-center text-2xl group-active:scale-90 transition-all shadow-sm">📜</div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Audit Log</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-3 group">
                <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center text-2xl group-active:scale-90 transition-all shadow-sm">🗄️</div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Database</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-3 group">
                <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center text-2xl group-active:scale-90 transition-all shadow-sm">📋</div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Master</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-3 group">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-2xl group-active:scale-90 transition-all shadow-sm">📑</div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Logs</span>
            </a>
        </div>
        
        {{-- Close Button --}}
        <button @click="open = false" class="mt-8 w-full py-4 bg-slate-50 text-slate-400 rounded-2xl text-xs font-bold uppercase tracking-widest active:bg-slate-100">
            Tutup Menu
        </button>
    </div>

    {{-- Backdrop --}}
    <div x-show="open" @click="open = false" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-teal-900/20 backdrop-blur-sm -z-10" x-cloak></div>

    {{-- Main Navbar --}}
    <div class="bg-white border-t border-slate-100 px-4 pb-6 pt-2 shadow-[0_-10px_40px_rgba(0,0,0,0.04)]">
        <div class="grid grid-cols-5 items-center">

            <a href="{{ route('admin-global.dashboard') }}"
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'dashboard' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $active === 'dashboard' ? '2.5' : '2' }}">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Beranda</span>
            </a>

            <a href="#"   
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'cabang' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $active === 'cabang' ? '2.5' : '2' }}">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Cabang</span>
            </a>

            {{-- Central Menu Button --}}
            <div class="flex flex-col items-center -mt-8 relative z-50">
                <button @click="open = !open" 
                    class="w-14 h-14 bg-teal-800 text-white rounded-2xl shadow-xl shadow-teal-900/30 flex items-center justify-center transition-all active:scale-90"
                    :class="open ? 'bg-orange-500 shadow-orange-500/30' : ''">
                    <svg x-show="!open" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <svg x-show="open" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" x-cloak>
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <span class="text-[10px] font-black text-teal-800 uppercase tracking-widest mt-2">Menu</span>
            </div>

            <a href="#"
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'settings' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $active === 'settings' ? '2.5' : '2' }}">
                    <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Sistem</span>
            </a>

            <a href="{{ route('admin-global.profile') }}"
                class="flex flex-col items-center gap-1 py-2 transition-all active:scale-90
                {{ $active === 'profile' ? 'text-teal-600' : 'text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $active === 'profile' ? '2.5' : '2' }}">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Profil</span>
            </a>

        </div>
    </div>
</div>