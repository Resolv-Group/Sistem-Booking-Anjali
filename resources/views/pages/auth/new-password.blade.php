@extends('components.layouts.app')

@section('title', 'Kata Sandi Baru')

@section('content')
<x-layouts.mobile-app>
    <div class="fixed inset-0 -z-10 bg-gradient-to-br from-teal-50 via-white to-gray-100"></div>

    <div class="flex min-h-screen flex-col items-center justify-center p-6">
        <div class="w-full max-w-md rounded-[2.5rem] border border-white/40 bg-white/70 p-8 shadow-2xl backdrop-blur-xl">
            
            <div class="mb-8 text-center">
                 <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10">
                    <svg class="h-8 w-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900">Kata Sandi Baru</h1>
                <p class="mt-2 text-sm text-gray-500">Kata sandi baru Anda harus berbeda dari sebelumnya, minimal 8 karakter.</p>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">Kata Sandi Baru</label>
                    <div class="relative">
                        <x-ui.input type="password" placeholder="••••••••••••" class="h-14 w-full rounded-2xl border-gray-200 bg-white/50 pl-5 pr-12 text-lg" />
                        <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <x-ui.input type="password" placeholder="••••••••••••" class="h-14 w-full rounded-2xl border-gray-200 bg-white/50 pl-5 pr-12 text-lg" />
                        <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </button>
                    </div>
                </div>

                <x-ui.button type="submit" class="h-14 w-full rounded-2xl bg-primary text-lg font-bold shadow-lg shadow-primary/30 mt-4">
                    Reset Kata Sandi
                </x-ui.button>
            </div>

            <div class="mt-8 text-center">
                <a href="#" class="text-sm font-bold text-gray-500">Kembali Ke Halaman Login</a>
            </div>
        </div>
    </div>
</x-layouts.mobile-app>
@endsection