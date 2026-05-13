@extends('components.layouts.app')

@section('title', 'Register')

@section('content')

<x-layouts.mobile-app>
    {{-- Background Decorative Element --}}
    <div class="fixed inset-0 -z-10 bg-gradient-to-br from-teal-50 via-white to-gray-100"></div>

    <div class="flex min-h-screen flex-col items-center justify-center p-6 py-12">
        
        {{-- Glass Card Container --}}
        <div class="relative w-full max-w-md rounded-[2.5rem] border border-white/40 bg-white/70 p-8 shadow-2xl backdrop-blur-xl">
            
            {{-- Back Button --}}
            <a href="{{ url('/') }}" class="absolute left-6 top-6 flex h-10 w-10 items-center justify-center rounded-full bg-white/80 shadow-sm transition-transform hover:scale-105 active:scale-95 text-gray-600 hover:text-primary backdrop-blur-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>

            {{-- LOGO & HEADER SECTION --}}
            <div class="mb-8 mt-2 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10 shadow-inner">
                    {{-- Placeholder for Logo Anjali --}}
                    <svg class="h-8 w-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Buat Akun Baru</h1>
                <p class="mt-2 text-base text-gray-500">Silakan lengkapi data diri Anda di bawah ini.</p>
            </div>

            {{-- FORM SECTION --}}
            <form method="POST" action="{{ route('auth.register') }}" class="space-y-5">
                @csrf
                
                {{-- NIK --}}
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">NIK</label>
                    <x-ui.input
                        name="nik"
                        placeholder="357811XXXXXXXXXX"
                        class="h-14 rounded-2xl border-gray-200 bg-white/50 px-5 text-lg transition-all focus:ring-primary/20"
                    />
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">Nama Lengkap</label>
                    <x-ui.input
                        name="name"
                        placeholder="Masukkan nama lengkap Anda"
                        class="h-14 rounded-2xl border-gray-200 bg-white/50 px-5 text-lg transition-all focus:ring-primary/20"
                    />
                </div>

                {{-- Email --}}
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">Email</label>
                    <x-ui.input
                        name="email"
                        type="email"
                        placeholder="nama@email.com"
                        class="h-14 rounded-2xl border-gray-200 bg-white/50 px-5 text-lg transition-all focus:ring-primary/20"
                    />
                </div>

                {{-- Nomor Telepon --}}
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">Nomor Telepon</label>
                    <x-ui.input
                        name="phone"
                        placeholder="081977785978"
                        class="h-14 rounded-2xl border-gray-200 bg-white/50 px-5 text-lg transition-all focus:ring-primary/20"
                    />
                </div>

                {{-- Tanggal Lahir --}}
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">Tanggal Lahir</label>
                    <x-ui.input
                        name="tanggal_lahir"
                        type="date"
                        class="h-14 rounded-2xl border-gray-200 bg-white/50 px-5 text-lg transition-all focus:ring-primary/20"
                    />
                </div>

                {{-- Jenis Kelamin --}}
                <x-ui.select-no-search
                    label="Jenis Kelamin"
                    name="jenis_kelamin"
                    placeholder="Pilih Jenis Kelamin"
                    :options="[
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan'
                    ]"
                />

                {{-- Kata Sandi --}}
                <div x-data="{ show: false }">
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">Kata Sandi</label>
                    <div class="relative">
                        <x-ui.input name="password" type="password" x-bind:type="show ? 'text' : 'password'" placeholder="Masukkan kata sandi" class="h-14 w-full rounded-2xl border-gray-200 bg-white/50 pl-5 pr-12 text-lg" />
                        <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary transition-colors focus:outline-none">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-4">
                    <x-ui.button type="submit" class="h-14 w-full rounded-2xl bg-primary text-lg font-bold shadow-lg shadow-primary/30 transition-transform active:scale-[0.98]">
                        Buat Akun
                    </x-ui.button>
                </div>
            </form>

            {{-- FOOTER --}}
            <div class="mt-8 text-center">
                <p class="text-gray-500">
                    Sudah punya akun? 
                    <a href="{{ route('view.auth.login') }}" class="font-bold text-primary hover:underline transition-all">
                        Masuk Disini
                    </a>
                </p>
            </div>

        </div>
    </div>
</x-layouts.mobile-app>

@endsection