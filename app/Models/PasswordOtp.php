<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordOtp extends Model
{
    protected $table = 'password_reset_otps';

    protected $fillable = [
        'phone',
        'otp',
        'expires_at',
        'created_at',
        'updated_at',
    ];
}
