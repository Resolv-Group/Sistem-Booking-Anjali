@extends('components.layouts.app')

@section('title', 'Tentang Kami - Anjali Therapy Center')

@section('content')

    <x-layouts.mobile-app class="bg-white min-h-screen">

        <div class="pb-32">
            {{-- 1. HERO SECTION --}}
            <div class="px-6 pt-10 pb-12 space-y-6">
                <span class="inline-block px-3 py-1 bg-teal-100 text-teal-800 text-[10px] font-black uppercase tracking-[0.2em] rounded-md">
                    Our Philosophy
                </span>
                
                <h2 class="text-5xl font-semibold text-slate-900 leading-[1.1] tracking-tight">
                    Dedikasi<br> <span class="italic text-teal-600 font-serif font-medium text-4xl">Tanpa</span><br>
                    Batas.
                </h2>
                
                <p class="text-lg text-slate-500 font-medium leading-relaxed">
                    Di Rumah Terapi Anjali, kami percaya bahwa pemulihan sejati terjadi ketika sains bertemu dengan empati terdalam manusia.
                </p>
            </div>

            {{-- 2. MAIN BRAND IMAGE --}}
            <div class="px-6 relative">
                <div class="rounded-[3rem] overflow-hidden shadow-2xl h-96 border-8 border-slate-50">
                    <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&q=80&w=800"
                        class="w-full h-full object-cover">
                </div>
                
                {{-- Floating Experience Card --}}
                <div class="absolute -bottom-10 right-10 bg-teal-900 p-6 rounded-3xl shadow-2xl border border-teal-800 space-y-1">
                    <h4 class="text-3xl font-black text-white leading-none">10+</h4>
                    <p class="text-[10px] font-bold text-teal-300 uppercase tracking-widest leading-none">Tahun Pengalaman</p>
                </div>
            </div>

            {{-- 3. OUR STORY SECTION --}}
            <div class="px-6 pt-24 space-y-6">
                <div class="space-y-4">
                    <h3 class="text-2xl font-bold text-slate-800 tracking-tight">Perjalanan Kami</h3>
                    <div class="w-12 h-1 bg-teal-600 rounded-full"></div>
                </div>
                
                <div class="space-y-6 text-slate-500 font-medium leading-relaxed text-base">
                    <p>
                        Berawal dari sebuah visi sederhana di Surabaya, Anjali hadir untuk menjembatani kesenjangan antara pengobatan konvensional dan kebutuhan akan terapi yang lebih personal serta berbasis bukti (*evidence-based*).
                    </p>
                    <p>
                        Kami telah berevolusi menjadi pusat kolaborasi bagi para spesialis terbaik, menyatukan berbagai keahlian untuk memberikan solusi kesehatan fisik dan mental yang menyeluruh bagi masyarakat.
                    </p>
                </div>
            </div>

            {{-- 4. CORE VALUES (Grid Layout) --}}
            <div class="px-6 pt-20 space-y-8">
                <div class="text-center space-y-2">
                    <h3 class="text-sm font-black text-teal-800 uppercase tracking-[0.3em]">Nilai Utama Kami</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase">The pillars of our excellence</p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    {{-- Value 1 --}}
                    <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 flex flex-col items-center text-center space-y-4">
                        <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-teal-600">
                            <i data-lucide="microscope" class="w-8 h-8"></i>
                        </div>
                        <h4 class="text-lg font-bold text-slate-800">Berbasis Bukti</h4>
                        <p class="text-sm text-slate-400 leading-relaxed font-medium">
                            Setiap diagnosa dan protokol terapi yang kami berikan didukung oleh data klinis dan riset medis terbaru.
                        </p>
                    </div>

                    {{-- Value 2 --}}
                    <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 flex flex-col items-center text-center space-y-4">
                        <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-teal-600">
                            <i data-lucide="heart-handshake" class="w-8 h-8"></i>
                        </div>
                        <h4 class="text-lg font-bold text-slate-800">Sentuhan Empati</h4>
                        <p class="text-sm text-slate-400 leading-relaxed font-medium">
                            Kami mendengarkan lebih dari sekadar keluhan fisik. Kami memahami aspek psikologis di balik setiap pemulihan.
                        </p>
                    </div>

                    {{-- Value 3 --}}
                    <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 flex flex-col items-center text-center space-y-4">
                        <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-teal-600">
                            <i data-lucide="shield-check" class="w-8 h-8"></i>
                        </div>
                        <h4 class="text-lg font-bold text-slate-800">Integritas Tinggi</h4>
                        <p class="text-sm text-slate-400 leading-relaxed font-medium">
                            Kerahasiaan data pasien dan transparansi biaya adalah prioritas utama dalam operasional harian kami.
                        </p>
                    </div>
                </div>
            </div>

            {{-- 6. CTA BANNER --}}
            <div class="px-6 pt-24 text-center space-y-8">
                <h3 class="text-3xl font-bold text-slate-800 tracking-tight leading-tight">Siap Untuk Memulai Pemulihan?</h3>
                <div class="flex flex-col gap-3">
                    <a href="{{ route('auth.login') }}" 
                        class="w-full py-5 bg-teal-800 text-white rounded-2xl text-sm font-black uppercase tracking-widest shadow-xl shadow-teal-900/20 transition-all active:scale-95">
                        Daftar Sesi Sekarang
                    </a>
                </div>
            </div>

            {{-- 7. BRANDING FOOTER --}}
            <div class="py-20 text-center opacity-30">
                <h3 class="text-sm font-black text-teal-800 uppercase tracking-[0.4em]">PT. Anjali sadina mulyo</h3>
            </div>
        </div>

        {{-- BOTTOM NAVBAR --}}
        <x-navigation.guest-navbar active="about" />

    </x-layouts.mobile-app>

    {{-- Script Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
      lucide.createIcons();
    </script>

@endsection