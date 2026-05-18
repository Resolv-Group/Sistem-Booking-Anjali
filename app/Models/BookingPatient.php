<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPatient extends Model
{

    protected $table = 'booking_pasien';

    protected $fillable = [
        'booking_id',
        'pasien_id',
        'keluhan_pasien',
        'catatan_terapis',
        'ringkasan_sesi',
        'status_pasien',
    ];

    public function booking()
    {
        return $this->belongsTo(
            Booking::class
        );
    }

    public function pasien()
    {
        return $this->belongsTo(
            Pasien::class,
            'pasien_id'
        );
    }
}
