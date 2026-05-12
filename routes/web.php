<?php

use Illuminate\Support\Facades\Route;

Route::view(
    '/dashboard/admin-global',
    'pages.dashboard.admin-global'
);

Route::view(
    '/dashboard/patient',
    'pages.dashboard.patient'
);

Route::view(
    '/dashboard/therapist',
    'pages.dashboard.therapist'
);

Route::view(
    '/dashboard/admin-cabang',
    'pages.dashboard.admin-cabang'
);

Route::view(
    '/lainnya/admin-global',
    'pages.lainnya.admin-global'
);


Route::view('/login', 'pages.auth.login')->name('view.auth.login');

Route::view('/register', 'pages.auth.register')->name('view.auth.register');

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

