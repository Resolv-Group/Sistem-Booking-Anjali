@extends('components.layouts.app')

@section('title', 'Layanan Saya')

@section('content')

<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen">

    {{-- TOPBAR --}}
    <nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-100/80 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">
                    Terapis — {{ $therapist->nama_karyawan }}
                </span>
                <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none uppercase">
                    Layanan Saya
                </h1>
            </div>
            <div class="w-10 h-10 rounded-xl border-2 border-white shadow-md p-0.5">
                @php
                    $fotoUrl = $therapist->foto
                        ? 'data:' . ($therapist->foto_mime ?? 'image/jpeg') . ';base64,' . $therapist->foto
                        : asset('images/logo_anjali.jpg');
                @endphp
                <img src="{{ $fotoUrl }}" class="w-full h-full rounded-[10px] object-cover bg-white">
            </div>
        </div>
    </nav>

    <div class="px-6 pt-8 pb-32 space-y-8">

        {{-- HEADER --}}
        <div class="space-y-2">
            <h2 class="text-3xl font-semibold text-teal-900 tracking-tight">Layanan Anda</h2>
            <p class="text-base text-slate-500 font-medium leading-relaxed">
                Berikut adalah layanan terapi yang ditugaskan kepada Anda oleh administrator.
            </p>
        </div>

        {{-- SUMMARY BADGE --}}
        <div class="bg-teal-900 rounded-3xl p-6 text-white relative overflow-hidden shadow-xl shadow-teal-900/20">
            <div class="absolute top-0 right-0 w-32 h-32 bg-teal-500/10 rounded-full -mr-10 -mt-10 blur-2xl"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-semibold text-teal-300 uppercase tracking-[0.2em] mb-1">Total Layanan</p>
                    <p class="text-4xl font-semibold leading-none">{{ sprintf('%02d', $layanans->count()) }}</p>
                    <p class="text-xs text-teal-200/60 mt-1 font-medium">layanan aktif ditugaskan</p>
                </div>
                <div class="w-14 h-14 bg-teal-700/50 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-teal-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- LAYANAN LIST --}}
        @php
            $icon_mapping = [
                'clinical psychology' => 'brain',
                'psikologi klinis'    => 'brain',
                'physiotherapy'       => 'accessibility',
                'fisioterapi'         => 'activity',
                'acupuncture'         => 'activity',
                'akupuntur'           => 'activity',
                'akupunktur'          => 'activity',
                'stimulator'          => 'wind',
                'sleeding'            => 'activity',
                'moksa'               => 'flame',
                'massage'             => 'hand',
                'bekam'               => 'droplets',
            ];
            $color_pairs = [
                ['bg' => 'bg-teal-50',    'text' => 'text-teal-600',    'border' => 'border-teal-100'],
                ['bg' => 'bg-blue-50',    'text' => 'text-blue-600',    'border' => 'border-blue-100'],
                ['bg' => 'bg-violet-50',  'text' => 'text-violet-600',  'border' => 'border-violet-100'],
                ['bg' => 'bg-orange-50',  'text' => 'text-orange-600',  'border' => 'border-orange-100'],
                ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-100'],
                ['bg' => 'bg-pink-50',    'text' => 'text-pink-600',    'border' => 'border-pink-100'],
            ];
        @endphp

        @forelse($layanans as $index => $item)
            @php
                $nama_lc  = strtolower($item->nama);
                $iconName = $icon_mapping[$nama_lc] ?? 'sparkles';
                $color    = $color_pairs[$index % count($color_pairs)];
                $diskon   = $item->diskon_persentase > 0 ? $item->diskon_persentase : null;
                $hargaAkhir = $diskon
                    ? $item->base_harga * (1 - $diskon / 100)
                    : $item->base_harga;
            @endphp

            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden group hover:shadow-md transition-all">

                {{-- Card Header --}}
                <div class="p-6 space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="p-3 {{ $color['bg'] }} {{ $color['text'] }} rounded-2xl">
                            <i data-lucide="{{ $iconName }}" class="w-6 h-6"></i>
                        </div>
                        <div class="text-right space-y-0.5">
                            @if($diskon)
                                <p class="text-[10px] text-slate-400 line-through">Rp{{ number_format($item->base_harga, 0, ',', '.') }}</p>
                                <p class="text-lg font-black text-teal-700 tracking-tighter leading-none">
                                    Rp{{ number_format($hargaAkhir, 0, ',', '.') }}
                                </p>
                                <span class="inline-block text-[9px] font-black uppercase tracking-widest px-2 py-0.5 bg-red-50 text-red-500 rounded-lg">
                                    -{{ $diskon }}% diskon
                                </span>
                            @else
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Harga</p>
                                <p class="text-lg font-black text-teal-700 tracking-tighter leading-none">
                                    Rp{{ number_format($item->base_harga, 0, ',', '.') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-xl font-bold text-slate-800 tracking-tight leading-none">{{ $item->nama }}</h3>
                        @if($item->deskripsi)
                            <p class="text-sm text-slate-500 font-medium leading-relaxed">{{ $item->deskripsi }}</p>
                        @endif
                    </div>

                    {{-- Status badge + homecare price --}}
                    <div class="flex items-center gap-3 flex-wrap pt-1">
                        <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest
                            {{ $item->status === 'Tersedia' ? 'text-emerald-600' : 'text-slate-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $item->status === 'Tersedia' ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            {{ $item->status }}
                        </span>

                        @if($item->homecare_harga && $item->homecare_harga > 0)
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                Homecare: Rp{{ number_format($item->homecare_harga, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Card Footer strip --}}
                <div class="px-6 py-3 border-t border-slate-50 bg-slate-50/50 flex items-center gap-3">
                    <svg class="w-4 h-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Berbasis Bukti</span>
                    <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Alat Steril</span>
                </div>
            </div>

        @empty
            <div class="text-center py-24 bg-white rounded-3xl border border-slate-100">
                <div class="w-16 h-16 mx-auto bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-400">Belum ada layanan yang ditugaskan.</p>
                <p class="text-xs text-slate-400 font-medium mt-1">Hubungi administrator untuk mengatur layanan Anda.</p>
            </div>
        @endforelse

    </div>

    {{-- BOTTOM NAVBAR --}}
    <x-navigation.therapist-navbar active="layanan" />

</x-layouts.mobile-app>

{{-- Lucide Icons --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>

@endsection
