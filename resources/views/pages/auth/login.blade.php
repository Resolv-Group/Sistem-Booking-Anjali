@extends('components.layouts.app')

@section('title', 'Login')

@section('content')

<x-layouts.mobile-app>
    <div class="fixed inset-0 -z-10 bg-gradient-to-br from-teal-50 via-white to-gray-100"></div>

    <div class="flex min-h-screen flex-col items-center justify-center p-6">
        <div class="w-full max-w-md rounded-[2.5rem] border border-white/40 bg-white/70 p-8 shadow-2xl backdrop-blur-xl">
            
            <div class="mb-10 text-center">
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/10 shadow-inner">
                    <svg class="h-10 w-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Masuk</h1>
                <p class="mt-2 text-base text-gray-500">Silakan masuk ke akun Anda.</p>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700 ml-1">Nomor Telepon</label>
                    <x-ui.input placeholder="081977785978" class="h-14 rounded-2xl border-gray-200 bg-white/50 px-5 text-lg" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700 ml-1">Kata Sandi</label>
                    <div class="relative group">
                        <x-ui.input
                            type="password"
                            placeholder="••••••••••••"
                            class="h-14 w-full rounded-2xl border-gray-200 bg-white/50 pl-5 pr-12 text-lg transition-all focus:ring-primary/20"
                        />
                        <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary transition-colors">
                            {{-- Heroicon: Eye --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-3 text-right">
                        <a href="{{ route('view.auth.forgot-password') }}" class="text-sm font-bold text-primary hover:underline">Lupa Kata Sandi?</a>
                    </div>
                </div>

                <div class="pt-2">
                    <x-ui.button class="h-14 w-full rounded-2xl bg-primary text-lg font-bold shadow-lg shadow-primary/30">
                        Login
                    </x-ui.button>
                </div>
            </div>

            <div class="mt-10 text-center">
                <p class="text-gray-500">Belum punya akun? <a href="{{ route('view.auth.register') }}" class="font-bold text-primary hover:underline">Daftar Disini</a></p>
            </div>
        </div>
    </div>
</x-layouts.mobile-app>
@endsection