<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordOtp;
use App\Models\User;
use App\Services\WhatsappServices;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('pages.auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Generate 6 digit OTP
        $otp = rand(100000, 999999);

        // 2. Cek apakah nomor telepon terdaftar di database
        $user = User::where('phone', $request->phone)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'phone' => 'Nomor telepon tidak terdaftar.',
            ]);
        }

        // 2. Simpan atau update OTP di database
        PasswordOtp::updateOrInsert(
            ['phone' => $request->phone],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(5),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // 3. Kirim OTP via Service WA
        $sent = WhatsappServices::sendOTP($request->phone, $otp);

        if (! $sent) {
            throw ValidationException::withMessages([
                'phone' => 'Gagal mengirimkan kode OTP. Silakan coba lagi nanti.',
            ]);
        }

        // Alihkan user ke halaman verifikasi OTP dengan membawa data nomor telepon di session
        return redirect()->route('password.verify-otp')->with('phone', $request->phone);
    }
}
