<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (!auth()->check()) {
        return view('pages.landing.index');
    }

    $user = auth()->user();

    return match ($user->role) {
        \App\Enums\UserRole::PATIENT => redirect()->route('patient.dashboard'),
        \App\Enums\UserRole::THERAPIST => redirect()->route('therapist.dashboard'),
        \App\Enums\UserRole::ADMIN_CABANG => redirect()->route('admin-cabang.dashboard'),
        \App\Enums\UserRole::ADMIN_GLOBAL => redirect()->route('admin-global.dashboard'),
        default => view('pages.landing.index'),
    };
})->name('landing');

Route::view('/layanan','pages.services.index')->name('layanan');

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

//booking
Route::get(
    '/booking',
    [\App\Http\Controllers\BookingController::class, 'index']
)->name('patient.booking.index');

Route::get(
    '/booking/form',
    [\App\Http\Controllers\BookingController::class, 'create']
)->name('patient.booking.form');

Route::post(
    '/booking/form',
    [\App\Http\Controllers\BookingController::class, 'store']
)->name('patient.booking.store');

Route::view(
    '/booking/form/selesai',
    'pages.booking.patient.form-selesai'
)->name('patient.booking.form-selesai');

Route::view('/patient/profile','pages.profile.patient')->name('patient.profile');
Route::view('/therapist/profile','pages.profile.therapist')->name('therapist.profile');
Route::view('/admin-global/profile','pages.profile.admin-global')->name('admin-global.profile');
Route::view('/admin-cabang/profile','pages.profile.admin-cabang')->name('admin-cabang.profile');

Route::view('/dashboard/therapist','pages.dashboard.therapist')->name('therapist.dashboard');
Route::view('/jadwal/therapist','pages.jadwal.therapist')->name('therapist.jadwal');
Route::view('/jadwal/therapist/atur-jam-kerja','pages.jadwal.atur-jam-kerja')->name('therapist.atur-jam-kerja');
Route::view('/jadwal/therapist/ringkasan-sesi','pages.jadwal.ringkasan-sesi')->name('therapist.ringkasan-sesi');
Route::view('/booking/list','pages.booking.therapist.index')->name('therapist.booking');
Route::view('/booking/history','pages.booking.therapist.history')->name('therapist.booking.history');

Route::view('/dashboard/admin-cabang','pages.dashboard.admin-cabang')->name('admin-cabang.dashboard');
Route::view('/lainnya/admin-global','pages.lainnya.admin-global')->name('global.dashboard');


//login
Route::view('/login', 'pages.auth.login')->name('view.auth.login');
Route::post('login', [
    AuthenticatedSessionController::class,
    'store'
])->name('auth.login');

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('auth.logout');

Route::view('/register', 'pages.auth.register')->name('view.auth.register');
Route::post('register', [RegisteredUserController::class, 'store'])->name('auth.register');

Route::view(
    '/forgot-password',
    'pages.auth.forgot-password'
) ->name('view.auth.forgot-password');

Route::view(
    '/verification',
    'pages.auth.verification'
) ->name('view.auth.verification');

Route::view(
    '/new-password',
    'pages.auth.new-password'
) ->name('view.auth.new-password');

