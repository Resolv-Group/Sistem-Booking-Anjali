@extends('components.layouts.app')

@section('title', 'Detail & Edit Karyawan')

<script>
    window.branches = @json($branches);
</script>

@section('content')

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{
        showDeleteModal: false,
        photoPreview: '{{ $karyawan->foto_path ? Storage::url($karyawan->foto_path) : '' }}',
        peran: '{{ $karyawan->peran }}',
        statusKaryawan: '{{ $karyawan->status_karyawan }}',
        jenisKelamin: '{{ $karyawan->jenis_kelamin }}',
        branchId: '{{ $karyawan->kolaborasi_id }}',
        openDropdown: null,
        searchBranch: '',
        branches: window.branches,
        get selectedBranchName() {
            let b = this.branches.find(i => i.id == this.branchId);
            return b ? b.nama_kolaborasi : 'Pilih Cabang';
        },
        get filteredBranches() {
            return this.branches.filter(i =>
                i.nama_kolaborasi.toLowerCase().includes(this.searchBranch.toLowerCase())
            );
        }
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
                <h1 class="text-sm font-bold text-teal-800 uppercase tracking-widest leading-none">Rumah Terapi Anjali</h1>
            </div>
            <img src="https://i.pravatar.cc/100?u=admin"
                class="w-10 h-10 rounded-xl border-2 border-orange-100 p-0.5 object-cover">
        </div>

        <div class="px-6 pt-8 pb-32 space-y-8">

            {{-- 2. TITLE SECTION --}}
            <div class="space-y-3 px-1">
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Detail Karyawan</h2>
                <p class="text-[10px] font-bold text-teal-600 uppercase tracking-widest">{{ $karyawan->nama_karyawan }}</p>
                <p class="text-sm font-medium text-slate-500 leading-relaxed">
                    Kelola data profil, peran, status, serta pemetaan cabang untuk karyawan ini.
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
            <form action="{{ route('admin-global.karyawan.update', [$kolaborasi->id, $karyawan->id]) }}" method="POST"
                enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

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
                                    <span
                                        class="text-teal-700 font-black text-lg">{{ substr($karyawan->nama_karyawan, 0, 2) }}</span>
                                </template>
                            </div>
                            <input type="file" name="foto" accept="image/*" class="hidden" id="foto-input"
                                @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result; }; reader.readAsDataURL(file); }">
                            <button type="button" onclick="document.getElementById('foto-input').click()"
                                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black uppercase tracking-wider rounded-xl border border-slate-200 transition active:scale-95">
                                Ubah Foto
                            </button>
                        </div>
                    </div>

                    {{-- Kode Karyawan (Read-only) --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Kode
                            Karyawan</label>
                        <input type="text" value="{{ $karyawan->kode_karyawan }}" disabled
                            class="w-full px-5 py-4 bg-slate-100 border-none rounded-2xl text-sm font-bold text-slate-400 outline-none cursor-not-allowed">
                    </div>

                    {{-- Nama --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Nama
                            Lengkap</label>
                        <input type="text" name="nama_karyawan"
                            value="{{ old('nama_karyawan', $karyawan->nama_karyawan) }}" required
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner"
                            placeholder="Contoh: Budi Santoso">
                    </div>

                    {{-- NIK --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">NIK (16
                            Digit)</label>
                        <input type="text" name="nik" value="{{ old('nik', $karyawan->nik) }}" maxlength="16"
                            minlength="16"
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
                        <input type="tel" name="no_telp" value="{{ old('no_telp', $karyawan->no_telp) }}" required
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner"
                            placeholder="Contoh: 08123456789">
                    </div>

                    {{-- Email --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Email
                            (Opsional)</label>
                        <input type="email" name="email" value="{{ old('email', $karyawan->email) }}"
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner"
                            placeholder="Contoh: budi@gmail.com">
                    </div>
                </div>

                {{-- Card 3: Biodata --}}
                <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm space-y-6">

                    {{-- Tanggal Lahir & Gender --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Jenis
                            Kelamin</label>
                        <div class="relative" x-data @click.outside="openDropdown = null">
                            <button type="button" @click.stop="openDropdown = openDropdown === 'gender' ? null : 'gender'"
                                class="w-full flex items-center justify-between px-4 py-3.5 bg-slate-50 rounded-xl text-xs font-bold text-slate-700 transition-all"
                                :class="openDropdown === 'gender' ? 'ring-2 ring-teal-400 bg-white' : ''">
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
                                x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-1.5 space-y-0.5">
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


                    {{-- Alamat --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Alamat
                            Rumah</label>
                        <textarea name="alamat" rows="4"
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-medium text-slate-600 leading-relaxed focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-inner resize-none"
                            placeholder="Alamat lengkap...">{{ old('alamat', $karyawan->alamat) }}</textarea>
                    </div>
                </div>

                {{-- Card 4: Peran & Status Kerja & Pemetaan Cabang --}}
                <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm space-y-6">

                    {{-- Pemetaan Cabang (Mapping) --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Pemetaan
                            Cabang / Kolaborasi</label>
                        <div class="relative" x-data @click.outside="openDropdown = null">
                            <button type="button"
                                @click.stop="openDropdown = openDropdown === 'branch' ? null : 'branch'"
                                class="w-full flex items-center justify-between px-5 py-4 bg-slate-50 rounded-2xl text-sm font-bold text-slate-700 transition-all"
                                :class="openDropdown === 'branch' ? 'ring-2 ring-teal-400 bg-white' : ''">
                                <span x-text="selectedBranchName"></span>
                                <svg class="w-5 h-5 text-slate-400 transition-transform duration-200"
                                    :class="openDropdown === 'branch' ? 'rotate-180' : ''" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="openDropdown === 'branch'" x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-2 space-y-1.5">
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <input type="text" x-model="searchBranch" placeholder="Cari cabang..."
                                        class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 outline-none">
                                </div>
                                <div class="max-h-48 overflow-y-auto space-y-0.5">
                                    <template x-for="branch in filteredBranches" :key="branch.id">
                                        <button type="button"
                                            @click="branchId = branch.id; openDropdown = null; searchBranch = ''"
                                            class="w-full text-left px-4 py-3 rounded-xl text-xs font-bold transition-colors"
                                            :class="branchId == branch.id ? 'bg-teal-50 text-teal-700' :
                                                'text-slate-600 hover:bg-slate-50'">
                                            <span x-text="branch.nama_kolaborasi"></span>
                                        </button>
                                    </template>
                                    <div x-show="filteredBranches.length === 0"
                                        class="px-4 py-3 text-xs font-bold text-slate-400 text-center">
                                        Cabang tidak ditemukan
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="kolaborasi_id" :value="branchId">
                        </div>
                        <p class="text-[9px] font-bold text-teal-600 mt-1 leading-tight">Ubah opsi ini untuk memindahkan
                            karyawan ke cabang lain.</p>
                    </div>

                    {{-- Peran --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Peran
                            Kerja</label>
                        <div class="relative" x-data @click.outside="openDropdown = null">
                            <button type="button" @click.stop="openDropdown = openDropdown === 'peran' ? null : 'peran'"
                                class="w-full flex items-center justify-between px-5 py-4 bg-slate-50 rounded-2xl text-sm font-bold text-slate-700 transition-all"
                                :class="openDropdown === 'peran' ? 'ring-2 ring-teal-400 bg-white' : ''">
                                <span x-text="peran"></span>
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
                                class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-1.5 space-y-0.5">
                                @foreach (['Terapis', 'Admin Kolaborasi', 'Admin Global'] as $p)
                                    <button type="button" @click="peran = '{{ $p }}'; openDropdown = null"
                                        class="w-full text-left px-4 py-3 rounded-xl text-sm font-bold transition-colors"
                                        :class="peran === '{{ $p }}' ? 'bg-teal-50 text-teal-700' :
                                            'text-slate-600 hover:bg-slate-50'">
                                        {{ $p }}
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="peran" :value="peran">
                        </div>
                    </div>

                    {{-- Tanggal Bergabung --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Tanggal
                            Bergabung</label>
                        <input type="date" name="tanggal_bergabung"
                            value="{{ old('tanggal_bergabung', $karyawan->tanggal_bergabung ? $karyawan->tanggal_bergabung->format('Y-m-d') : '') }}"
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all">
                    </div>

                    {{-- Status --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Status
                            Karyawan</label>
                        <div class="relative">
                            <button type="button"
                                @click.stop="openDropdown = openDropdown === 'status' ? null : 'status'"
                                class="w-full flex items-center justify-between px-5 py-4 bg-slate-50 rounded-2xl text-sm font-bold text-slate-700 transition-all"
                                :class="openDropdown === 'status' ? 'ring-2 ring-teal-400 bg-white' : ''">
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
                                class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-1.5 space-y-0.5">
                                @foreach ([['value' => 'Aktif', 'dot' => 'bg-emerald-500', 'label' => 'Aktif', 'desc' => 'Karyawan aktif bekerja'], ['value' => 'Tidak Aktif', 'dot' => 'bg-slate-400', 'label' => 'Tidak Aktif', 'desc' => 'Sementara tidak aktif']] as $s)
                                    <button type="button"
                                        @click="statusKaryawan = '{{ $s['value'] }}'; openDropdown = null"
                                        class="w-full text-left px-4 py-3 rounded-xl transition-colors flex items-center gap-3"
                                        :class="statusKaryawan === '{{ $s['value'] }}' ? 'bg-teal-50' : 'hover:bg-slate-50'">
                                        <div class="w-2 h-2 rounded-full shrink-0 {{ $s['dot'] }}"></div>
                                        <div>
                                            <p class="text-xs font-bold"
                                                :class="statusKaryawan === '{{ $s['value'] }}' ? 'text-teal-700' :
                                                    'text-slate-700'">
                                                {{ $s['label'] }}
                                            </p>
                                            <p class="text-[10px] font-medium text-slate-400">{{ $s['desc'] }}</p>
                                        </div>
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
                        Simpan Perubahan
                    </button>
                    <button type="button" @click="showDeleteModal = true"
                        class="w-full py-4 bg-red-50 text-red-500 rounded-[1.5rem] text-sm font-black uppercase tracking-[0.2em] active:scale-95 transition-all border border-red-100">
                        Hapus Karyawan
                    </button>
                    <a href="{{ route('admin-global.karyawan', $kolaborasi->id) }}"
                        class="block w-full py-4 bg-slate-200/50 text-slate-500 rounded-[1.5rem] text-sm font-black uppercase tracking-[0.2em] active:scale-95 transition-all text-center">
                        Batal
                    </a>
                </div>
            </form>

            {{-- Delete Confirmation Modal --}}
            <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[999] flex items-center justify-center p-6"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                {{-- Overlay --}}
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showDeleteModal = false"></div>

                {{-- Modal --}}
                <div class="relative bg-white rounded-[2rem] p-8 max-w-sm w-full shadow-2xl space-y-6"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100">

                    <div class="text-center space-y-3">
                        <div class="w-16 h-16 mx-auto bg-red-50 rounded-2xl flex items-center justify-center text-red-500">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-slate-800">Hapus Karyawan?</h3>
                        <p class="text-sm text-slate-500">Karyawan <strong>{{ $karyawan->nama_karyawan }}</strong> beserta
                            akun user-nya akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.</p>
                    </div>

                    <div class="space-y-3">
                        <form action="{{ route('admin-global.karyawan.destroy', [$kolaborasi->id, $karyawan->id]) }}"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full py-4 bg-red-500 text-white rounded-2xl text-sm font-black uppercase tracking-widest active:scale-95 transition-all shadow-lg shadow-red-500/20">
                                Ya, Hapus
                            </button>
                        </form>
                        <button @click="showDeleteModal = false"
                            class="w-full py-4 bg-slate-100 text-slate-500 rounded-2xl text-sm font-black uppercase tracking-widest active:scale-95 transition-all">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTTOM NAVBAR --}}
        <x-navigation.admin-global-navbar active="cabang" />

    </x-layouts.mobile-app>

@endsection
