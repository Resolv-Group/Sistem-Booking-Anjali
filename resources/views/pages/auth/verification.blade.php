@extends('components.layouts.app')

@section('title', 'Verifikasi OTP')

@section('content')
    <x-layouts.mobile-app>
        <div class="fixed inset-0 -z-10 bg-gradient-to-br from-teal-50 via-white to-gray-100"></div>

        <div class="flex min-h-screen flex-col items-center justify-center p-6">
            <div class="w-full max-w-md rounded-[2.5rem] border border-white/40 bg-white/70 p-8 shadow-2xl backdrop-blur-xl">

                <div class="mb-8 text-center">
                    <div
                        class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/10 shadow-inner">
                        <svg class="h-10 w-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-900">Verifikasi OTP</h1>
                    <p class="mt-3 text-base text-gray-500">
                        Kami mengirimkan kode 6 digit ke nomor <span
                            class="font-bold text-gray-700">{{ substr($phone, 0, 4) }}******{{ substr($phone, -2) }}</span>.
                    </p>
                </div>

                {{-- Form Verifikasi & Reset Password --}}
                <form method="POST" action="{{ route('password.update-phone') }}" x-data="{ showNew: false, showConfirm: false }">
                    @csrf
                    <input type="hidden" name="phone" value="{{ $phone }}">

                    {{-- OTP INPUT BOXES (6 Digits) --}}
                    <div class="mb-6 text-left">
                        <label class="mb-2 block text-sm font-semibold text-gray-700 ml-1">Kode OTP</label>

                        {{-- Hidden input asli untuk mengirimkan gabungan teks OTP ke Laravel --}}
                        <input type="hidden" name="otp" id="real-otp">

                        <div class="flex justify-between gap-2" id="otp-container">
                            @for ($i = 0; $i < 6; $i++)
                                <input type="text" maxlength="1" pattern="\d*" inputmode="numeric"
                                    class="otp-box h-14 w-full rounded-2xl border bg-white/50 text-center text-xl font-bold text-primary shadow-sm focus:outline-none focus:ring-4 focus:ring-primary/10
                                {{ $errors->has('otp') ? 'border-red-500' : 'border-gray-200' }}" />
                            @endfor
                        </div>
                        @error('otp')
                            <p class="mt-2 flex items-center gap-1 text-sm font-semibold text-red-500 ml-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password fields disatukan di sini agar proses submit aman --}}
                    <div class="space-y-4 text-left mb-6">
                        {{-- Kata Sandi Baru --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">Kata Sandi Baru</label>
                            <div class="relative">
                                <input :type="showNew ? 'text' : 'password'" name="password"
                                    placeholder="Minimal 8 karakter"
                                    class="h-14 w-full rounded-2xl border bg-white/50 pl-5 pr-12 text-base font-medium transition-all focus:outline-none focus:ring-4
                                {{ $errors->has('password') ? 'border-red-500 focus:ring-red-500/20' : 'border-gray-200 focus:border-primary focus:ring-primary/10' }}" />
                                <button type="button" @click="showNew = !showNew"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary transition-colors focus:outline-none">
                                    <svg x-show="!showNew" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="showNew" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1.5 text-sm font-semibold text-red-500 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Kata Sandi --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">Konfirmasi Kata
                                Sandi</label>
                            <div class="relative">
                                <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation"
                                    placeholder="Ulangi kata sandi baru"
                                    class="h-14 w-full rounded-2xl border border-gray-200 bg-white/50 pl-5 pr-12 text-base font-medium transition-all focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10" />
                                <button type="button" @click="showConfirm = !showConfirm"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary transition-colors focus:outline-none">
                                    <svg x-show="!showConfirm" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="showConfirm" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <x-ui.button type="submit"
                        class="h-14 w-full rounded-2xl bg-primary text-lg font-bold shadow-lg shadow-primary/30">
                        Perbarui Kata Sandi
                    </x-ui.button>
                </form>

                <div class="mt-8 text-center space-y-4">
                    <p class="text-sm text-gray-500">
                        Tidak menerima kode?
                        <a href="{{ route('view.auth.forgot-password') }}"
                            class="font-extrabold text-primary hover:underline">Kirim
                            Ulang</a>
                    </p>

                    <div class="pt-2">
                        <a href="{{ route('login') }}"
                            class="text-sm font-bold text-gray-400 hover:text-primary transition-all">
                            Kembali Ke Halaman Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-layouts.mobile-app>

    {{-- JavaScript Helper untuk Auto-Tab Input Kotak OTP --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('otp-container');
            const boxes = container.querySelectorAll('.otp-box');
            const realOtpInput = document.getElementById('real-otp');

            const updateRealOtp = () => {
                let compiled = '';
                boxes.forEach(box => compiled += box.value);
                realOtpInput.value = compiled;
            };

            boxes.forEach((box, index) => {
                box.addEventListener('input', (e) => {
                    if (box.value.length === 1 && index < boxes.length - 1) {
                        boxes[index + 1].focus();
                    }
                    updateRealOtp();
                });

                box.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && box.value === '' && index > 0) {
                        boxes[index - 1].focus();
                    }
                });
            });
        });
    </script>
@endsection
