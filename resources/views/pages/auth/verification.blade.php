@extends('components.layouts.app')

@section('title', 'Verifikasi')

@section('content')
<x-layouts.mobile-app>
    <div class="fixed inset-0 -z-10 bg-gradient-to-br from-teal-50 via-white to-gray-100"></div>

    <div class="flex min-h-screen flex-col items-center justify-center p-6">
        <div class="w-full max-w-md rounded-[2.5rem] border border-white/40 bg-white/70 p-8 shadow-2xl backdrop-blur-xl text-center">
            
            <div class="mb-10">
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/10 shadow-inner">
                    <svg class="h-10 w-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900">Verifikasi</h1>
                <p class="mt-3 text-base text-gray-500">
                    Kami baru saja mengirimkan kode lima digit ke <span class="font-bold text-gray-700 text-nowrap">0819*******</span>.
                </p>
            </div>

            {{-- OTP INPUT BOXES --}}
            <div class="mb-8 flex justify-between gap-2">
                @foreach([3, 5, 1, 8, 2] as $val) {{-- Example values from screenshot --}}
                    <input 
                        type="text" 
                        value="{{ $val }}"
                        maxlength="1" 
                        class="h-16 w-full rounded-2xl border-gray-200 bg-white/50 text-center text-2xl font-bold text-primary focus:border-primary focus:ring-4 focus:ring-primary/10 shadow-sm"
                    />
                @endforeach
            </div>

            <x-ui.button class="h-14 w-full rounded-2xl bg-primary text-lg font-bold shadow-lg shadow-primary/30">
                Lanjut
            </x-ui.button>

            <div class="mt-8 space-y-4">
                <p class="text-sm text-gray-500">
                    Tidak menerima kode? 
                    <button class="font-extrabold text-primary hover:underline">Kirim Ulang</button>
                </p>
                
                <div class="pt-4">
                    <a href="{{ route('view.auth.login') }}" class="text-sm font-bold text-gray-400 hover:text-primary transition-all">
                        Kembali Ke Halaman Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.mobile-app>
@endsection