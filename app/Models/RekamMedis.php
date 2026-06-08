<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    protected $table = 'rekam_medis';

    protected $fillable = [
        'booking_pasien_id',
        'tensi_sys',
        'tensi_dia',
        'tensi_pulse',
        'area_tubuh',
        'area_leher',
        'area_dada',
        'area_perut',
        'area_tangan',
        'area_kaki',
        'area_punggung',
        'area_pinggang',
        'makan_suhu',
        'makan_rasa',
        'minum_suhu',
        'minum_tipe',
        'keringat',
        'bab_kapan',
        'bab_bentuk',
        'bak_frekuensi',
        'bak_warna',
        'skala_nyeri',
        'tingkat_perbaikan',
        'goal_terapi',
        'saran_rekomendasi',
        'catatan_khusus',
        'catatan_terapis',
    ];

    protected $casts = [
        'makan_suhu' => 'array',
        'makan_rasa' => 'array',
        'minum_suhu' => 'array',
        'minum_tipe' => 'array',
        'skala_nyeri' => 'integer',
    ];

    public function bookingPatient()
    {
        return $this->belongsTo(BookingPatient::class, 'booking_pasien_id');
    }

    public function fotos()
    {
        return $this->hasMany(RekamMedisFotos::class, 'rekam_medis_id');
    }
}
