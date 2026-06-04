<?php

use App\Enums\UserRole;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyOtpController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KolaborasiController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\OperasionalController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TherapistScheduleController;
use App\Http\Controllers\TherapistSessionController;
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

Route::get(
    '/therapist',
    [PagesController::class, 'TherapistListView']
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
    '/booking/form-pasien/selesai',
    'pages.booking.patient.form-selesai'
)->name('patient.booking.form-selesai');

Route::get(
    '/booking/patient/my-booking',
    [BookingController::class, 'myBooking']
)->name('patient.booking.my-booking');


Route::view(
    '/booking/form/selesai',
    'pages.booking.admin.form-selesai'
)->name('admin.booking.form-selesai');

// Route::middleware('auth')->post('/patients/quick-register', [BookingController::class, 'quickRegisterPatient']);

// Admin Global Profile
Route::view('/admin-global/profile', 'pages.profile.admin-global')->name('admin-global.profile');
Route::get('/admin-global/profile/edit', [ProfileController::class, 'editProfileAdminGlobal'])->name('admin-global.profile.edit');
Route::put('/admin-global/profile/update', [ProfileController::class, 'updateProfileAdminGlobal'])->name('admin-global.profile.update');

// Pasien Profile
Route::view('/patient/profile', 'pages.profile.patient')->name('patient.profile');
Route::get('/patient/profile/edit', [ProfileController::class, 'editProfilePasien'])->name('patient.profile.edit');
Route::put('/patient/profile/update', [ProfileController::class, 'updateProfilePasien'])->name('patient.profile.update');

// Terapis Profile
Route::view('/therapist/profile', 'pages.profile.therapist')->name('therapist.profile');
Route::get('/therapist/profile/edit', [ProfileController::class, 'editProfileTherapist'])->name('therapist.profile.edit');
Route::put('/therapist/profile/update', [ProfileController::class, 'updateProfileTherapist'])->name('therapist.profile.update');

// Admin Cabang Profile
Route::view('/admin-cabang/profile', 'pages.profile.admin-cabang')->name('admin-cabang.profile');
Route::get('/admin-cabang/profile/edit', [ProfileController::class, 'editProfileAdminCabang'])->name('admin-cabang.profile.edit');
Route::put('/admin-cabang/profile/update', [ProfileController::class, 'updateProfileAdminCabang'])->name('admin-cabang.profile.update');

// therapist
Route::view('/dashboard/therapist', 'pages.dashboard.therapist')->name('therapist.dashboard');
Route::get('/jadwal/therapist', [TherapistSessionController::class, 'index'])->name('therapist.jadwal');
Route::post('/jadwal/therapist/session/{id}/start', [TherapistSessionController::class, 'startSession'])->name('therapist.session.start');
Route::get('/jadwal/therapist/atur-jam-kerja', [TherapistScheduleController::class, 'index'])->name('therapist.atur-jam-kerja');
Route::post('/jadwal/therapist/atur-jam-kerja/form', [TherapistScheduleController::class, 'store'])->name('therapist.atur-jam-kerja.store');
Route::get('/jadwal/therapist/session/{id}/catatan', [TherapistSessionController::class, 'catatanForm'])->name('therapist.ringkasan-sesi');
Route::post('/jadwal/therapist/session/{id}/catatan', [TherapistSessionController::class, 'saveCatatan'])->name('therapist.ringkasan-sesi.store');
Route::view('/therapist/booking/list', 'pages.booking.therapist.index')->name('therapist.booking');
Route::view('/therapist/booking/history', 'pages.booking.therapist.history')->name('therapist.booking.history');

// admin cabang / kolaborasi
Route::view('/dashboard/admin-kolaborasi', 'pages.dashboard.admin-cabang')->name('admin-cabang.dashboard');
Route::get('/admin-cabang/booking/list', [BookingController::class, 'adminBookingListIndex'])->name('admin-cabang.booking.list');
Route::post('/admin-cabang/booking/{booking}/accept', [BookingController::class, 'accept'])->name('admin-cabang.booking.accept');
Route::post('/admin-cabang/booking/{booking}/reject', [BookingController::class, 'reject'])->name('admin-cabang.booking.reject');
Route::post('/admin-cabang/booking/{booking}/cancel-approval', [BookingController::class, 'cancelApproval'])->name('admin-cabang.booking.cancel');
Route::get('/admin-cabang/booking/form', [BookingController::class, 'adminBookingForm'])->name('admin-cabang.booking.form');
Route::post('/admin-cabang/booking/form', [BookingController::class, 'adminBookingStore'])->name('admin-cabang.booking.store');

