<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRescheduleHistory extends Model
{
    protected $table = 'booking_reschedule_histories';

    protected $fillable = [
        'booking_id',
        'old_terapis_sesi_id',
        'new_terapis_sesi_id',
        'changed_by',
        'alasan_old',
        'alasan_new',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function oldTerapisSesi()
    {
        return $this->belongsTo(TherapistSession::class, 'old_terapis_sesi_id');
    }

    public function newTerapisSesi()
    {
        return $this->belongsTo(TherapistSession::class, 'new_terapis_sesi_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
