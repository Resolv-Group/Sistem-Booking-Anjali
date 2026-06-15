@extends('components.layouts.app')

@section('title', 'Program Referral')

@section('content')
    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32" x-data="{
        kodeReferral: '{{ $pasien->kode_referral }}',
        copied: false,
        loading: false,
        copyCode() {
            navigator.clipboard.writeText(this.kodeReferral);
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        },
        generateCode() {
            this.loading = true;
            fetch('{{ route('patient.referral.generate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.loading = false;
                if (data.success) {
                    this.kodeReferral = data.kode_referral;
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        customClass: {
                            popup: 'rounded-2xl shadow-xl border border-emerald-100 bg-white/90 backdrop-blur-md',
                            title: 'text-sm font-black text-slate-800',
                            htmlContainer: 'text-xs font-medium text-slate-500'
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.error || 'Terjadi kesalahan.',
                        customClass: {
                            popup: 'rounded-2xl shadow-xl border border-red-100 bg-white/90 backdrop-blur-md'
                        }
                    });
                }
            })
            .catch(err => {
                this.loading = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan koneksi.'
                });
            });
        }
    }">

        {{-- 1. TOPBAR GLASSY --}}
        <nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-100/80 px-6 py-4">
            <div class="flex items-center justify-between">
                
                {{-- Left: Navigation & Context --}}
                <div class="flex items-center gap-4">
                    <a href="{{ route('patient.profile') }}" 
                    class="group flex items-center justify-center w-10 h-10 bg-white border border-slate-100 rounded-xl shadow-sm hover:bg-teal-50 transition-all active:scale-90">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">
                            ANJALI SADINA MULYO
                        </span>
                        <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none uppercase">
                            Program Referral
                        </h1>
                    </div>
                </div>

                {{-- Right: Profile --}}
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-xl border-2 border-white shadow-md p-0.5">
                            <img src="{{ asset('images/logo_anjali.jpg') }}" 
                                class="w-full h-full rounded-[10px] object-cover bg-white">
                        </div>
                    </div>
                </div>

            </div>
        </nav>

        <div class="px-6 pt-8 space-y-6">

            {{-- 2. HERO CARD - POINTS DISPLAY --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-teal-800 to-emerald-950 text-white p-6 rounded-[2.2rem] shadow-xl">
                <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute -left-8 -top-8 w-24 h-24 bg-teal-500/20 rounded-full blur-xl animate-pulse"></div>

                <div class="relative z-10 flex justify-between items-center">
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-teal-200/80 uppercase tracking-widest">Total Poin Referral</p>
                        <h2 class="text-4xl font-black tracking-tight">{{ $pasien->poin_referral ?? 0 }} <span class="text-sm font-bold text-teal-300">Poin</span></h2>
                    </div>
                    <div class="bg-white/10 p-3.5 rounded-2xl backdrop-blur-md">
                        <svg class="w-8 h-8 text-teal-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- 3. REFERRAL CODE GENERATOR / VIEW CARD --}}
            <div class="bg-white p-6 rounded-[2.2rem] border border-slate-100 shadow-sm space-y-5">
                <div class="text-center space-y-2">
                    <h3 class="text-base font-black text-slate-800">Bagikan Kode Referral Anda</h3>
                    <p class="text-xs font-medium text-slate-500 leading-relaxed px-4">Dapatkan {{ config('referral.points_per_referral', 10) }} poin gratis untuk setiap teman yang mendaftar dan menyelesaikan terapi pertama mereka.</p>
                </div>

                {{-- State 1: Code generated --}}
                <div x-show="kodeReferral" x-cloak class="space-y-4">
                    <div class="flex items-center justify-between bg-slate-50 p-4 rounded-2xl border border-dashed border-slate-200">
                        <span class="text-lg font-black tracking-widest text-teal-700 select-all ml-2" x-text="kodeReferral"></span>
                        <button @click="copyCode()" 
                            class="px-4 py-2.5 bg-teal-50 text-teal-700 hover:bg-teal-100 active:scale-95 transition-all text-xs font-black uppercase tracking-wider rounded-xl flex items-center gap-1.5">
                            <span x-show="!copied">Salin</span>
                            <span x-show="copied" x-cloak>Tersalin!</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 002 2h2a2 2 0 002-2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                        </button>
                    </div>

                    {{-- WhatsApp Share --}}
                    {{-- <a :href="'https://api.whatsapp.com/send?text=Yuk%20daftar%20terapi%20di%20Rumah%20Terapi%20Anjali%20menggunakan%20kode%20referral%20saya%20%2A' + kodeReferral + '%2A%20dan%20dapatkan%20layanan%20kesehatan%20terbaik%21%20Daftar%20di%20sini%3A%20' + encodeURIComponent(window.location.origin + '/register')"
                       target="_blank"
                       class="w-full flex items-center justify-center gap-2 py-4 bg-emerald-600 text-white rounded-2xl text-xs font-bold uppercase tracking-wider shadow-md shadow-emerald-600/10 hover:bg-emerald-700 active:scale-[0.98] transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.062 5.248 5.308 0 11.77 0c3.13 0 6.073 1.22 8.285 3.435 2.213 2.217 3.429 5.166 3.427 8.297-.005 6.525-5.25 11.77-11.713 11.77-2.002-.001-3.971-.51-5.713-1.477L0 24zm6.59-4.846c1.616.96 3.2 1.463 4.981 1.464 5.36 0 9.721-4.362 9.725-9.727.002-2.583-1.002-5.011-2.831-6.84S14.34 1.258 11.766 1.258c-5.366 0-9.73 4.364-9.734 9.729-.001 1.905.5 3.758 1.453 5.385l-1.02 3.722 3.822-1.005zm12.39-7.234c-.308-.154-1.82-.9-2.102-1-.28-.1-.485-.154-.687.154-.202.308-.783.985-.96 1.189-.177.202-.353.228-.66.074-.308-.154-1.3-.478-2.477-1.528-.916-.816-1.534-1.825-1.714-2.132-.18-.308-.02-.474.135-.628.14-.138.307-.359.462-.538.154-.18.205-.308.308-.513.102-.206.05-.385-.026-.538-.076-.154-.687-1.654-.94-2.27-.247-.59-.5-.51-.687-.52-.18-.01-.385-.01-.59-.01-.205 0-.538.077-.82.385-.28.308-1.077 1.051-1.077 2.564 0 1.513 1.1 2.974 1.254 3.18 1.54 2.03 3.327 3.09 5.56 3.96 1.28.5 2.29.58 3.08.46.884-.13 1.82-.74 2.08-1.42.256-.67.256-1.25.18-1.37-.08-.12-.28-.19-.588-.344z"/>
                        </svg>
                        Bagikan Lewat WhatsApp
                    </a> --}}
                </div>

                {{-- State 2: Code not generated yet --}}
                <div x-show="!kodeReferral" class="pt-2">
                    <button @click="generateCode()" :disabled="loading"
                        class="w-full flex items-center justify-center gap-2 py-4 bg-teal-800 text-white rounded-2xl text-sm font-black uppercase tracking-widest shadow-xl shadow-teal-900/10 hover:bg-teal-950 active:scale-[0.98] transition-all disabled:opacity-50">
                        <span x-show="!loading">Buat Kode Referral</span>
                        <span x-show="loading" x-cloak>Membuat...</span>
                        <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- 4. MONTHLY LIMIT INDICATOR --}}
            @php 
                $limit = config('referral.monthly_limit', 5);
                $percentage = min(100, ($currentMonthCount / $limit) * 100);
            @endphp
            <div class="bg-white p-6 rounded-[2.2rem] border border-slate-100 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <div class="space-y-0.5">
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Limit Reward Bulan Ini</h4>
                        <p class="text-[10px] font-semibold text-slate-400">Limit bulanan agar promo tetap seimbang</p>
                    </div>
                    <span class="text-xs font-black text-teal-700 bg-teal-50 px-3 py-1.5 rounded-xl">
                        {{ $currentMonthCount }} / {{ $limit }} Sukses
                    </span>
                </div>

                <div class="relative w-full h-3.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-600 rounded-full transition-all duration-500" 
                         style="width: {{ $percentage }}%"></div>
                </div>

                <p class="text-[10px] font-bold text-slate-400 leading-normal italic">
                    *Anda bisa mengundang teman sebanyak-banyaknya, namun hanya 3-5 referral pertama (sesuai ketentuan) per bulan yang memberikan poin reward.
                </p>
            </div>

            {{-- 5. REFERRED FRIENDS LIST --}}
            <div class="bg-white p-6 rounded-[2.2rem] border border-slate-100 shadow-sm space-y-4">
                <div class="px-1 border-b border-slate-50 pb-3">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Teman Yang Anda Undang ({{ $referrals->count() }})</h3>
                </div>

                @if($referrals->isEmpty())
                    <div class="py-8 text-center space-y-3">
                        <div class="inline-flex p-4 bg-slate-50 text-slate-300 rounded-full">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-slate-400">Belum ada teman yang bergabung.</p>
                        <p class="text-[10px] font-medium text-slate-300">Bagikan kodemu untuk mulai mengumpulkan poin!</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-50 max-h-[400px] overflow-y-auto pr-1">
                        @foreach($referrals as $ref)
                            <div class="py-4 flex justify-between items-center first:pt-0 last:pb-0">
                                <div class="space-y-1">
                                    <p class="text-sm font-bold text-slate-800">{{ $ref->referee->nama_pasien ?? 'Pasien' }}</p>
                                    <p class="text-[10px] font-medium text-slate-400">Bergabung: {{ $ref->created_at->translatedFormat('d M Y') }}</p>
                                </div>

                                <div>
                                    @if(is_null($ref->completed_at))
                                        <span class="px-3 py-1.5 bg-amber-50 text-amber-700 text-[10px] font-black uppercase tracking-wider rounded-xl">
                                            Menunggu Sesi
                                        </span>
                                    @elseif($ref->points_awarded > 0)
                                        <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-wider rounded-xl">
                                            Selesai (+{{ $ref->points_awarded }} Poin)
                                        </span>
                                    @else
                                        <span class="px-3 py-1.5 bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-wider rounded-xl">
                                            Limit Tercapai (+0 Poin)
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <x-navigation.patient-navbar active="profile" />
    </x-layouts.mobile-app>

    <style>
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }
    </style>
@endsection
