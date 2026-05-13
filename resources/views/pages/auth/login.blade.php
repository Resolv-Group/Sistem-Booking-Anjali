@extends('components.layouts.app')

@section('title', 'Login')

@section('content')

<x-layouts.mobile-app>
    <div class="fixed inset-0 -z-10 bg-gradient-to-br from-teal-50 via-white to-gray-100"></div>

    <div class="flex min-h-screen flex-col items-center justify-center p-6">
        <div class="relative w-full max-w-md rounded-[2.5rem] border border-white/40 bg-white/70 p-8 shadow-2xl backdrop-blur-xl">
            
            {{-- Back Button --}}
            <a href="{{ url('/') }}" class="absolute left-6 top-6 flex h-10 w-10 items-center justify-center rounded-full bg-white/80 shadow-sm transition-transform hover:scale-105 active:scale-95 text-gray-600 hover:text-primary backdrop-blur-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>

            <div class="mb-10 mt-2 text-center">
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/10 shadow-inner">
                    <svg class="h-10 w-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Masuk</h1>
                <p class="mt-2 text-base text-gray-500">Silakan masuk ke akun Anda.</p>
            </div>

            {{-- Success Alert --}}
            @if(session('success'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 4000)"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-10 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 -translate-y-10 scale-95"
                     class="fixed top-6 left-1/2 -translate-x-1/2 z-50 flex max-w-sm w-full items-center gap-3 rounded-2xl border border-green-200 bg-green-50 px-6 py-4 shadow-2xl">
                    <svg class="h-6 w-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p class="text-sm font-bold text-green-900">{{ session('success') }}</p>
                </div>
            @endif

            <form action="#" method="POST" class="space-y-6">
                @csrf
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700 ml-1">Nomor Telepon</label>
                            <x-ui.input 
                                id="phone"
                                name="phone"
                                type="text"
                                placeholder="081977785978"
                                class="h-14 rounded-2xl border-gray-200 bg-white/50 px-5 text-lg"
                            />
                    </div>

                    <div x-data="{ show: false }">
                        <label class="mb-2 block text-sm font-semibold text-gray-700 ml-1">Kata Sandi</label>
                        <div class="relative group">
                            <x-ui.input
                                id="password"
                                name="password"
                                type="password"
                                x-bind:type="show ? 'text' : 'password'"
                                placeholder="••••••••••••"
                                class="h-14 w-full rounded-2xl border-gray-200 bg-white/50 pl-5 pr-12 text-lg transition-all focus:ring-primary/20"
                            />
                            <button @click="show = !show" type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary transition-colors focus:outline-none">
                                {{-- Eye Icon --}}
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{-- Eye Slash Icon --}}
                                <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-3 text-right">
                            <a href="{{ route('view.auth.forgot-password') }}" class="text-sm font-bold text-primary hover:underline">Lupa Kata Sandi?</a>
                        </div>
                    </div>

                    <div class="pt-2">
                        <x-ui.button
                            type="submit"
                            placeholder="Login"
                            name="login"
                            value="login"
                            class="h-14 w-full rounded-2xl bg-primary text-lg font-bold shadow-lg shadow-primary/30"
                        />
                    </div>
            </form>

            <div class="mt-10 text-center">
                <p class="text-gray-500">Belum punya akun? <a href="{{ route('view.auth.register') }}" class="font-bold text-primary hover:underline">Daftar Disini</a></p>
            </div>
        </div>
    </div>
</x-layouts.mobile-app>
@endsection