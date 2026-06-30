@extends('components.layouts.app')

@section('title', 'Tambah Karyawan Baru')

@section('content')

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{
        namaKaryawan: '{{ old('nama_karyawan', '') }}',
        roleKaryawan: '{{ old('peran', 'Terapis') }}',
        statusKaryawan: '{{ old('status_karyawan', 'Aktif') }}',
        jenisKelamin: '{{ old('jenis_kelamin', 'L') }}',
        photoPreview: null,
        openDropdown: null
    }">

        {{-- 1. TOPBAR --}}
        <div
            class="px-6 py-5 flex justify-between items-center bg-white/90 backdrop-blur-xl sticky top-0 z-50 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin-global.karyawan', $kolaborasi->id) }}"
                    class="p-1 -ml-1 text-slate-400 hover:text-teal-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-lg font-bold text-teal-800 uppercase tracking-widest leading-none">
                    {{ $kolaborasi->nama_kolaborasi }}</h1>
            </div>
        </div>

        <div class="px-6 pt-8 pb-32 space-y-8">

            {{-- 2. TITLE SECTION --}}
            <div class="space-y-3 px-1">
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Tambah Karyawan</h2>
                <p class="text-[10px] font-bold text-teal-600 uppercase tracking-widest">{{ $kolaborasi->nama_kolaborasi }}
                </p>
                <p class="text-sm font-medium text-slate-500 leading-relaxed">
                    Lengkapi informasi karyawan di bawah ini. Akun login user akan dibuat secara otomatis.
                </p>
            </div>

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="text-xs font-bold text-red-600">• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- 3. FORM --}}
            <form action="{{ route('admin-global.karyawan.store', $kolaborasi->id) }}" method="POST"
                enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Card 1: Identitas Utama --}}
                <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm space-y-6">

                    {{-- Foto Profil --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Foto
                            Profil</label>
                        <div class="flex items-center gap-4">
                            <div
                                class="w-16 h-16 rounded-2xl bg-slate-50 border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden shrink-0">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!photoPreview">
                                    <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </template>
                            </div>
                            <input type="file" name="foto" accept="image/*" class="hidden" id="foto-input"
                                @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result; }; reader.readAsDataURL(file); }">
                            <button type="button" onclick="document.getElementById('foto-input').click()"
                                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black uppercase tracking-wider rounded-xl border border-slate-200 transition active:scale-95">
                                Pilih Foto
                            </button>
                        </div>
                    </div>

                    {{-- Nama --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Nama
                            Lengkap</label>
                        <input type="text" name="nama_karyawan" x-model="namaKaryawan" value="{{ old('nama_karyawan') }}"
                            required
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner"
                            placeholder="Contoh: Budi Santoso">
                    </div>

                    {{-- NIK --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">NIK (16
                            Digit)</label>
                        <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" minlength="16"
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner"
                            placeholder="Masukkan 16 digit NIK">
                    </div>
                </div>

                {{-- Card 2: Kontak & Login Info --}}
                <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm space-y-6">

                    {{-- No Telepon (Login ID) --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">No. Telepon
                            (Digunakan untuk Login)</label>
                        <input type="tel" name="no_telp" value="{{ old('no_telp') }}" required
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner"
                            placeholder="Contoh: 08123456789">
                    </div>

                    {{-- Email --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Email
                            (Opsional)</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner"
                            placeholder="Contoh: budi@gmail.com">
                    </div>
                </div>

                {{-- Card 3: Biodata --}}
                <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm space-y-6">

                    {{-- Tanggal Lahir & Gender --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Tgl
                                Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
                                class="w-full px-4 py-3.5 bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20">
                            <p class="text-[8px] font-bold text-slate-400 leading-tight">Digunakan sebagai password awal
                                (Format: DD-MM-YYYY)</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Jenis
                                Kelamin</label>
                            <div class="relative" @click.outside="openDropdown = null">
                                <button type="button" @click.stop="openDropdown = openDropdown === 'gender' ? null : 'gender'"
                                    class="w-full flex items-center justify-between px-4 py-3.5 bg-slate-50 rounded-xl text-xs font-bold text-slate-700 transition-all outline-none"
                                    :class="openDropdown === 'gender' ? 'ring-2 ring-teal-400 bg-white shadow-md' : ''">
                                    <span x-text="jenisKelamin === 'L' ? 'Laki-laki' : 'Perempuan'"></span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                        :class="openDropdown === 'gender' ? 'rotate-180' : ''" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="openDropdown === 'gender'" x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                    class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-1.5 space-y-0.5"
                                    x-cloak>
                                    <button type="button" @click="jenisKelamin = 'L'; openDropdown = null"
                                        class="w-full text-left px-4 py-3 rounded-xl text-xs font-bold transition-colors"
                                        :class="jenisKelamin === 'L' ? 'bg-teal-50 text-teal-700' :
                                            'text-slate-600 hover:bg-slate-50'">
                                        Laki-laki
                                    </button>
                                    <button type="button" @click="jenisKelamin = 'P'; openDropdown = null"
                                        class="w-full text-left px-4 py-3 rounded-xl text-xs font-bold transition-colors"
                                        :class="jenisKelamin === 'P' ? 'bg-teal-50 text-teal-700' :
                                            'text-slate-600 hover:bg-slate-50'">
                                        Perempuan
                                    </button>
                                </div>
                                <input type="hidden" name="jenis_kelamin" :value="jenisKelamin">
                            </div>
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Alamat
                            Rumah</label>
                        <textarea name="alamat" rows="4"
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-medium text-slate-600 leading-relaxed focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner resize-none"
                            placeholder="Alamat lengkap...">{{ old('alamat') }}</textarea>
                    </div>
                </div>

                {{-- Card 4: Peran & Status Kerja --}}
                <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm space-y-6">

                    {{-- Peran --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Peran
                            Kerja</label>
                        <div class="relative" @click.outside="openDropdown = null">
                            <button type="button" @click.stop="openDropdown = openDropdown === 'peran' ? null : 'peran'"
                                class="w-full flex items-center justify-between px-5 py-4 bg-slate-50 rounded-2xl text-sm font-bold text-slate-700 transition-all outline-none"
                                :class="openDropdown === 'peran' ? 'ring-2 ring-teal-400 bg-white shadow-md' : ''">
                                <span x-text="roleKaryawan"></span>
                                <svg class="w-5 h-5 text-slate-400 transition-transform duration-200"
                                    :class="openDropdown === 'peran' ? 'rotate-180' : ''" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="openDropdown === 'peran'" x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-1.5 space-y-0.5"
                                x-cloak>
                                @foreach (['Terapis', 'Admin Kolaborasi', 'Admin Global'] as $role)
                                    <button type="button" @click="roleKaryawan = '{{ $role }}'; openDropdown = null"
                                        class="w-full text-left px-4 py-3 rounded-xl text-sm font-bold transition-colors"
                                        :class="roleKaryawan === '{{ $role }}' ? 'bg-teal-50 text-teal-700' :
                                            'text-slate-600 hover:bg-slate-50'">
                                        {{ $role }}
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="peran" :value="roleKaryawan">
                        </div>
                    </div>

                    {{-- Tanggal Bergabung --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Tanggal
                            Bergabung</label>
                        <input type="date" name="tanggal_bergabung"
                            value="{{ old('tanggal_bergabung', date('Y-m-d')) }}"
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all">
                    </div>

                    {{-- Status --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Status
                            Karyawan</label>
                        <div class="relative" @click.outside="openDropdown = null">
                            <button type="button"
                                @click.stop="openDropdown = openDropdown === 'status' ? null : 'status'"
                                class="w-full flex items-center justify-between px-5 py-4 bg-slate-50 rounded-2xl text-sm font-bold text-slate-700 transition-all outline-none"
                                :class="openDropdown === 'status' ? 'ring-2 ring-teal-400 bg-white shadow-md' : ''">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full"
                                        :class="{
                                            'bg-emerald-500': statusKaryawan === 'Aktif',
                                            'bg-slate-400': statusKaryawan === 'Tidak Aktif'
                                        }">
                                    </div>
                                    <span x-text="statusKaryawan"></span>
                                </div>
                                <svg class="w-5 h-5 text-slate-400 transition-transform duration-200"
                                    :class="openDropdown === 'status' ? 'rotate-180' : ''" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="openDropdown === 'status'" x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-1.5 space-y-0.5"
                                x-cloak>
                                @foreach (['Aktif', 'Tidak Aktif'] as $status)
                                    <button type="button" @click="statusKaryawan = '{{ $status }}'; openDropdown = null"
                                        class="w-full text-left px-4 py-3 rounded-xl text-sm font-bold transition-colors flex items-center gap-2"
                                        :class="statusKaryawan === '{{ $status }}' ? 'bg-teal-50 text-teal-700' :
                                            'text-slate-600 hover:bg-slate-50'">
                                        <div class="w-2 h-2 rounded-full {{ $status === 'Aktif' ? 'bg-emerald-500' : 'bg-slate-400' }}"></div>
                                        <span>{{ $status }}</span>
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="status_karyawan" :value="statusKaryawan">
                        </div>
                    </div>
                </div>

                {{-- 4. FOOTER ACTIONS --}}
                <div class="space-y-3 pt-4">
                    <button type="submit"
                        class="w-full py-5 bg-teal-800 text-white rounded-[1.5rem] text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 active:scale-95 transition-all">
                        Daftarkan Karyawan
                    </button>
                    <a href="{{ route('admin-global.karyawan', $kolaborasi->id) }}"
                        class="block w-full py-4 bg-slate-200/50 text-slate-500 rounded-[1.5rem] text-sm font-black uppercase tracking-[0.2em] active:scale-95 transition-all text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- BOTTOM NAVBAR --}}
        <x-navigation.admin-global-navbar active="cabang" />

    </x-layouts.mobile-app>

@endsection
