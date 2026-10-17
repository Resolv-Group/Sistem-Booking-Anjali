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
                {{-- Right Slot: Brand Logo --}}
                <div class="w-8 h-8 rounded-lg overflow-hidden border border-slate-100 shadow-sm flex items-center justify-center bg-white shrink-0">
                    <img src="{{ asset('images/logo_anjali.jpg') }}" alt="Logo Anjali" class="w-full h-full object-cover">
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
                                Reset Pass
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
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-[999] flex items-center justify-center p-4 sm:p-6"
        x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-md" @click="showAddModal = false"></div>

        {{-- Modal Content --}}
        <div class="relative bg-white border border-slate-100 rounded-[2.5rem] p-6 sm:p-7 max-w-md w-full shadow-2xl max-h-[90vh] overflow-hidden flex flex-col"
            x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            {{-- Modal Header --}}
            <div class="relative z-10 flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-teal-50 border border-teal-100 text-teal-700 flex items-center justify-center shadow-sm shrink-0"
                        style="background-color: #f0fdfa; border-color: #ccfbf1; color: #0f766e;">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-800 tracking-tight leading-snug">Tambah Administrator</h3>
                        <p class="text-xs font-medium text-slate-400">Daftarkan akun admin cabang baru</p>
                    </div>
                </div>
                <button type="button" @click="showAddModal = false"
                    class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center transition active:scale-95 shrink-0"
                    style="background-color: #f1f5f9; color: #64748b;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Form Body (Scrollable) --}}
            <form action="{{ route('admin-global.store-admin') }}" method="POST" class="relative z-10 space-y-4 pt-4 overflow-y-auto custom-scrollbar pr-1 flex-1">
                @csrf
                <input type="hidden" name="peran" value="Admin Kolaborasi">

                {{-- Nama Lengkap --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider flex items-center gap-1.5 ml-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-600"></span>
                        Nama Karyawan <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" name="nama_karyawan" x-model="addForm.nama_karyawan" required
                            class="w-full pl-10 pr-4 py-3 bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-semibold text-slate-700 placeholder-slate-400 focus:border-teal-600 transition-all outline-none shadow-sm"
                            placeholder="Contoh: Budi Santoso">
                    </div>
                </div>

                {{-- Grid: No. Telepon & Email --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- No Telepon --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider flex items-center gap-1.5 ml-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-600"></span>
                            No. Telepon <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <input type="text" name="no_telp" x-model="addForm.no_telp" required
                                class="w-full pl-10 pr-4 py-3 bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-semibold text-slate-700 placeholder-slate-400 focus:border-teal-600 transition-all outline-none shadow-sm"
                                placeholder="08123456789">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider flex items-center gap-1.5 ml-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                            Email (Opsional)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="email" name="email" x-model="addForm.email"
                                class="w-full pl-10 pr-4 py-3 bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-semibold text-slate-700 placeholder-slate-400 focus:border-teal-600 transition-all outline-none shadow-sm"
                                placeholder="admin@anjali.id">
                        </div>
                    </div>
                </div>

                {{-- Grid: Tanggal Lahir & Jenis Kelamin --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- DOB --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider flex items-center gap-1.5 ml-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-600"></span>
                            Tgl Lahir <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal_lahir" x-model="addForm.tanggal_lahir" required
                            class="w-full px-3.5 py-3 bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-semibold text-slate-700 focus:border-teal-600 transition-all outline-none shadow-sm">
                        <div class="mt-1 flex items-center gap-1.5 text-[9.5px] font-semibold text-slate-400 ml-1">
                            <svg class="w-3 h-3 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            <span>Pass default: <span class="font-bold text-teal-700">DD-MM-YYYY</span></span>
                        </div>
                    </div>

                    {{-- Jenis Kelamin Segmented Toggle --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider flex items-center gap-1.5 ml-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-600"></span>
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 p-1 bg-slate-100 border border-slate-200 rounded-2xl gap-1">
                            <button type="button" @click="addForm.jenis_kelamin = 'L'"
                                class="py-2.5 rounded-xl text-xs font-black transition-all text-center"
                                :class="addForm.jenis_kelamin === 'L' ? 'bg-teal-800 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                :style="addForm.jenis_kelamin === 'L' ? 'background-color: #115e59; color: #ffffff;' : ''">
                                Laki-laki
                            </button>
                            <button type="button" @click="addForm.jenis_kelamin = 'P'"
                                class="py-2.5 rounded-xl text-xs font-black transition-all text-center"
                                :class="addForm.jenis_kelamin === 'P' ? 'bg-teal-800 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                :style="addForm.jenis_kelamin === 'P' ? 'background-color: #115e59; color: #ffffff;' : ''">
                                Perempuan
                            </button>
                        </div>
                        <input type="hidden" name="jenis_kelamin" :value="addForm.jenis_kelamin">
                    </div>
                </div>

                {{-- Kolaborasi ID (Cabang) Dropdown --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider flex items-center gap-1.5 ml-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-600"></span>
                        Cabang Penugasan <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative" @click.outside="openDropdown = null">
                        <button type="button" @click.stop="openDropdown = openDropdown === 'addBranch' ? null : 'addBranch'"
                            class="w-full flex items-center justify-between px-4 py-3 bg-slate-50/70 hover:bg-white border border-slate-200 rounded-2xl text-xs font-semibold text-slate-700 transition-all outline-none shadow-sm hover:border-teal-300"
                            :class="openDropdown === 'addBranch' ? 'border-teal-600 bg-white' : ''">
                            <div class="flex items-center gap-2.5 truncate">
                                <svg class="w-4 h-4 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span x-text="addBranchName" class="truncate" :class="!addForm.kolaborasi_id ? 'text-slate-400' : 'text-slate-800 font-bold'"></span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0"
                                :class="openDropdown === 'addBranch' ? 'rotate-180 text-teal-600' : ''" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="openDropdown === 'addBranch'" x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 -translate-y-2 scale-98"
                            class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 p-2.5 space-y-2">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" x-model="searchBranch" placeholder="Cari nama cabang..."
                                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs font-bold text-slate-700 outline-none focus:bg-white focus:border-teal-500">
                            </div>
                            <div class="max-h-40 overflow-y-auto space-y-1 custom-scrollbar pr-1">
                                <template x-for="branch in filteredAddBranches" :key="branch.id">
                                    <button type="button"
                                        @click="addForm.kolaborasi_id = branch.id; openDropdown = null; searchBranch = ''"
                                        class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-between group"
                                        :class="addForm.kolaborasi_id == branch.id ? 'bg-teal-50 text-teal-700 border border-teal-100' : 'text-slate-600 hover:bg-slate-50'">
                                        <span x-text="branch.nama_kolaborasi"></span>
                                        <svg x-show="addForm.kolaborasi_id == branch.id" class="w-4 h-4 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </template>
                                <div x-show="filteredAddBranches.length === 0"
                                    class="px-4 py-3 text-xs font-semibold text-slate-400 text-center">
                                    Cabang tidak ditemukan
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="kolaborasi_id" :value="addForm.kolaborasi_id">
                </div>

                {{-- Alamat --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider flex items-center gap-1.5 ml-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                        Alamat (Opsional)
                    </label>
                    <textarea name="alamat" x-model="addForm.alamat" rows="2"
                        class="w-full px-4 py-3 bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-semibold text-slate-700 placeholder-slate-400 focus:border-teal-600 transition-all outline-none resize-none shadow-sm"
                        placeholder="Contoh: Jl. Mawar No. 10"></textarea>
                </div>

                {{-- Action Buttons --}}
                <div class="space-y-2.5 pt-2 pb-1">
                    <button type="submit"
                        class="w-full py-4 bg-teal-800 hover:bg-teal-900 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-teal-900/20 active:scale-95 transition-all flex items-center justify-center gap-2"
                        style="background-color: #115e59; color: #ffffff;">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Daftarkan Admin</span>
                    </button>
                    <button type="button" @click="showAddModal = false"
                        class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-xs font-bold uppercase tracking-wider active:scale-95 transition-all text-center"
                        style="background-color: #f1f5f9; color: #475569;">
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
