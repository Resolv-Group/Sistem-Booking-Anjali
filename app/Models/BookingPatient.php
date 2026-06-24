<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPatient extends Model
{

    protected $table = 'booking_pasien';

    protected $fillable = [
        'booking_id',
        'pasien_id',
        'layanan_id',
        'keluhan_pasien',
        'catatan_terapis',
        'ringkasan_sesi',
        'status_pasien',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

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

    // public function status_pasien()
    // {
    //     return $this->belongsTo(
    //         Pasien::class,
    //         'status_pasien'
    //     );
    // }

    public function rekamMedis()
    {
        return $this->hasOne(RekamMedis::class, 'booking_pasien_id');
    }
}
