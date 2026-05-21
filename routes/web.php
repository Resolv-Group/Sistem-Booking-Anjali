<?php

use App\Enums\UserRole;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TherapistScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return view('pages.landing.index');
    }

    $user = auth()->user();

    return match ($user->role) {
        UserRole::PATIENT => redirect()->route('patient.dashboard'),
        UserRole::THERAPIST => redirect()->route('therapist.dashboard'),
        UserRole::ADMIN_KOLABORASI => redirect()->route('admin-cabang.dashboard'),
        UserRole::ADMIN_GLOBAL => redirect()->route('admin-global.dashboard'),
        default => view('pages.landing.index'),
    };
})->name('landing');

Route::view('/layanan', 'pages.services.index')->name('layanan');

Route::view(
    '/dashboard/admin-global',
    'pages.dashboard.admin-global'
)->name('admin-global.dashboard');

Route::view(
    '/dashboard/patient',
    'pages.dashboard.patient'
)->name('patient.dashboard');

Route::view(
    '/therapist',
    'pages.therapist.patient'
)->name('patient.therapist');

// booking
Route::get(
    '/booking',
    [BookingController::class, 'index']
)->name('patient.booking.index');

Route::get(
    '/booking/form',
    [BookingController::class, 'create']
)->name('patient.booking.form');

Route::post(
    '/booking/form',
    [BookingController::class, 'store']
)->name('patient.booking.store');

Route::view(
    '/booking/form/selesai',
    'pages.booking.patient.form-selesai'
)->name('patient.booking.form-selesai');

Route::view('/patient/profile', 'pages.profile.patient')->name('patient.profile');
Route::view('/therapist/profile', 'pages.profile.therapist')->name('therapist.profile');
Route::view('/admin-global/profile', 'pages.profile.admin-global')->name('admin-global.profile');
Route::view('/admin-cabang/profile', 'pages.profile.admin-cabang')->name('admin-cabang.profile');

// therapist
Route::view('/dashboard/therapist', 'pages.dashboard.therapist')->name('therapist.dashboard');
Route::view('/jadwal/therapist', 'pages.jadwal.therapist')->name('therapist.jadwal');
Route::get('/jadwal/therapist/atur-jam-kerja', [TherapistScheduleController::class, 'index'])->name('therapist.atur-jam-kerja');
Route::post('/jadwal/therapist/atur-jam-kerja/form', [TherapistScheduleController::class, 'store'])->name('therapist.atur-jam-kerja.store');
Route::view('/jadwal/therapist/ringkasan-sesi', 'pages.jadwal.ringkasan-sesi')->name('therapist.ringkasan-sesi');
Route::view('/therapist/booking/list', 'pages.booking.therapist.index')->name('therapist.booking');
Route::view('/therapist/booking/history', 'pages.booking.therapist.history')->name('therapist.booking.history');

// admin cabang / kolaborasi
Route::view('/dashboard/admin-kolaborasi', 'pages.dashboard.admin-cabang')->name('admin-cabang.dashboard');
Route::get('/admin-cabang/booking/list', [BookingController::class, 'adminBookingListIndex'])->name('admin-cabang.booking.list');
Route::post('/admin-cabang/booking/{booking}/accept', [BookingController::class, 'accept'])->name('admin-cabang.booking.accept');
Route::post('/admin-cabang/booking/{booking}/reject', [BookingController::class, 'reject'])->name('admin-cabang.booking.reject');
Route::post('/admin-cabang/booking/{booking}/cancel-approval', [BookingController::class, 'cancelApproval'])->name('admin-cabang.booking.cancel');

// admin global

Route::view('/lainnya/admin-global', 'pages.lainnya.admin-global')->name('global.dashboard');
Route::view('/cabang/admin-global', 'pages.cabang.index')->name('admin-global.cabang');
Route::view('/cabang/admin-global/menu', 'pages.cabang.menu')->name('admin-global.cabang.menu');
Route::view('/cabang/admin-global/create', 'pages.cabang.cabang-create')->name('admin-global.cabang.create');
// Route::view('/cabang/admin-global/edit','pages.cabang.edit')->name('admin-global.cabang.edit');

Route::view('/operasional-jadwal/admin-global/menu', 'pages.cabang.menu.operasional-jadwal')->name('admin-global.operasional-jadwal');
Route::view('/layanan/admin-global/menu', 'pages.cabang.menu.layanan.layanan-menu')->name('admin-global.layanan');
Route::view('/layanan/admin-global/create', 'pages.cabang.menu.layanan.layanan-create')->name('admin-global.layanan.create');
Route::view('/layanan/admin-global/detail', 'pages.cabang.menu.layanan.layanan-detail')->name('admin-global.layanan.detail');

Route::view('/atur-layanan/pilih-therapist', 'pages.cabang.menu.assign-layanan.therapist-list')->name('admin-global.therapist-list');
Route::view('/atur-layanan/admin-global', 'pages.cabang.menu.assign-layanan.assign-layanan')->name('admin-global.assign-layanan');

// login
Route::view('/login', 'pages.auth.login')->name('view.auth.login');
Route::post('login', [
    AuthenticatedSessionController::class,
    'store',
])->name('auth.login');

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('auth.logout');

Route::view('/register', 'pages.auth.register')->name('view.auth.register');
Route::post('register', [RegisteredUserController::class, 'store'])->name('auth.register');

Route::view(
    '/forgot-password',
    'pages.auth.forgot-password'
)->name('view.auth.forgot-password');

Route::view(
    '/verification',
    'pages.auth.verification'
)->name('view.auth.verification');

Route::view(
    '/new-password',
    'pages.auth.new-password'
)->name('view.auth.new-password');
