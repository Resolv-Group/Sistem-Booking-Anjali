@extends('components.layouts.app')

@section('title', 'Lupa Kata Sandi')

@section('content')
    <x-layouts.mobile-app>
        <div class="fixed inset-0 -z-10 bg-gradient-to-br from-teal-50 via-white to-gray-100"></div>

        <div class="flex min-h-screen flex-col items-center justify-center p-6">
            <div
                class="w-full max-w-md rounded-[2.5rem] border border-white/40 bg-white/70 p-8 shadow-2xl backdrop-blur-xl text-center">

                <div class="mb-8">
                    <div
                        class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/10 shadow-inner">
                        <svg class="h-10 w-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-900">Lupa Kata Sandi?</h1>
                    <p class="mt-3 text-base text-gray-500 leading-relaxed">
                        Masukkan nomor telepon Anda dan kami akan mengirimkan kode verifikasi untuk mengatur ulang kata
                        sandi Anda.
                    </p>
                </div>

                {{-- Form Pengiriman --}}
                <form method="POST" action="{{ route('password.email') }}" class="space-y-6 text-left">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700 ml-1">Nomor Telepon</label>
                        <x-ui.input id="phone" name="phone" type="tel" inputmode="numeric" pattern="[0-9]*"
                            placeholder="081977785978" maxlength="13" minlength="10"
                            class="h-14 rounded-2xl bg-white/50 px-5 text-lg {{ $errors->has('phone') ? 'border-red-500 focus:ring-red-500/20' : 'border-gray-200' }}"
                            x-data @input="$el.value = $el.value.replace(/[^0-9]/g, '')" value="{{ old('phone') }}" />
                    </div>

                    <x-ui.button type="submit"
                        class="h-14 w-full rounded-2xl bg-primary text-lg font-bold shadow-lg shadow-primary/30">
                        Kirim Kode Verifikasi
                    </x-ui.button>
                </form>

                <div class="mt-10">
                    <a href="{{ route('view.auth.login') }}"
                        class="text-sm font-bold text-gray-500 hover:text-primary transition-all">
                        Kembali Ke Halaman Login
                    </a>
                </div>
            </div>
        </div>
    </x-layouts.mobile-app>
@endsection
