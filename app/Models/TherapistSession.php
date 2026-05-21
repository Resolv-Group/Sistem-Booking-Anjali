<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapistSession extends Model
{
    protected $table = 'terapis_sesi';

    protected $fillable = [
        'terapis_id',
        'operasional_id',
        'kolaborasi_id',
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

    public function kolaborasi()
    {
        return $this->belongsTo(
            Kolaborasi::class,
            'kolaborasi_id'
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
        // Sum the patient count from all valid bookings
        $used = $this->bookings
            ->whereIn('status', ['pending', 'approved', 'completed'])
            ->sum(function ($booking) {
                return $booking->bookingPatients->count();
            });

        return $this->kuota - $used;
    }

    public function getUsedCapacityAttribute()
    {
        return $this->bookings()
            ->whereIn('status', [
                'pending',
                'approved',
                'completed',
            ])
            ->withCount('bookingPatients')
            ->get()
            ->sum('booking_patients_count');
    }
}
