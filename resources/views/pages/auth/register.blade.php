@extends('components.layouts.app')

@section('title', 'Register')

@section('content')

<x-layouts.mobile-app>
    {{-- Background Decorative Element --}}
    <div class="fixed inset-0 -z-10 bg-gradient-to-br from-teal-50 via-white to-gray-100"></div>

    <div class="flex min-h-screen flex-col items-center justify-center p-6 py-12">
        
        {{-- Glass Card Container --}}
        <div class="w-full max-w-md rounded-[2.5rem] border border-white/40 bg-white/70 p-8 shadow-2xl backdrop-blur-xl">
            
            {{-- LOGO & HEADER SECTION --}}
            <div class="mb-8 text-center">
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
            <div class="space-y-5">
                
                {{-- NIK --}}
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">NIK</label>
                    <x-ui.input
                        placeholder="357811XXXXXXXXXX"
                        class="h-14 rounded-2xl border-gray-200 bg-white/50 px-5 text-lg transition-all focus:ring-primary/20"
                    />
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">Nama Lengkap</label>
                    <x-ui.input
                        placeholder="Masukkan nama lengkap Anda"
                        class="h-14 rounded-2xl border-gray-200 bg-white/50 px-5 text-lg transition-all focus:ring-primary/20"
                    />
                </div>

                {{-- Email --}}
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">Email</label>
                    <x-ui.input
                        type="email"
                        placeholder="nama@email.com"
                        class="h-14 rounded-2xl border-gray-200 bg-white/50 px-5 text-lg transition-all focus:ring-primary/20"
                    />
                </div>

                {{-- Nomor Telepon --}}
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">Nomor Telepon</label>
                    <x-ui.input
                        placeholder="081977785978"
                        class="h-14 rounded-2xl border-gray-200 bg-white/50 px-5 text-lg transition-all focus:ring-primary/20"
                    />
                </div>

                {{-- Kata Sandi --}}
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">Kata Sandi</label>
                    <div class="relative">
                        <x-ui.input type="password" placeholder="••••••••••••" class="h-14 w-full rounded-2xl border-gray-200 bg-white/50 pl-5 pr-12 text-lg" />
                        <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-4">
                    <x-ui.button class="h-14 w-full rounded-2xl bg-primary text-lg font-bold shadow-lg shadow-primary/30 transition-transform active:scale-[0.98]">
                        Buat Akun
                    </x-ui.button>
                </div>
            </div>

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