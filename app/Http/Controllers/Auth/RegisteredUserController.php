<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'password' => ['nullable', Rules\Password::defaults()],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'referral_code' => ['nullable', 'string', 'exists:pasiens,kode_referral'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'phone.required' => 'Nomor telepon wajib diisi',
            'phone.unique' => 'Nomor telepon sudah terdaftar',
            'password.required' => 'Kata sandi wajib diisi',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi',
            'password.min' => 'Kata sandi minimal 8 karakter',
            'referral_code.exists' => 'Kode referral tidak valid.',
        ]);

        $finalPassword = $request->password
            ? $request->password
            : Carbon::parse($request->tanggal_lahir)->format('d-m-Y');

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($finalPassword),
            'role' => UserRole::PATIENT,
        ]);

        $referer = null;
        if ($request->filled('referral_code')) {
            $referer = Pasien::where('kode_referral', $request->referral_code)->first();
        }

        $pasien = Pasien::create([
            'user_id' => $user->id,
            'nik' => $request->nik,
            'nama_pasien' => $request->name,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_telp' => $request->phone,
            'email' => $request->email,
        ]);

        if ($referer) {
            \App\Models\Referral::create([
                'referer_id' => $referer->id,
                'referee_id' => $pasien->id,
                'points_awarded' => 0,
                'completed_at' => null,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('view.auth.login', absolute: false))
            ->with('success', 'Pendaftaran berhasil! Silakan masuk.');
    }
}
