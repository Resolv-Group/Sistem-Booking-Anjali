@extends('components.layouts.app')

@section('title', 'Anjali Therapy Center')

@section('content')

    <x-layouts.mobile-app class="bg-white min-h-screen">

        <div class="pb-32">
            {{-- 2. HERO SECTION --}}
            <div class="px-6 pt-10 pb-8 space-y-6">
                <span
                    class="inline-block px-3 py-1 bg-teal-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-md">
                    Clinical Excellence
                </span>
                <h2 class="text-5xl font-semibold text-slate-900 leading-[1.1] tracking-tight">
                    Clinical <br> <span class="italic text-teal-600 font-serif font-medium text-4xl">Precision</span> in <br>
                    Therapy.
                </h2>
                <p class="text-lg text-slate-500 font-medium leading-relaxed">
                    Kami menggabungkan pengalaman bertahun-tahun dalam pengobatan dan diagnostik berbasis bukti untuk
                    mencapai hasil terbaik bagi komunitas kami.
                </p>
                <div class="flex flex-col gap-3">
                    <a href="{{ route('view.auth.login') }}"
                        class="w-full py-5 bg-teal-800 text-white rounded-2xl text-base font-semibold uppercase tracking-widest shadow-xl shadow-teal-900/20 text-center active:scale-95 transition-all">
                        Book Session
                    </a>
                    <a href="{{ route('layanan') }}"
                        class="w-full py-5 bg-blue-50 text-teal-700 rounded-2xl text-base font-semibold uppercase tracking-widest text-center">
                        View Therapy Menu
                    </a>
                </div>
            </div>

            {{-- 3. IMAGE & LOCATION CARD --}}
            <div class="px-6 relative">
                <div class="rounded-[2.5rem] overflow-hidden shadow-2xl h-80">
                    <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&q=80&w=800"
                        class="w-full h-full object-cover">
                </div>
                {{-- Floating Location Card --}}
                <div
                    class="absolute -bottom-10 left-10 right-10 bg-white/95 backdrop-blur-md p-6 rounded-3xl border border-slate-100 shadow-xl flex items-start gap-4">
                    <div class="p-3 bg-teal-50 text-teal-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Surabaya Medical Hub</h4>
                        <p class="text-xs text-slate-400 font-medium leading-relaxed mt-1">Berlokasi strategis di pusat kota
                            untuk akses kesehatan terbaik Anda.</p>
                    </div>
                </div>
            </div>

            {{-- 4. THERAPY MENU SECTION --}}
            <div class="px-6 pt-24 space-y-6">
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-slate-800 tracking-tight">Therapy Menu</h3>
                    <p class="text-sm text-slate-400 font-semibold uppercase tracking-[0.1em] mt-1">Designed with scientific
                        excellence</p>
                </div>

                <div class="space-y-4">
                    @php
                        $menus = [
                            [
                                'title' => 'Clinical Psychology',
                                'price' => '350',
                                'icon' => 'brain',
                                'desc' => 'Kesehatan mental berbasis bukti untuk komunitas.',
                            ],
                            [
                                'title' => 'Physiotherapy',
                                'price' => '450',
                                'icon' => 'accessibility',
                                'desc' => 'Fokus pada restorasi fungsional gerak tubuh.',
                            ],
                            [
                                'title' => 'Acupuncture',
                                'price' => '200',
                                'icon' => 'activity',
                                'desc' => 'Pengobatan berbagai keluhan melalui titik saraf.',
                            ],
                        ];
                    @endphp

                    @foreach ($menus as $menu)
                        <div
                            class="bg-white border border-slate-100 rounded-[2rem] p-6 shadow-sm hover:shadow-md transition-all">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="p-3 bg-slate-50 text-teal-600 rounded-2xl">
                                    <i class="lucide-{{ $menu['icon'] }} w-6 h-6"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-slate-800 leading-none">{{ $menu['title'] }}</h4>
                                    <p class="text-sm text-slate-400 font-medium mt-2 leading-relaxed">{{ $menu['desc'] }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex justify-between items-center pt-4 border-t border-slate-50">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Mulai Dari <span
                                        class="text-teal-600 ml-1">Rp{{ $menu['price'] }}k</span></p>
                                <a href="{{ route('layanan') }}"
                                    class="text-xs font-bold text-teal-700 uppercase tracking-widest underline">View
                                    Detail</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 5. EXPERTS SECTION --}}
            <div class="px-6 pt-20 space-y-8">
                <h3 class="text-center text-sm font-bold text-slate-400 uppercase tracking-[0.3em]">Our Experts Info</h3>

                <div class="space-y-12">
                    {{-- Expert 1 --}}
                    <div class="flex flex-col items-center">
                        <div
                            class="w-full aspect-[4/5] rounded-[3rem] overflow-hidden shadow-2xl mb-6 border-8 border-white">
                            <img src="https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=800"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="text-center space-y-2">
                            <h4 class="text-2xl font-black text-slate-800 tracking-tight leading-none uppercase">Mr. David
                                Purnomo</h4>
                            <p
                                class="text-xs font-bold text-teal-600 uppercase tracking-widest border-b-2 border-teal-500 inline-block pb-1">
                                Head of Therapy Hub</p>
                            <p class="text-sm text-slate-500 font-medium leading-relaxed pt-2 px-4">
                                Berpengalaman luas dalam menangani kecemasan, depresi, dan pemulihan kesehatan mental
                                keluarga.
                            </p>
                        </div>
                        <div class="flex gap-4 mt-6">
                            <span
                                class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest"><i
                                    class="lucide-award w-4 h-4"></i> Spesialis</span>
                            <a href="#"
                                class="flex items-center gap-2 text-[10px] font-black text-teal-700 uppercase tracking-widest underline">Lihat
                                Jadwal</a>
                        </div>
                    </div>

                    {{-- Expert 2 --}}
                    <div class="flex flex-col items-center">
                        <div
                            class="w-full aspect-[4/5] rounded-[3rem] overflow-hidden shadow-2xl mb-6 border-8 border-white">
                            <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&q=80&w=800"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="text-center space-y-2">
                            <h4 class="text-2xl font-black text-slate-800 tracking-tight leading-none uppercase">Dr. Aris
                                Budiman</h4>
                            <p
                                class="text-xs font-bold text-teal-600 uppercase tracking-widest border-b-2 border-teal-500 inline-block pb-1">
                                Chief Clinical Officer</p>
                            <p class="text-sm text-slate-500 font-medium leading-relaxed pt-2 px-4">
                                Pakar dalam rehabilitasi saraf dan manajemen nyeri berkelanjutan dengan standar medis
                                internasional.
                            </p>
                        </div>
                        <div class="flex gap-4 mt-6">
                            <span
                                class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest"><i
                                    class="lucide-award w-4 h-4"></i> Spesialis</span>
                            <a href="#"
                                class="flex items-center gap-2 text-[10px] font-black text-teal-700 uppercase tracking-widest underline">Lihat
                                Jadwal</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6. SEAMLESS BOOKING --}}
            <div class="mx-6 mt-20 p-10 bg-slate-50 rounded-[3rem] border border-slate-100 space-y-10">
                <div class="text-center space-y-3">
                    <h3 class="text-3xl font-bold text-slate-800 tracking-tight">Seamless Booking</h3>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed">
                        Kami menyediakan cara termudah dan tercepat untuk mendapatkan bantuan ahli dari spesialis kami.
                    </p>
                </div>

                <div class="space-y-12 relative">
                    {{-- Decorative Line --}}
                    <div class="absolute left-6 top-8 bottom-8 w-0.5 bg-slate-200"></div>

                    <div class="flex gap-6 relative z-10">
                        <div
                            class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-teal-600 font-black shadow-sm">
                            1</div>
                        <div>
                            <h4 class="text-base font-bold text-slate-800">Join Therapy</h4>
                            <p class="text-xs text-slate-400 font-medium mt-1 leading-relaxed">Hubungi kami dan terhubung
                                dengan tim kru kesehatan kami.</p>
                        </div>
                    </div>
                    <div class="flex gap-6 relative z-10">
                        <div
                            class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-teal-600 font-black shadow-sm">
                            2</div>
                        <div>
                            <h4 class="text-base font-bold text-slate-800">Finalise</h4>
                            <p class="text-xs text-slate-400 font-medium mt-1 leading-relaxed">Tentukan sesi, tanggal, dan
                                waktu sesuai dengan ketersediaan Anda.</p>
                        </div>
                    </div>
                    <div class="flex gap-6 relative z-10">
                        <div
                            class="w-12 h-12 rounded-2xl bg-teal-800 text-white flex items-center justify-center font-black shadow-lg shadow-teal-900/20">
                            3</div>
                        <div>
                            <h4 class="text-base font-bold text-slate-800">Payment</h4>
                            <p class="text-xs text-slate-400 font-medium mt-1 leading-relaxed">Amankan slot Anda dan segera
                                mulai perjalanan kesehatan Anda.</p>
                        </div>
                    </div>
                </div>

                <button
                    class="w-full py-5 bg-teal-800 text-white rounded-2xl text-base font-bold uppercase tracking-widest shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                    Connect to us Now
                </button>
            </div>

            {{-- 7. CERTIFIED HOUSES --}}
            <div class="px-6 pt-20 space-y-8 pb-10">
                <div class="space-y-3">
                    <h3 class="text-2xl font-bold text-slate-800 tracking-tight">Certified Therapy Houses</h3>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed">
                        Rumah terapi kami tersertifikasi dan berlokasi di sekitar Anda untuk memastikan pelayanan terbaik.
                    </p>
                    <div class="flex gap-4 pt-2">
                        <span
                            class="flex items-center gap-2 text-[10px] font-black text-teal-700 uppercase tracking-widest"><i
                                class="lucide-check-circle w-3 h-3"></i> Certified Hub</span>
                        <span
                            class="flex items-center gap-2 text-[10px] font-black text-teal-700 uppercase tracking-widest"><i
                                class="lucide-globe w-3 h-3"></i> Global Standard</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="relative rounded-3xl overflow-hidden h-40 group shadow-lg">
                        <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&q=80&w=800"
                            class="w-full h-full object-cover">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-80">
                        </div>
                        <div class="absolute bottom-6 left-6 text-white">
                            <h4 class="text-lg font-bold uppercase tracking-widest">Surabaya Hub</h4>
                            <p class="text-xs font-medium opacity-80 mt-1">Pusat terapi utama kami di jantung Kota
                                Surabaya.</p>
                        </div>
                    </div>
                    <div class="relative rounded-3xl overflow-hidden h-40 group shadow-lg">
                        <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=800"
                            class="w-full h-full object-cover">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-80">
                        </div>
                        <div class="absolute bottom-6 left-6 text-white">
                            <h4 class="text-lg font-bold uppercase tracking-widest">Malang Hub</h4>
                            <p class="text-xs font-medium opacity-80 mt-1">Ekspansi layanan untuk kesehatan masyarakat
                                Malang.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 8. FOOTER --}}
            <div class="px-10 py-16 text-center space-y-8 bg-white border-t border-slate-50">
                <div class="space-y-2">
                    <h3 class="text-sm font-black text-teal-800 uppercase tracking-[0.3em]">Anjali Therapy Center</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest italic">Built for healthcare,
                        driven by you.</p>
                </div>

                <div class="flex justify-center gap-6">
                    <a href="#"
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-teal-600 transition">Privacy
                        Policy</a>
                    <a href="#"
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-teal-600 transition">Terms
                        of Service</a>
                </div>
            </div>

        </div>

        {{-- BOTTOM NAVBAR --}}
        <x-navigation.guest-navbar active="home" />

    </x-layouts.mobile-app>

@endsection
