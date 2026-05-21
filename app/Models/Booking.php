<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'booking';

    protected $fillable = [
        'booking_oleh_pasien_id',
        'terapis_sesi_id',
        'status',
        'bukti_transfer_booking_path',
        'bukti_transfer_booking_mime',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'completed_by',
        'completed_at',
        'cancelled_by',
        'cancelled_at',
        'alasan_status',
        'batalkan_type',
        'updated_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(
            Pasien::class,
            'booking_oleh_pasien_id'
        );
    }

    public function session()
    {
        return $this->belongsTo(
            TherapistSession::class,
            'terapis_sesi_id'
        );
    }

    public function layanan()
    {
        return $this->belongsTo(
            Layanan::class,
            'layanan_id'
        );
    }

    public function bookingPatients()
    {
        return $this->hasMany(
            BookingPatient::class
        );
    }
}
