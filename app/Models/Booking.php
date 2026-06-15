<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'booking';

    protected $fillable = [
        'booking_oleh_pasien_id',
        'terapis_sesi_id',
        'booking_oleh_karyawan_id',
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

    protected static function booted()
    {
        static::updated(function ($booking) {
            if ($booking->wasChanged('status') && $booking->status === 'completed') {
                self::processReferralPoints($booking);
            }
        });
    }

    public static function processReferralPoints($booking)
    {
        $patientIds = [];
        if ($booking->booking_oleh_pasien_id) {
            $patientIds[] = $booking->booking_oleh_pasien_id;
        }

        $bpPatientIds = $booking->bookingPatients()->pluck('pasien_id')->toArray();
        $patientIds = array_unique(array_merge($patientIds, $bpPatientIds));

        foreach ($patientIds as $pasienId) {
            $referral = \App\Models\Referral::where('referee_id', $pasienId)
                ->whereNull('completed_at')
                ->first();

            if ($referral) {
                $referer = \App\Models\Pasien::find($referral->referer_id);
                if ($referer) {
                    $limit = config('referral.monthly_limit', 5);

                    $currentMonthCount = \App\Models\Referral::where('referer_id', $referer->id)
                        ->whereNotNull('completed_at')
                        ->where('points_awarded', '>', 0)
                        ->whereMonth('completed_at', now()->month)
                        ->whereYear('completed_at', now()->year)
                        ->count();

                    if ($currentMonthCount < $limit) {
                        $points = config('referral.points_per_referral', 10);
                        $referer->increment('poin_referral', $points);

                        $referral->update([
                            'completed_at' => now(),
                            'points_awarded' => $points,
                        ]);
                    } else {
                        $referral->update([
                            'completed_at' => now(),
                            'points_awarded' => 0,
                        ]);
                    }
                }
            }
        }
    }
}
