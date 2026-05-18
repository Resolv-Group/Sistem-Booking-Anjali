<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    protected $table = 'cabang';

    protected $fillable = [
        'nama_cabang',
        'alamat_cabang',
        'no_telp_cabang',
        'email_cabang',
        'nilai_review',
        'deskripsi_review',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'nilai_review' => 'decimal:2',
    ];

    public function karyawans()
    {
        return $this->hasMany(Karyawan::class);
    }

    public function therapistSessions()
    {
        return $this->hasMany(TherapistSession::class);
    }
}
