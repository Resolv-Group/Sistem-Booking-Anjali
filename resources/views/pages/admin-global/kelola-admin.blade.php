@extends('components.layouts.app')

@section('title', 'Kelola Admin Klinik')

@section('content')

<script>
    window.kelolaAdminBranches = @json($branches);
    window.kelolaAdminSearch = @json($search);
    window.kelolaAdminSearchData = @json($adminSearchData);
</script>

<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32" x-data="{
    showAddModal: false,
    showEditModal: false,
    showResetModal: false,
    openDropdown: null,
    searchBranch: '',
    adminSearch: window.kelolaAdminSearch || '',
    branches: window.kelolaAdminBranches || [],
    adminSearchData: window.kelolaAdminSearchData || [],

    addForm: {
        nama_karyawan: '',
        no_telp: '',
        email: '',
        peran: 'Admin Kolaborasi',
        kolaborasi_id: '',
        tanggal_lahir: '',
        jenis_kelamin: 'L',
        alamat: ''
    },

    editId: null,
    editForm: {
        nama_karyawan: '',
        no_telp: '',
        email: '',
        peran: 'Admin Kolaborasi',
        kolaborasi_id: '',
        alamat: ''
    },

    resetId: null,
    resetTargetName: '',
    resetForm: {
        nama_karyawan: '',
        no_telp: '',
        peran: 'Admin Kolaborasi'
    },

    get editBranchName() {
        const branch = this.branches.find(b => b.id == this.editForm.kolaborasi_id);
        return branch ? branch.nama_kolaborasi : '-- Pilih Cabang --';
    },

    get filteredEditBranches() {
        const q = this.searchBranch.toLowerCase();
        return this.branches.filter(b => b.nama_kolaborasi.toLowerCase().includes(q));
    },

    get addBranchName() {
        const branch = this.branches.find(b => b.id == this.addForm.kolaborasi_id);
        return branch ? branch.nama_kolaborasi : '-- Pilih Cabang --';
    },

    get filteredAddBranches() {
        const q = this.searchBranch.toLowerCase();
        return this.branches.filter(b => b.nama_kolaborasi.toLowerCase().includes(q));
    },

    matchesAdminSearch(nama, telp, cabang) {
        const q = this.adminSearch.trim().toLowerCase();
        if (!q) return true;
        return nama.toLowerCase().includes(q)
            || String(telp).includes(q)
            || (cabang && cabang.toLowerCase().includes(q));
    },

    get filteredAdminCount() {
        const q = this.adminSearch.trim().toLowerCase();
        if (!q) return this.adminSearchData.length;
        return this.adminSearchData.filter(a =>
            a.nama.toLowerCase().includes(q)
            || String(a.telp).includes(q)
            || (a.cabang && a.cabang.toLowerCase().includes(q))
        ).length;
    },

    openEditModal(admin) {
        this.editId = admin.id;
        this.editForm.nama_karyawan = admin.nama_karyawan;
        this.editForm.no_telp = admin.no_telp;
        this.editForm.email = admin.email || '';
        this.editForm.peran = admin.peran;
        this.editForm.kolaborasi_id = admin.kolaborasi_id || '';
        this.editForm.alamat = admin.alamat || '';
        this.openDropdown = null;
        this.searchBranch = '';
        this.showEditModal = true;
    },

    openResetModal(admin) {
        this.resetId = admin.id;
        this.resetTargetName = admin.nama_karyawan;
        this.resetForm.nama_karyawan = '';
        this.resetForm.no_telp = '';
        this.resetForm.peran = admin.peran;
        this.showResetModal = true;
    }
}">

    {{-- TOPBAR GLASSY --}}
    <div class="sticky top-0 z-50 bg-white/85 backdrop-blur-xl border-b border-slate-100/80 shadow-sm">
        <div class="h-1 w-full bg-gradient-to-r from-teal-500 via-teal-700 to-emerald-500"></div>
        <div class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin-global.dashboard') }}" class="p-2 -ml-2 text-slate-400 hover:text-teal-600 hover:bg-slate-50 rounded-xl active:scale-95 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="flex flex-col">
                    <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">
                        Klinik Anjali
                    </span>
                    <h1 class="text-xs font-black text-slate-800 uppercase tracking-wider leading-none">
                        Kelola Admin
                    </h1>
                </div>
            </div>
            {{-- Decorative Icon --}}
            <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center text-teal-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="space-y-6 p-4">

        {{-- SUCCESS/ERROR NOTIFICATIONS --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                x-transition:leave="transition ease-in duration-300"
                class="bg-teal-600 text-white rounded-2xl p-4 text-xs font-black uppercase tracking-widest text-center shadow-lg shadow-teal-700/20">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="bg-rose-500 text-white rounded-2xl p-4 text-xs font-black uppercase tracking-widest text-center shadow-lg shadow-rose-600/20">
                {{ session('error') }}
            </div>
        @endif

        {{-- Laravel Validation Error --}}
        @if ($errors->any())
            <div class="bg-rose-500 text-white rounded-2xl p-4 text-xs font-bold space-y-1 shadow-lg shadow-rose-600/20">
                <p class="uppercase tracking-widest font-black mb-1">Periksa Inputan Anda:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- SEARCH & FILTER SECTION --}}
        <div class="space-y-3">
            <div class="relative group">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-teal-600 transition-colors pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" x-model="adminSearch" placeholder="Cari nama atau no. telp..."
                    class="w-full pl-10 pr-4 py-3 bg-white border border-slate-100 rounded-2xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all shadow-sm">
            </div>
        </div>

        {{-- ADMINS LIST --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.25em]">Daftar Administrator</h3>
                <span class="px-2.5 py-0.5 bg-teal-50 text-teal-700 text-[9px] font-bold rounded-full border border-teal-100"
                    x-text="filteredAdminCount + ' Orang'"></span>
            </div>

            <div class="space-y-3">
                @forelse($admins as $admin)
                    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm space-y-4"
                        x-show="matchesAdminSearch({{ json_encode($admin->nama_karyawan) }}, {{ json_encode($admin->no_telp) }}, {{ json_encode($admin->kolaborasi?->nama_kolaborasi ?? '') }})"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-teal-700 font-bold text-lg">
                                    {{ substr($admin->nama_karyawan, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-slate-800 leading-none">{{ $admin->nama_karyawan }}</h4>
                                    <p class="text-[10px] font-bold text-teal-700 uppercase tracking-wide mt-1.5">{{ $admin->peran }}</p>
                                    @if($admin->kolaborasi_id)
                                        <p class="text-[9px] text-slate-400 mt-1">Cabang: <span class="font-bold text-slate-600">{{ $admin->kolaborasi->nama_kolaborasi }}</span></p>
                                    @else
                                        <p class="text-[9px] text-slate-400 mt-1">Cabang: <span class="font-bold text-teal-600/80">Seluruh Cabang (Pusat)</span></p>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($admin->status_karyawan === 'Aktif')
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase rounded-full tracking-wider border border-emerald-100">Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-500 text-[9px] font-black uppercase rounded-full tracking-wider border border-slate-200">Nonaktif</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-slate-500 pt-1 border-t border-slate-50">
                            <span class="flex items-center gap-1.5 font-medium text-slate-600">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $admin->no_telp }}
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 pt-2">
                            {{-- Edit Button --}}
                            <button @click="openEditModal({{ json_encode($admin) }})"
                                class="py-2.5 bg-slate-50 border border-slate-100 hover:bg-slate-100/70 text-slate-700 rounded-xl text-[9px] font-black uppercase tracking-widest active:scale-95 transition-all text-center">
                                Edit
                            </button>

                            {{-- Reset Password --}}
                            <button @click="openResetModal({{ json_encode($admin) }})"
                                class="py-2.5 bg-amber-50 hover:bg-amber-100/75 border border-amber-100 text-amber-700 rounded-xl text-[9px] font-black uppercase tracking-widest active:scale-95 transition-all text-center">
                                Reset Pwd
                            </button>

                            {{-- Status toggle --}}
                            <form action="{{ route('admin-global.toggle-admin-status', $admin->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2.5 rounded-xl text-[9px] font-black uppercase tracking-widest active:scale-95 transition-all text-center {{ $admin->status_karyawan === 'Aktif' ? 'bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-100/75' : 'bg-emerald-50 border border-emerald-100 text-emerald-700 hover:bg-emerald-100/75' }}">
                                    {{ $admin->status_karyawan === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 space-y-3 bg-white rounded-3xl border border-slate-100 p-6">
                        <div class="w-16 h-16 mx-auto bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-400">Tidak ada admin ditemukan.</p>
                        <p class="text-xs text-slate-300">Coba cari dengan kata kunci lain.</p>
                    </div>
                @endforelse

                @if($admins->count() > 0)
                    <div x-cloak x-show="adminSearch.trim() !== '' && filteredAdminCount === 0"
                        class="text-center py-16 space-y-3 bg-white rounded-3xl border border-slate-100 p-6">
                        <div class="w-16 h-16 mx-auto bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-400">Tidak ada admin ditemukan.</p>
                        <p class="text-xs text-slate-300">Coba cari dengan kata kunci lain.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- FAB for Add Admin --}}
    <div class="fixed bottom-28 left-1/2 -translate-x-1/2 w-full max-w-[430px] pointer-events-none px-6 z-40">
        <div class="flex justify-end pointer-events-auto">
            <button @click="showAddModal = true"
                class="w-14 h-14 bg-teal-950 text-white rounded-2xl flex items-center justify-center shadow-2xl shadow-teal-900/40 active:scale-90 transition-all">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>
    </div>

    {{-- BOTTOM NAVBAR --}}
    <x-navigation.admin-global-navbar active="admin" />

    {{-- MODAL TAMBAH ADMIN --}}
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-[999] flex items-center justify-center p-6"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-md" @click="showAddModal = false"></div>

        {{-- Modal Content --}}
        <div class="relative bg-white/80 border border-white/50 backdrop-blur-2xl rounded-[2.5rem] p-7 max-w-sm w-full shadow-2xl max-h-[85vh] overflow-y-auto space-y-4 custom-scrollbar"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100">

            <div class="text-center space-y-1">
                <h3 class="text-lg font-black text-slate-800 uppercase tracking-wider">Tambah Admin</h3>
                <p class="text-xs text-slate-400">Daftarkan akun administrator sistem baru.</p>
            </div>

            <form action="{{ route('admin-global.store-admin') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Nama --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Karyawan <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_karyawan" x-model="addForm.nama_karyawan" required
                        class="w-full px-4 py-3 bg-white/50 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-sm"
                        placeholder="Contoh: Budi Santoso">
                </div>

                {{-- Phone --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomor Telepon <span class="text-rose-500">*</span></label>
                    <input type="text" name="no_telp" x-model="addForm.no_telp" required
                        class="w-full px-4 py-3 bg-white/50 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-sm"
                        placeholder="Contoh: 08123456789">
                </div>

                {{-- Email --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Email (Opsional)</label>
                    <input type="email" name="email" x-model="addForm.email"
                        class="w-full px-4 py-3 bg-white/50 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-sm"
                        placeholder="Contoh: budi@gmail.com">
                </div>

                {{-- DOB (untuk default password) --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Tanggal Lahir (Password default) <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_lahir" x-model="addForm.tanggal_lahir" required
                        class="w-full px-4 py-3 bg-white/50 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-sm">
                </div>

                {{-- Jenis Kelamin --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <div class="relative" @click.outside="openDropdown = null">
                        <button type="button" @click.stop="openDropdown = openDropdown === 'addGender' ? null : 'addGender'"
                            class="w-full flex items-center justify-between px-4 py-3 bg-white/50 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 transition-all outline-none shadow-sm"
                            :class="openDropdown === 'addGender' ? 'ring-2 ring-teal-400 bg-white' : ''">
                            <span x-text="addForm.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'"></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                :class="openDropdown === 'addGender' ? 'rotate-180' : ''" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openDropdown === 'addGender'" x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-1.5 space-y-0.5">
                            <button type="button" @click="addForm.jenis_kelamin = 'L'; openDropdown = null"
                                class="w-full text-left px-4 py-3 rounded-xl text-xs font-bold transition-colors"
                                :class="addForm.jenis_kelamin === 'L' ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50'">
                                Laki-laki
                            </button>
                            <button type="button" @click="addForm.jenis_kelamin = 'P'; openDropdown = null"
                                class="w-full text-left px-4 py-3 rounded-xl text-xs font-bold transition-colors"
                                :class="addForm.jenis_kelamin === 'P' ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50'">
                                Perempuan
                            </button>
                        </div>
                        <input type="hidden" name="jenis_kelamin" :value="addForm.jenis_kelamin">
                    </div>
                </div>

                <input type="hidden" name="peran" value="Admin Kolaborasi">

                {{-- Kolaborasi ID (Cabang) --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Pilih Cabang <span class="text-rose-500">*</span></label>
                    <div class="relative" @click.outside="openDropdown = null">
                        <button type="button" @click.stop="openDropdown = openDropdown === 'addBranch' ? null : 'addBranch'"
                            class="w-full flex items-center justify-between px-4 py-3 bg-white/50 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 transition-all outline-none shadow-sm"
                            :class="openDropdown === 'addBranch' ? 'ring-2 ring-teal-400 bg-white' : ''">
                            <span x-text="addBranchName"></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                :class="openDropdown === 'addBranch' ? 'rotate-180' : ''" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openDropdown === 'addBranch'" x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-2 space-y-1.5">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" x-model="searchBranch" placeholder="Cari cabang..."
                                    class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 outline-none">
                            </div>
                            <div class="max-h-40 overflow-y-auto space-y-0.5 custom-scrollbar">
                                <template x-for="branch in filteredAddBranches" :key="branch.id">
                                    <button type="button"
                                        @click="addForm.kolaborasi_id = branch.id; openDropdown = null; searchBranch = ''"
                                        class="w-full text-left px-4 py-3 rounded-xl text-xs font-bold transition-colors"
                                        :class="addForm.kolaborasi_id == branch.id ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50'">
                                        <span x-text="branch.nama_kolaborasi"></span>
                                    </button>
                                </template>
                                <div x-show="filteredAddBranches.length === 0"
                                    class="px-4 py-3 text-xs font-bold text-slate-400 text-center">
                                    Cabang tidak ditemukan
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="kolaborasi_id" :value="addForm.kolaborasi_id">
                </div>

                {{-- Alamat --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Alamat (Opsional)</label>
                    <textarea name="alamat" x-model="addForm.alamat" rows="2"
                        class="w-full px-4 py-3 bg-white/50 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none resize-none shadow-sm"
                        placeholder="Contoh: Jl. Mawar No. 10"></textarea>
                </div>

                <div class="space-y-2 pt-2">
                    <button type="submit"
                        class="w-full py-4 bg-teal-800 text-white rounded-xl text-xs font-black uppercase tracking-widest active:scale-95 transition-all shadow-lg shadow-teal-850/20">
                        Tambah Admin
                    </button>
                    <button type="button" @click="showAddModal = false"
                        class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-xl text-xs font-black uppercase tracking-widest active:scale-95 transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT ADMIN --}}
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[999] flex items-center justify-center p-6"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-md" @click="showEditModal = false"></div>

        {{-- Modal Content --}}
        <div class="relative bg-white/80 border border-white/50 backdrop-blur-2xl rounded-[2.5rem] p-7 max-w-sm w-full shadow-2xl max-h-[85vh] overflow-y-auto space-y-4 custom-scrollbar"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100">

            <div class="text-center space-y-1">
                <h3 class="text-lg font-black text-slate-800 uppercase tracking-wider">Edit Data Admin</h3>
                <p class="text-xs text-slate-400">Ganti informasi untuk admin ini.</p>
            </div>

            <form :action="'/admin-global/kelola-admin/' + editId" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Nama --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Karyawan <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_karyawan" x-model="editForm.nama_karyawan" required
                        class="w-full px-4 py-3 bg-white/50 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-sm"
                        placeholder="Contoh: Budi Santoso">
                </div>

                {{-- Phone --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomor Telepon <span class="text-rose-500">*</span></label>
                    <input type="text" name="no_telp" x-model="editForm.no_telp" required
                        class="w-full px-4 py-3 bg-white/50 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-sm"
                        placeholder="Contoh: 08123456789">
                </div>

                {{-- Email --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Email (Opsional)</label>
                    <input type="email" name="email" x-model="editForm.email"
                        class="w-full px-4 py-3 bg-white/50 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none shadow-sm"
                        placeholder="Contoh: budi@gmail.com">
                </div>

                <input type="hidden" name="peran" value="Admin Kolaborasi">

                {{-- Kolaborasi ID (Cabang) --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Pilih Cabang <span class="text-rose-500">*</span></label>
                    <div class="relative" @click.outside="openDropdown = null">
                        <button type="button" @click.stop="openDropdown = openDropdown === 'editBranch' ? null : 'editBranch'"
                            class="w-full flex items-center justify-between px-4 py-3 bg-white/50 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 transition-all outline-none shadow-sm"
                            :class="openDropdown === 'editBranch' ? 'ring-2 ring-teal-400 bg-white' : ''">
                            <span x-text="editBranchName"></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                :class="openDropdown === 'editBranch' ? 'rotate-180' : ''" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openDropdown === 'editBranch'" x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-2 space-y-1.5">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" x-model="searchBranch" placeholder="Cari cabang..."
                                    class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 outline-none">
                            </div>
                            <div class="max-h-40 overflow-y-auto space-y-0.5 custom-scrollbar">
                                <template x-for="branch in filteredEditBranches" :key="branch.id">
                                    <button type="button"
                                        @click="editForm.kolaborasi_id = branch.id; openDropdown = null; searchBranch = ''"
                                        class="w-full text-left px-4 py-3 rounded-xl text-xs font-bold transition-colors"
                                        :class="editForm.kolaborasi_id == branch.id ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50'">
                                        <span x-text="branch.nama_kolaborasi"></span>
                                    </button>
                                </template>
                                <div x-show="filteredEditBranches.length === 0"
                                    class="px-4 py-3 text-xs font-bold text-slate-400 text-center">
                                    Cabang tidak ditemukan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="kolaborasi_id" :value="editForm.kolaborasi_id">

                {{-- Alamat --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Alamat (Opsional)</label>
                    <textarea name="alamat" x-model="editForm.alamat" rows="2"
                        class="w-full px-4 py-3 bg-white/50 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none resize-none shadow-sm"
                        placeholder="Contoh: Jl. Mawar No. 10"></textarea>
                </div>

                <div class="space-y-2 pt-2">
                    <button type="submit"
                        class="w-full py-4 bg-teal-800 text-white rounded-xl text-xs font-black uppercase tracking-widest active:scale-95 transition-all shadow-lg shadow-teal-850/20">
                        Simpan Perubahan
                    </button>
                    <button type="button" @click="showEditModal = false"
                        class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-xl text-xs font-black uppercase tracking-widest active:scale-95 transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL RESET PASSWORD CONFIRMATION (With verification fields) --}}
    <div x-show="showResetModal" x-cloak class="fixed inset-0 z-[999] flex items-center justify-center p-6"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-md" @click="showResetModal = false"></div>

        {{-- Modal Content --}}
        <div class="relative bg-white/80 border border-white/50 backdrop-blur-2xl rounded-[2.5rem] p-7 max-w-sm w-full shadow-2xl space-y-4"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100">

            <div class="text-center space-y-2">
                <div class="w-12 h-12 mx-auto bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-base font-black text-slate-800 uppercase tracking-wider">Verifikasi Reset Password</h3>
                <p class="text-xs text-slate-400">Ketik ulang nama, telepon, dan peran untuk admin <strong class="text-slate-700" x-text="resetTargetName"></strong>.</p>
            </div>

            <form :action="'/admin-global/kelola-admin/' + resetId + '/reset-password'" method="POST" class="space-y-3">
                @csrf

                {{-- Verify Nama --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Verifikasi Nama <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_karyawan" x-model="resetForm.nama_karyawan" required
                        class="w-full px-4 py-2.5 bg-white/50 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 focus:ring-4 focus:ring-amber-500/10 focus:bg-white transition-all outline-none shadow-sm"
                        placeholder="Ketik nama lengkap...">
                </div>

                {{-- Verify Phone --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Verifikasi No. Telp <span class="text-rose-500">*</span></label>
                    <input type="text" name="no_telp" x-model="resetForm.no_telp" required
                        class="w-full px-4 py-2.5 bg-white/50 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 focus:ring-4 focus:ring-amber-500/10 focus:bg-white transition-all outline-none shadow-sm"
                        placeholder="Ketik nomor telepon...">
                </div>

                <input type="hidden" name="peran" value="Admin Kolaborasi">

                <div class="space-y-2 pt-2">
                    <button type="submit"
                        class="w-full py-3.5 bg-amber-500 text-white rounded-xl text-xs font-black uppercase tracking-widest active:scale-95 transition-all shadow-lg shadow-amber-500/25">
                        Konfirmasi Reset Password
                    </button>
                    <button type="button" @click="showResetModal = false"
                        class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-xl text-xs font-black uppercase tracking-widest active:scale-95 transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.mobile-app>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }
</style>

@endsection
