<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    protected $with = ['bookings.bookingPatients'];

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

    // public function getRemainingCapacityAttribute()
    // {
    //     // Sum the patient count from all valid bookings
    //     $used = $this->bookings
    //         ->whereIn('status', ['pending', 'approved', 'completed'])
    //         ->sum(function ($booking) {
    //             return $booking->bookingPatients->count();
    //         });

    //     return $this->kuota - $used;
    // }

    public function getRemainingCapacityAttribute()
    {
        $used = $this->bookings()
            ->whereIn('status', ['pending', 'approved', 'completed'])
            ->withCount(['bookingPatients as unique_patient_count' => function ($query) {
                $query->select(DB::raw('COUNT(DISTINCT pasien_id)'));
            }])
            ->get()
            ->sum('unique_patient_count');

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
