<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal_Karyawan extends Model
{
    protected $table = 'jadwal_karyawan';

    protected $fillable = [
        'karyawan_id',
        'hari',
        'shift',
        'jam_mulai',
        'jam_selesai',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'hari' => 'integer',
        'jam_mulai' => 'time',
        'jam_selesai' => 'time',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function scopeByKaryawan($query, $karyawanId)
    {
        return $query->where('karyawan_id', $karyawanId);
    }

    public function scopeByHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    public function scopeByShift($query, $shift)
    {
        return $query->where('shift', $shift);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }
}
