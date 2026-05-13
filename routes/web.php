<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.landing.index')->name('landing');

Route::view(
    '/dashboard/admin-global',
    'pages.dashboard.admin-global'
)->name('admin-global.dashboard');

Route::view(
    '/dashboard/patient',
    'pages.dashboard.patient'
)->name('patient.dashboard');

Route::view('/patient/profile','pages.profile.patient')->name('patient.profile');
Route::view('/therapist/profile','pages.profile.therapist')->name('therapist.profile');
Route::view('/admin-global/profile','pages.profile.admin-global')->name('admin-global.profile');
Route::view('/admin-cabang/profile','pages.profile.admin-cabang')->name('admin-cabang.profile');

Route::view(
    '/dashboard/therapist',
    'pages.dashboard.therapist'
)->name('therapist.dashboard');

Route::view(
    '/dashboard/admin-cabang',
    'pages.dashboard.admin-cabang'
)->name('admin-cabang.dashboard');

Route::view(
    '/lainnya/admin-global',
    'pages.lainnya.admin-global'
)->name('global.dashboard');


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

