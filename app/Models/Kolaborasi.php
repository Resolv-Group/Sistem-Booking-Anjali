<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kolaborasi extends Model
{
    protected $table = 'kolaborasi';

    protected $fillable = [
        'nama_kolaborasi',
        'alamat_kolaborasi',
        'kota_kolaborasi',
        'no_telp_kolaborasi',
        'email_kolaborasi',
        'nilai_review',
        'deskripsi_review',
        'homecare_harga',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'nilai_review' => 'decimal:2',
        'homecare_harga' => 'decimal:2',
    ];

    public function karyawans()
    {
        return $this->hasMany(Karyawan::class);
    }

    public function therapistSessions()
    {
        return $this->hasMany(TherapistSession::class);
    }

    public function getStaffCountAttribute()
    {
        return $this->karyawans()->count();
    }
}
