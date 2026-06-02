<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappServices
{
    public static function sendOTP($to, $otp)
    {
        $message = "Kode OTP untuk mereset kata sandi Anda adalah: *{$otp}*. Kode ini berlaku selama 5 menit. Jangan bagikan kode ini kepada siapa pun.";

        // Contoh integrasi dengan contoh vendor (misal: Fonnte)
        // Sesuaikan URL dan strukturnya dengan dokumentasi vendor yang Anda gunakan
        $response = Http::withHeaders([
            'Authorization' => env('WHATSAPP_API_TOKEN'),
        ])->post('https://api.fonnte.com/send', [
            'target' => $to,
            'message' => $message,
        ]);

        return $response->successful();
    }
}
