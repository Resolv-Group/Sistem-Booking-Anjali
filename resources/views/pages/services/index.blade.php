@extends('components.layouts.app')

@section('title', 'Menu Layanan - Anjali')

@section('content')

    <x-layouts.mobile-app class="bg-white min-h-screen">

        <div class="pb-32">
            {{-- 1. HEADER SECTION --}}
            <div class="px-6 pt-10 pb-12 space-y-6">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-0.5 bg-teal-600"></span>
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-teal-600">Our Services</span>
                </div>
                
                <h2 class="text-5xl font-semibold text-slate-900 leading-[1.1] tracking-tight">
                    Menu<br> <span class="italic text-teal-600 font-serif font-medium text-4xl">Terapi</span><br>
                    Terpadu.
                </h2>
                
                <p class="text-base text-slate-500 font-medium leading-relaxed">
                    Pilih layanan yang dirancang secara saintifik untuk membantu perjalanan pemulihan fisik dan mental Anda secara optimal.
                </p>
            </div>

            {{-- 2. FEATURED IMAGE (Matching the Home Vibe) --}}
            <div class="px-6 mb-12">
                <div class="relative rounded-[2.5rem] overflow-hidden shadow-2xl h-52">
                    <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?auto=format&fit=crop&q=80&w=800"
                        class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-teal-900/20"></div>
                </div>
            </div>

            {{-- 3. SERVICES GRID/LIST --}}
            <div class="px-6 space-y-10">
                @php
                    $all_layanan = App\Models\Layanan::all();

                    $icon_mapping = [
                        'clinical psychology' => 'brain',
                        'psikologi klinis' => 'brain',
                        'physiotherapy' => 'accessibility',
                        'fisioterapi' => 'accessibility',
                        'acupuncture' => 'activity',
                        'akupuntur' => 'activity',
                        'akupunktur' => 'activity',
                        'stimulator' => 'wind',
                        'sleeding' => 'activity',
                        'moksa' => 'flame', // Diubah sedikit agar lebih variatif
                        'massage' => 'hand',
                        'bekam' => 'droplets'
                    ];
                @endphp

                @foreach ($all_layanan as $item)
                    @php
                        $nama_lc = strtolower($item->nama);
                        $iconName = $icon_mapping[$nama_lc] ?? 'sparkles';
                    @endphp

                    <div class="group space-y-5 animate-in fade-in slide-in-from-bottom-5 duration-700">
                        {{-- Service Card --}}
                        <div class="bg-slate-50 border border-slate-100 rounded-[2.5rem] p-8 transition-all hover:bg-white hover:shadow-2xl hover:shadow-teal-900/5 hover:-translate-y-1">
                            <div class="flex justify-between items-start mb-6">
                                <div class="p-4 bg-white text-teal-600 rounded-3xl shadow-sm group-hover:bg-teal-600 group-hover:text-white transition-colors">
                                    <i data-lucide="{{ $iconName }}" class="w-8 h-8"></i>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest block mb-1">Price Start</span>
                                    <p class="text-xl font-black text-teal-700 tracking-tighter">
                                        Rp{{ number_format($item->base_harga, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <h3 class="text-2xl font-bold text-slate-800 tracking-tight leading-none">
                                    {{ $item->nama }}
                                </h3>
                                <p class="text-sm text-slate-500 font-medium leading-relaxed">
                                    {{ $item->deskripsi }}
                                </p>
                            </div>

                            {{-- Bullet Points of Benefits (Optional/Extra Vibe) --}}
                            <div class="pt-6 mt-6 border-t border-slate-200/50 flex flex-wrap gap-4">
                                <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wide">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-teal-500"></i> Berbasis Bukti
                                </span>
                                <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wide">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-teal-500"></i> Alat Steril
                                </span>
                            </div>

                            {{-- Floating Action --}}
                            <div class="pt-8">
                                <a href="{{ route('auth.login') }}" 
                                    class="flex items-center justify-center gap-3 w-full py-4 bg-teal-800 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-teal-900/20 active:scale-95 transition-all">
                                    Pesan Sekarang <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 4. FOOTER BANNER --}}
            <div class="mx-6 mt-20 p-8 bg-teal-900 rounded-[3rem] text-center space-y-6 shadow-2xl shadow-teal-900/30">
                <div class="w-16 h-16 bg-teal-800 rounded-full flex items-center justify-center mx-auto text-teal-400">
                    <i data-lucide="help-circle" class="w-8 h-8"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="text-xl font-bold text-white tracking-tight">Butuh Konsultasi?</h3>
                    <p class="text-xs text-teal-200 font-medium leading-relaxed px-4">
                        Bingung memilih layanan yang tepat untuk keluhan Anda? Tim medis kami siap membantu memberikan rekomendasi.
                    </p>
                </div>
                <a href="https://wa.me/yournumber" class="inline-block px-8 py-4 bg-white text-teal-900 rounded-2xl text-[10px] font-black uppercase tracking-widest active:scale-95 transition-all">
                    Hubungi via WhatsApp
                </a>
            </div>

            {{-- 5. BRANDING FOOTER --}}
            <div class="py-16 text-center">
                <h3 class="text-sm font-black text-teal-800 uppercase tracking-[0.3em] opacity-30">Anjali Sadina Mulyo</h3>
            </div>
        </div>

        {{-- BOTTOM NAVBAR --}}
        <x-navigation.guest-navbar active="layanan" />

    </x-layouts.mobile-app>

    {{-- Script Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
      lucide.createIcons();
    </script>

@endsection