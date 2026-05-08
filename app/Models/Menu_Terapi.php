<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu_Terapi extends Model
{
    protected $table = 'menu_terapi';

    protected $fillable = [
        'daftar_layanan_id',
        'cabang_anjali_id',
        'harga_menu',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'harga_menu' => 'decimal:2',
    ];

    public function layanan()
    {
        return $this->belongsTo(Daftar_Layanan::class, 'daftar_layanan_id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang_Anjali::class, 'cabang_anjali_id');
    }

    public function scopeByLayanan($query, $layananId)
    {
        return $query->where('daftar_layanan_id', $layananId);
    }

    public function scopeByCabang($query, $cabangId)
    {
        return $query->where('cabang_anjali_id', $cabangId);
    }
}
