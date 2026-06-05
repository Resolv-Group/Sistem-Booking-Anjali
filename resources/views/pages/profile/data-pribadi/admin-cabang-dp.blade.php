@extends('components.layouts.app')

@section('title', 'Data Pribadi')

@section('content')
    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32" x-data="{
        photoPreview: '{{ $admin_kolaborasi?->foto
            ? 'data:' . ($admin_kolaborasi->foto_mime ?? 'image/jpeg') . ';base64,' . $admin_kolaborasi->foto
            : asset('images/logo_anjali.jpg') }}',
        gender: '{{ old('jenis_kelamin', $admin_kolaborasi->jenis_kelamin ?? 'L') }}',
        openDropdown: false,
        showPw: false,
        showPwConf: false
    }">

        {{-- 1. TOPBAR GLASSY --}}
        <nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-100 px-6 py-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin-cabang.profile') }}"
                    class="group flex items-center justify-center w-10 h-10 bg-slate-50 hover:bg-teal-50 rounded-xl transition-all duration-300 active:scale-90 border border-slate-100">
                    <i data-lucide="chevron-left" class="w-5 h-5 text-slate-400 group-hover:text-teal-600"></i>
                </a>
                <div class="flex flex-col">
                    <span
                        class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none mb-1">Pengaturan</span>
                    <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none">Data Pribadi</h1>
                </div>
            </div>
        </nav>

        <div class="px-6 pt-8 space-y-8">

            {{-- HEADER INFO --}}
            <div class="px-1 space-y-1">
                <h2 class="text-2xl font-black text-slate-800">Lengkapi Profil</h2>
                <p class="text-xs font-medium text-slate-500">Pastikan data Anda sesuai dengan identitas resmi (KTP).</p>
            </div>

            <form action="{{ route('admin-cabang.profile.update') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="bg-red-100 p-4 rounded-2xl mb-4">
                        <ul class="text-xs text-red-600 font-bold space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- FOTO PROFIL SECTION --}}
                <div class="flex flex-col items-center space-y-4">
                    <div class="relative">
                        <div
                            class="w-28 h-28 rounded-[2.5rem] bg-slate-100 border-4 border-white shadow-xl overflow-hidden flex items-center justify-center">
                            <img :src="photoPreview" class="w-full h-full object-cover"
                                alt="{{ $admin_kolaborasi->nama_karyawan }}">
                        </div>

                        {{-- Tombol Kamera untuk Upload --}}
                        <label for="foto-input"
                            class="absolute -bottom-2 -right-2 w-10 h-10 bg-teal-600 text-white rounded-2xl flex items-center justify-center border-4 border-[#F8FAFB] shadow-lg active:scale-90 transition-all cursor-pointer">
                            <i data-lucide="camera" class="w-5 h-5"></i>
                        </label>

                        <input type="file" name="foto" id="foto-input" class="hidden" accept="image/*"
                            @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result; }; reader.readAsDataURL(file); }">
                    </div>

                    <div class="text-center space-y-1">
                        {{-- <p class="text-xs font-black text-teal-600 uppercase tracking-widest">
                            {{ $admin_kolaborasi->pasien_public_id }}</p> --}}
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Ketuk ikon kamera untuk
                            ubah foto</p>
                    </div>
                </div>

                {{-- CARD 1: IDENTITAS --}}
                <div class="bg-white p-6 rounded-[2.2rem] border border-slate-100 shadow-sm space-y-5">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama
                            Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_karyawan"
                            value="{{ old('nama_karyawan', $admin_kolaborasi->nama_karyawan) }}"
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomor NIK
                            (KTP)</label>
                        <input type="text" name="nik" value="{{ old('nik', $admin_kolaborasi->nik) }}" maxlength="16"
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tgl
                                Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', $admin_kolaborasi->tanggal_lahir ? $admin_kolaborasi->tanggal_lahir->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-700 outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Gender <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <button type="button" @click="openDropdown = !openDropdown"
                                    class="w-full flex items-center justify-between px-4 py-4 bg-slate-50 rounded-2xl text-xs font-bold text-slate-700">
                                    <span x-text="gender === 'L' ? 'Laki-laki' : 'Perempuan'"></span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform"
                                        :class="openDropdown ? 'rotate-180' : ''"></i>
                                </button>
                                <div x-show="openDropdown" @click.away="openDropdown = false" x-cloak
                                    class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-1 overflow-hidden animate-in fade-in zoom-in duration-200">
                                    <button type="button" @click="gender = 'L'; openDropdown = false"
                                        class="w-full text-left px-4 py-3 rounded-xl text-xs font-bold hover:bg-teal-50"
                                        :class="gender === 'L' ? 'text-teal-600 bg-teal-50' : 'text-slate-600'">Laki-laki</button>
                                    <button type="button" @click="gender = 'P'; openDropdown = false"
                                        class="w-full text-left px-4 py-3 rounded-xl text-xs font-bold hover:bg-teal-50"
                                        :class="gender === 'P' ? 'text-teal-600 bg-teal-50' : 'text-slate-600'">Perempuan</button>
                                </div>
                                <input type="hidden" name="jenis_kelamin" :value="gender">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: KONTAK --}}
                <div class="bg-white p-6 rounded-[2.2rem] border border-slate-100 shadow-sm space-y-5">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email
                            Aktif</label>
                        <input type="email" name="email" value="{{ old('email', $admin_kolaborasi->email) }}"
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">No. Telepon /
                            WhatsApp <span class="text-red-500">*</span></label>
                        <input type="tel" name="no_telp" value="{{ old('no_telp', $admin_kolaborasi->no_telp) }}"
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 outline-none">
                    </div>
                </div>

                {{-- CARD 3: ALAMAT --}}
                <div class="bg-white p-6 rounded-[2.2rem] border border-slate-100 shadow-sm space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Alamat
                        Tinggal</label>
                    <textarea name="alamat" rows="3"
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-medium text-slate-600 leading-relaxed outline-none resize-none"
                        placeholder="Contoh: Jl. Mawar No. 123, Surabaya...">{{ old('alamat', $admin_kolaborasi->alamat) }}</textarea>
                </div>

                {{-- CARD 4: KEAMANAN (UBAH PASSWORD) --}}
                <div class="bg-white p-6 rounded-[2.2rem] border border-slate-100 shadow-sm space-y-5">
                    <div class="px-1 border-b border-slate-50 pb-3">
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Ubah Kata Sandi</h3>
                        <p class="text-[10px] font-medium text-slate-400 mt-1 italic">*Kosongkan jika tidak ingin mengubah
                            kata sandi</p>
                    </div>

                    {{-- Password Baru --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kata Sandi
                            Baru</label>
                        <div class="relative">
                            <input :type="showPw ? 'text' : 'password'" name="password"
                                class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none"
                                placeholder="Minimal 8 karakter">
                            <button type="button" @click="showPw = !showPw"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-teal-600 transition-colors">
                                <i :data-lucide="showPw ? 'eye-off' : 'eye'" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Konfirmasi Kata
                            Sandi Baru</label>
                        <div class="relative">
                            <input :type="showPwConf ? 'text' : 'password'" name="password_confirmation"
                                class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none"
                                placeholder="Ulangi kata sandi baru">
                            <button type="button" @click="showPwConf = !showPwConf"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-teal-600 transition-colors">
                                <i :data-lucide="showPwConf ? 'eye-off' : 'eye'" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="space-y-3 pt-4">
                    <button type="submit"
                        class="w-full py-5 bg-teal-800 text-white rounded-3xl text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin-cabang.profile') }}"
                        class="block w-full py-4 text-center text-slate-400 text-[10px] font-black uppercase tracking-widest">
                        Batalkan
                    </a>
                </div>
            </form>
        </div>

        <x-navigation.admin-cabang-navbar active="profile" />
    </x-layouts.mobile-app>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top', // Menampilkan di atas tengah
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-emerald-100 bg-white/90 backdrop-blur-md',
                        title: 'text-sm font-black text-slate-800',
                        htmlContainer: 'text-xs font-medium text-slate-500'
                    }
                });
            });
        </script>
    @endif
@endsection
