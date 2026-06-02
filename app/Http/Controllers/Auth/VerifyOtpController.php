<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class VerifyOtpController extends Controller
{
    // Menampilkan halaman input OTP & Password baru
    public function create(Request $request)
    {
        $phone = session('phone') ?? $request->phone;

        if (! $phone) {
            return redirect()->route('view.auth.forgot-password')->withErrors(['phone' => 'Sesi habis, silakan masukkan nomor kembali.']);
        }

        return view('pages.auth.verification', compact('phone'));
    }

    // Memproses verifikasi OTP dan update password
    public function store(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'exists:users,phone'],
            'otp' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Cek apakah OTP cocok dan belum expired
        $otpData = PasswordOtp::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otpData) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau telah kadaluwarsa.'])->withInput();
        }

        // OTP Valid, update password user
        $user = User::where('phone', $request->phone)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        // Hapus OTP agar tidak bisa digunakan lagi
        PasswordOtp::where('phone', $request->phone)->delete();

        return redirect('/login')->with('success', 'Kata sandi berhasil diperbarui. Silakan masuk.');
    }
}
