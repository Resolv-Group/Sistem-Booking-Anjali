<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';

    protected $fillable = [
        'nama',
        'deskripsi',
        'base_harga',
        'homecare_harga',
        'diskon_persentase',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'base_harga' => 'decimal:2',
        'homecare_harga' => 'decimal:2',
        'diskon_persentase' => 'decimal:2',
    ];

    public function karyawans()
    {
        return $this->belongsToMany(
            Karyawan::class,
            'terapis_layanan',
            'layanan_id',
            'terapis_id'
        );
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
