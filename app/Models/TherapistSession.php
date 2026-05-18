<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapistSession extends Model
{

    protected $table = 'terapis_sesi';

    protected $fillable = [
        'terapis_id',
        'cabang_id',
        'tanggal_sesi',
        'waktu_mulai',
        'kuota',
        'status',
    ];

    protected $casts = [];

    public function therapist()
    {
        return $this->belongsTo(
            Karyawan::class,
            'terapis_id'
        );
    }

    public function cabang()
    {
        return $this->belongsTo(
            Cabang::class,
            'cabang_id'
        );
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'terapis_sesi_id');
    }

    /*
    Remaining slot helper
    */

    public function getRemainingCapacityAttribute()
    {
        $used = $this->bookings()
            ->whereIn('status',[
                'menunggu',
                'disetujui',
                'selesai'
            ])
            ->withCount('bookingPatients')
            ->get()
            ->sum('booking_patients_count');

        return $this->kuota - $used;
    }

    public function getUsedCapacityAttribute()
    {
        return $this->bookings()
            ->whereIn('status',[
                'menunggu',
                'disetujui',
                'selesai'
            ])
            ->withCount('bookingPatients')
            ->get()
            ->sum('booking_patients_count');
    }
}
