<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralRedemption extends Model
{
    protected $table = 'referral_redemptions';

    protected $fillable = [
        'pasien_id',
        'booking_id',
        'reward_type',
        'points_used',
        'discount_amount',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
