@extends('components.layouts.app')

@section('title', 'Dashboard Admin Global')

@section('content')

<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32">

    {{-- TOPBAR GLASSY --}}
    <nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-100/80 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">
                    Rumah Terapi Anjali
                </span>
                <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none uppercase">
                    Dashboard Global
                </h1>
            </div>
        </div>
    </nav>

    <div class="space-y-6 p-4">

        {{-- SUCCESS/ERROR NOTIFICATIONS --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:leave="transition ease-in duration-300"
                class="bg-teal-600 text-white rounded-2xl p-4 text-xs font-black uppercase tracking-widest text-center shadow-lg shadow-teal-700/20">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:leave="transition ease-in duration-300"
                class="bg-rose-500 text-white rounded-2xl p-4 text-xs font-black uppercase tracking-widest text-center shadow-lg shadow-rose-600/20">
                {{ session('error') }}
            </div>
        @endif

        {{-- Welcome Card --}}
        <div class="bg-teal-900 rounded-[2rem] p-6 text-white shadow-xl shadow-teal-900/10 relative overflow-hidden">
            <div class="relative z-10 space-y-1">
                <span class="text-[10px] font-black text-teal-300 uppercase tracking-[0.2em] leading-none">Selamat Datang</span>
                <h2 class="text-xl font-bold tracking-tight">{{ auth()->user()->name }}</h2>
                <p class="text-[11px] text-teal-100 font-medium">Administrator Global Klinik Anjali</p>
            </div>
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-teal-800 rounded-full opacity-40"></div>
            <div class="absolute -right-2 -top-10 w-20 h-20 bg-teal-700 rounded-full opacity-20"></div>
        </div>

        {{-- STATS GRID (6 Cards) --}}
        <div class="space-y-3">
            <div class="flex items-center px-1">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.25em]">Statistik Sistem</h3>
            </div>
            
            <div class="grid grid-cols-3 gap-3">
                {{-- Total Kolaborasi --}}
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-2 hover:scale-[1.02] active:scale-95 transition-all duration-300">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider leading-tight">Total Cabang</span>
                    <span class="text-2xl font-black text-teal-800 tracking-tight">{{ $totalKolaborasi }}</span>
                </div>
                
                {{-- Total Terapis --}}
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-2 hover:scale-[1.02] active:scale-95 transition-all duration-300">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider leading-tight">Terapis Aktif</span>
                    <span class="text-2xl font-black text-teal-800 tracking-tight">{{ $totalTerapis }}</span>
                </div>
                
                {{-- Total Karyawan --}}
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-2 hover:scale-[1.02] active:scale-95 transition-all duration-300">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider leading-tight">Karyawan</span>
                    <span class="text-2xl font-black text-teal-800 tracking-tight">{{ $totalKaryawan }}</span>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                {{-- Total Pasien --}}
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-2 hover:scale-[1.02] active:scale-95 transition-all duration-300">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider leading-tight">Total Pasien</span>
                    <span class="text-2xl font-black text-teal-800 tracking-tight">{{ $totalPasien }}</span>
                </div>
                
                {{-- Booking Hari Ini --}}
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-2 hover:scale-[1.02] active:scale-95 transition-all duration-300">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider leading-tight">Booking Hari Ini</span>
                    <span class="text-2xl font-black text-teal-800 tracking-tight">{{ $bookingHariIni }}</span>
                </div>
                
                {{-- Booking Bulan Ini --}}
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-2 hover:scale-[1.02] active:scale-95 transition-all duration-300">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider leading-tight">Booking Bulan Ini</span>
                    <span class="text-2xl font-black text-teal-800 tracking-tight">{{ $bookingBulanIni }}</span>
                </div>
            </div>
        </div>

        {{-- CHART SECTION --}}
        <div class="space-y-3">
            <div class="flex items-center px-1">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.25em]">Tren Booking (30 Hari Terakhir)</h3>
            </div>
            
            <div class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-sm">
                <div class="h-48 w-full relative">
                    <canvas id="bookingChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- BOTTOM NAVBAR --}}
    <x-navigation.admin-global-navbar active="dashboard" />

</x-layouts.mobile-app>

{{-- LOAD CHART JS CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('bookingChart').getContext('2d');
        
        const labels = {!! json_encode($chartLabels) !!};
        const values = {!! json_encode($chartValues) !!};

        // Create gradient fill
        const gradient = ctx.createLinearGradient(0, 0, 0, 180);
        gradient.addColorStop(0, 'rgba(15, 118, 110, 0.25)'); // teal-800
        gradient.addColorStop(1, 'rgba(15, 118, 110, 0.00)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Booking',
                    data: values,
                    borderColor: '#0f766e', // teal-700
                    borderWidth: 3,
                    pointBackgroundColor: '#0f766e',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    tension: 0.35,
                    fill: true,
                    backgroundColor: gradient
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f766e',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        displayColors: false,
                        padding: 8,
                        titleFont: {
                            family: 'Manrope',
                            weight: 'bold',
                            size: 10
                        },
                        bodyFont: {
                            family: 'Manrope',
                            size: 12,
                            weight: 'black'
                        },
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' Booking';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Manrope',
                                size: 8,
                                weight: 'bold'
                            },
                            color: '#94a3b8', // slate-400
                            maxTicksLimit: 6
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9', // slate-100
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                family: 'Manrope',
                                size: 8,
                                weight: 'bold'
                            },
                            color: '#94a3b8',
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>

@endsection