<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapistReview extends Model
{
    protected $table = 'therapist_reviews';

    protected $fillable = [
        'booking_id',
        'pasien_id',
        'terapis_id',
        'rating',
        'deskripsi',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function therapist()
    {
        return $this->belongsTo(Karyawan::class, 'terapis_id');
    }
}
