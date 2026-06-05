@extends('components.layouts.app')

@section('title', 'Anjali Therapy Center')

@section('content')

    <x-layouts.mobile-app class="bg-white min-h-screen">

        <div class="pb-32">
            {{-- 2. HERO SECTION --}}
            <div class="px-6 pt-10 pb-8 space-y-6">
                <span
                    class="inline-block px-3 py-1 bg-teal-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-md">
                    Anjali Sadina Mulyo
                </span>
                <h2 class="text-5xl font-semibold text-slate-900 leading-[1.1] tracking-tight">
                    Terapi Akurat<br> <span class="italic text-teal-600 font-serif font-medium text-4xl">Pemulihan</span><br>
                    Tepat.
                </h2>
                <p class="text-lg text-slate-500 font-medium leading-relaxed">
                    Kami menggabungkan pengalaman bertahun-tahun dalam pengobatan dan diagnostik berbasis bukti untuk
                    mencapai hasil terbaik bagi komunitas kami.
                </p>
                <div class="flex flex-col gap-3">
                    <a href="{{ route('patient.booking.form') }}"
                        class="w-full py-5 bg-teal-800 text-white rounded-2xl text-base font-semibold uppercase tracking-widest shadow-xl shadow-teal-900/20 text-center active:scale-95 transition-all">
                        Buat Janji
                    </a>
                    <a href="{{ route('patient.therapist') }}"
                        class="w-full py-5 bg-blue-50 text-teal-700 rounded-2xl text-base font-semibold uppercase tracking-widest text-center">
                        Lihat Terapis & Layanan
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
                        <h4 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Tersedia di Surabaya</h4>
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
                        // 1. Ambil 3 data dari database
                        $daftar_layanan = App\Models\Layanan::limit(3)->get();

                        // 2. Buat mapping dengan HURUF KECIL SEMUA pada bagian Key (kiri)
                        // Ini untuk menghindari error salah ketik huruf besar/kecil di database
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
                            'moksa' => 'accessibility',
                        ];
                    @endphp

                    @foreach ($daftar_layanan as $item)
                        @php
                            // 3. Ubah nama layanan dari DB ke huruf kecil sebelum dicocokkan
                            $nama_layanan_lc = strtolower($item->nama);

                            // 4. Cari ikonnya. Jika nama di DB tidak ada di list, pakai 'activity'
                            $iconName = $icon_mapping[$nama_layanan_lc] ?? 'activity';
                        @endphp

                        <div
                            class="bg-white border border-slate-100 rounded-[2rem] p-6 shadow-sm hover:shadow-md transition-all">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="p-3 bg-slate-50 text-teal-600 rounded-2xl">
                                    {{-- Sekarang ikon akan berubah secara dinamis di setiap loop --}}
                                    <i class="lucide-{{ $iconName }} w-6 h-6"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-slate-800 leading-none">{{ $item->nama }}</h4>
                                    <p class="text-sm text-slate-400 font-medium mt-2 leading-relaxed">
                                        {{ $item->deskripsi }}</p>
                                </div>
                            </div>
                            <div class="flex justify-center items-center pt-4 border-t border-slate-50">
                                <p class="text-sm font-bold uppercase tracking-widest text-slate-800">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                                    Mulai Dari <span
                                        class="text-teal-600 ml-1">Rp{{ number_format($item->base_harga, 0) }}k</span>
                                </p>
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 5. EXPERTS SECTION --}}
            <div class="px-6 pt-20 space-y-8">
                @php
                    // 1. Ambil 3 data terapis dari database
                    $spesialis = App\Models\karyawan::where('peran', 'Terapis')->limit(3)->get();

                    // 2. Kumpulan teks bio acak agar tampilan bervariasi dan tetap terlihat profesional
                    $random_bios = [
                        'Berpengalaman luas dalam menangani pemulihan holistik, manajemen stres, dan peningkatan kualitas hidup pasien.',
                        'Berdedikasi tinggi dalam memberikan penanganan taktis, adaptif, dan berbasis bukti demi kenyamanan Anda.',
                        'Spesialis dalam pendekatan personal yang berfokus pada pemulihan jangka panjang dan kesehatan menyeluruh.',
                    ];

                    // 3. Kumpulan jadwal acak untuk ditampilkan di card
                    $random_schedules = [
                        'Senin - Kamis (09:00 - 15:00)',
                        'Selasa & Jumat (13:00 - 20:00)',
                        'Senin - Jumat (10:00 - 17:00)',
                    ];
                @endphp

                <h3 class="text-center text-sm font-bold text-slate-400 uppercase tracking-[0.3em]">Spesialis Kami</h3>

                <div class="space-y-12">
                    @foreach ($spesialis as $index => $item)
                        @php
                            // Mengambil bio dan jadwal acak berdasarkan indeks perulangan loop
                            $bio = $random_bios[$index % count($random_bios)];
                            $jadwal = $random_schedules[$index % count($random_schedules)];

                            $photoUrl = $item->foto
                                ? 'data:' . ($item->foto_mime ?? 'image/jpg') . ';base64,' . $item->foto
                                : asset('images/logo_anjali.jpg'); 
                        @endphp

                        <div class="flex flex-col items-center">
                            {{-- Foto Pakar --}}
                            <div
                                class="w-full aspect-[4/5] rounded-[3rem] overflow-hidden shadow-2xl mb-6 border-8 border-white">
                                <img src="{{ $photoUrl }}" class="w-full h-full object-cover">
                            </div>

                            {{-- Detail Pakar --}}
                            <div class="text-center space-y-2">
                                <h4 class="text-2xl font-black text-slate-800 tracking-tight leading-none uppercase">
                                    {{ $item->nama_karyawan }}
                                </h4>

                                {{-- Menggunakan nama kolaborasi dari relasi sesuai permintaan Anda --}}
                                <p
                                    class="text-xs font-bold text-teal-600 uppercase tracking-widest border-b-2 border-teal-500 inline-block pb-1">
                                    {{ $item->kolaborasi->nama_kolaborasi ?? 'Pakar Terapi' }}
                                </p>

                                {{-- Menampilkan Bio Acak --}}
                                <p class="text-sm text-slate-500 font-medium leading-relaxed pt-2 px-4">
                                    {{ $bio }}
                                </p>

                                {{-- Komponen Jadwal Baru --}}
                                <div class="pt-3 px-6 inline-block">
                                    <div
                                        class="bg-slate-50 border border-slate-100 rounded-full px-4 py-1.5 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span class="text-[11px] font-bold text-slate-600 tracking-wide uppercase">
                                            {{ $jadwal }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- CTA Bagian Bawah --}}
                            <div class="flex gap-4 mt-6">
                                <span
                                    class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                    <i class="lucide-award w-4 h-4"></i> Spesialis
                                </span>
                                <a href="{{ route('patient.booking.form', ['therapist_id' => $item->id]) }}"
                                    class="flex items-center gap-2 text-[10px] font-black text-teal-700 uppercase tracking-widest underline">
                                    Booking Sekarang
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 6. SEAMLESS BOOKING --}}
            <div class="mx-6 mt-20 p-10 bg-slate-50 rounded-[3rem] border border-slate-100 space-y-10">
                <div class="text-center space-y-3">
                    <h3 class="text-3xl font-bold text-slate-800 tracking-tight">Pendaftaran Mudah</h3>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed">
                        Kami menyediakan cara termudah dan tercepat untuk mendapatkan bantuan ahli dari spesialis kami.
                    </p>
                </div>

                <div class="space-y-12 relative">
                    {{-- Decorative Line --}}
                    <div class="absolute left-6 top-8 bottom-8 w-0.5 bg-slate-200"></div>

                    {{-- Langkah 1 --}}
                    <div class="flex items-start gap-6 relative z-10">
                        {{-- Ditambahkan flex-shrink-0 agar ukuran kotak nomor terkunci --}}
                        <div
                            class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex flex-shrink-0 items-center justify-center text-teal-600 font-black shadow-sm">
                            1
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-slate-800">Pilih Terapis</h4>
                            <p class="text-xs text-slate-400 font-medium mt-1 leading-relaxed">
                                Pilih terapis yang paling sesuai dengan kebutuhan Anda. Anda bebas menentukan spesialis yang
                                Anda percayai untuk proses pemulihan yang optimal.
                            </p>
                        </div>
                    </div>

                    {{-- Langkah 2 --}}
                    <div class="flex items-start gap-6 relative z-10">
                        {{-- Ditambahkan flex-shrink-0 --}}
                        <div
                            class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex flex-shrink-0 items-center justify-center text-teal-600 font-black shadow-sm">
                            2
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-slate-800">Atur Jadwal</h4>
                            <p class="text-xs text-slate-400 font-medium mt-1 leading-relaxed">
                                Pilih tanggal dan waktu terapi sesuai ketersediaan waktu luang Anda tanpa mengganggu
                                produktivitas harian.
                            </p>
                        </div>
                    </div>

                    {{-- Langkah 3 --}}
                    <div class="flex items-start gap-6 relative z-10">
                        {{-- Ditambahkan flex-shrink-0 --}}
                        <div
                            class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex flex-shrink-0 items-center justify-center text-teal-600 font-black shadow-sm">
                            3
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-slate-800">Pilih Layanan</h4>
                            <p class="text-xs text-slate-400 font-medium mt-1 leading-relaxed">
                                Pilih jenis paket layanan yang sesuai dengan kebutuhan kesehatan Anda saat ini untuk hasil
                                yang maksimal.
                            </p>
                        </div>
                    </div>

                    {{-- Langkah 4 --}}
                    <div class="flex items-start gap-6 relative z-10">
                        {{-- Ditambahkan flex-shrink-0 --}}
                        <div
                            class="w-12 h-12 rounded-2xl bg-teal-800 text-white flex flex-shrink-0 items-center justify-center font-black shadow-lg shadow-teal-900/20">
                            4
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-slate-800">Konfirmasi & Bayar</h4>
                            <p class="text-xs text-slate-400 font-medium mt-1 leading-relaxed">
                                Amankan slot Anda dan segera mulai perjalanan menuju pemulihan kesehatan fisik maupun mental
                                Anda bersama kami.
                            </p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('patient.booking.index') }}"
                    class="inline-block w-full max-w-md mx-auto text-center py-5 bg-teal-800 hover:bg-teal-900 text-white rounded-2xl text-base font-bold uppercase tracking-widest shadow-xl shadow-teal-900/20 active:scale-[0.98] transition-all">
                    Mulai Sekarang
                </a>
            </div>

            {{-- 7. CERTIFIED HOUSES --}}
            <div class="px-6 pt-20 space-y-8 pb-10">
    <div class="space-y-3">
        {{-- Judul Utama diubah menjadi Kolaborasi --}}
        <h3 class="text-2xl font-bold text-slate-800 tracking-tight">Kolaborasi</h3>
        
        <p class="text-sm text-slate-500 font-medium leading-relaxed">
            Sinergi eksklusif rumah terapi tersertifikasi untuk menghadirkan pelayanan kesehatan fisik dan mental terbaik di dekat Anda.
        </p>
        
        <div class="flex gap-4 pt-2">
            <span class="flex items-center gap-2 text-[10px] font-black text-teal-700 uppercase tracking-widest">
                <i class="lucide-check-circle w-3 h-3"></i> Kerja Sama Resmi
            </span>
            <span class="flex items-center gap-2 text-[10px] font-black text-teal-700 uppercase tracking-widest">
                <i class="lucide-globe w-3 h-3"></i> Standar Global
            </span>
        </div>
    </div>

    <div class="space-y-4">
        {{-- Pusat Layanan Terpadu di Surabaya --}}
        <div class="relative rounded-3xl overflow-hidden h-52 group shadow-lg">
            <img src="{{ asset('images/anjali_1.png') }}"
                class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-90"></div>
            
            <div class="absolute bottom-6 left-6 right-6 text-white">
                <h4 class="text-xl font-bold uppercase tracking-widest mt-1">Anjali</h4>
                <p class="text-xs font-medium opacity-80 mt-1 leading-relaxed">
                    Tersedia di surabaya.
                </p>
            </div>
        </div>
        <div class="relative rounded-3xl overflow-hidden h-52 group shadow-lg">
            <img src="{{ asset('images/limajari_1.png') }}"
                class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-90"></div>
            
            <div class="absolute bottom-6 left-6 right-6 text-white">
                <h4 class="text-xl font-bold uppercase tracking-widest mt-1">Limajari</h4>
                <p class="text-xs font-medium opacity-80 mt-1 leading-relaxed">
                    Tersedia di surabaya.
                </p>
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
                {{-- 
                <div class="flex justify-center gap-6">
                    <a href="#"
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-teal-600 transition">Privacy
                        Policy</a>
                    <a href="#"
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-teal-600 transition">Terms
                        of Service</a>
                </div> --}}
            </div>

        </div>

        {{-- BOTTOM NAVBAR --}}
        <x-navigation.patient-navbar active="home" />

    </x-layouts.mobile-app>

@endsection