// admin global
Route::view('/lainnya/admin-global', 'pages.lainnya.admin-global')->name('global.dashboard');
// Route::view('/cabang/admin-global', 'pages.cabang.index')->name('admin-global.cabang');
Route::get('/cabang/admin-global', [KolaborasiController::class, 'index'])->name('admin-global.cabang');
Route::get('/cabang/admin-global/menu/{id_kolaborasi}', [KolaborasiController::class, 'menuIndex'])->name('admin-global.cabang.menu');
Route::get('/cabang/admin-global/edit/{id_kolaborasi}', [KolaborasiController::class, 'edit'])->name('admin-global.cabang.edit');
Route::put('/cabang/admin-global/update/{id_kolaborasi}', [KolaborasiController::class, 'update'])->name('admin-global.cabang.update');
Route::view('/cabang/admin-global/create', 'pages.cabang.cabang-create')->name('admin-global.cabang.create');
Route::get('/cabang/admin-global/menu/{id_kolaborasi}/operasional', [OperasionalController::class, 'index'])->name('admin-global.operasional-jadwal');
Route::post('/cabang/admin-global/menu/{id_kolaborasi}/operasional', [OperasionalController::class, 'update'])->name('admin-global.operasional-jadwal.update');

// Layanan CRUD (scoped to kolaborasi)
Route::get('/layanan/{id_kolaborasi}', [LayananController::class, 'layananIndex'])->name('admin-global.layanan');
Route::get('/layanan/{id_kolaborasi}/create', [LayananController::class, 'layananCreate'])->name('admin-global.layanan.create');
Route::post('/layanan/{id_kolaborasi}', [LayananController::class, 'layananStore'])->name('admin-global.layanan.store');
Route::get('/layanan/{id_kolaborasi}/detail/{id_layanan}', [LayananController::class, 'layananDetail'])->name('admin-global.layanan.detail');
Route::put('/layanan/{id_kolaborasi}/detail/{id_layanan}', [LayananController::class, 'layananUpdate'])->name('admin-global.layanan.update');
Route::delete('/layanan/{id_kolaborasi}/detail/{id_layanan}', [LayananController::class, 'layananDestroy'])->name('admin-global.layanan.destroy');

// Assign layanan to therapist
Route::get('/atur-layanan/{id_kolaborasi}', [LayananController::class, 'index'])->name('admin-global.therapist-list');
Route::get('/atur-layanan/{id_kolaborasi}/pilih-therapist/{id_karyawan}', [LayananController::class, 'assignLayanan'])->name('admin-global.assign-layanan');
Route::post('/atur-layanan/{id_kolaborasi}/pilih-therapist/{id_karyawan}', [LayananController::class, 'assignLayananStore'])->name('admin-global.assign-layanan.store');

// Karyawan Management (scoped to kolaborasi)
Route::get('/cabang/admin-global/menu/{id_kolaborasi}/karyawan', [KaryawanController::class, 'index'])->name('admin-global.karyawan');
Route::get('/cabang/admin-global/menu/{id_kolaborasi}/karyawan/create', [KaryawanController::class, 'create'])->name('admin-global.karyawan.create');
Route::post('/cabang/admin-global/menu/{id_kolaborasi}/karyawan', [KaryawanController::class, 'store'])->name('admin-global.karyawan.store');
Route::get('/cabang/admin-global/menu/{id_kolaborasi}/karyawan/{id_karyawan}/detail', [KaryawanController::class, 'detail'])->name('admin-global.karyawan.detail');
Route::put('/cabang/admin-global/menu/{id_kolaborasi}/karyawan/{id_karyawan}', [KaryawanController::class, 'update'])->name('admin-global.karyawan.update');
Route::delete('/cabang/admin-global/menu/{id_kolaborasi}/karyawan/{id_karyawan}', [KaryawanController::class, 'destroy'])->name('admin-global.karyawan.destroy');
Route::post('/cabang/admin-global/menu/{id_kolaborasi}/karyawan/map-batch', [KaryawanController::class, 'mapToCabang'])
    ->name('admin-global.karyawan.map');
// login
Route::view('/login', 'pages.auth.login')->name('view.auth.login');
Route::post('login', [
    AuthenticatedSessionController::class,
    'store',
])->name('auth.login');

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('auth.logout');

Route::view('/register', 'pages.auth.register')->name('view.auth.register');
Route::post('register', [RegisteredUserController::class, 'store'])->name('auth.register');

// 1. Halaman minta OTP (Input Nomor Telepon)
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('view.auth.forgot-password');
Route::post('/forgot-password/request', [PasswordResetLinkController::class, 'store'])->name('password.email');

// 2. Halaman Verifikasi OTP & Input Password Baru
Route::get('/verify-otp', [VerifyOtpController::class, 'create'])->name('password.verify-otp');
Route::post('/verify-otp/update', [VerifyOtpController::class, 'store'])->name('password.update-phone');
