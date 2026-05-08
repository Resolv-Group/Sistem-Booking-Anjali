<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Daftar_Layanan extends Model
{
    protected $table = 'daftar_layanan';

    protected $fillable = [
        'nama_layanan',
        'detail_layanan',
        'durasi_menit',
        'status_layanan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'durasi_menit' => 'integer',
    ];

    public function menuTerapi()
    {
        return $this->hasMany(Menu_Terapi::class, 'daftar_layanan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status_layanan', 'Tersedia');
    }

    public function scopeSearch($query, $term)
    {
        return $query->when($term, function ($q) use ($term) {
            $q->where('nama_layanan', 'like', "%{$term}%")
              ->orWhere('detail_layanan', 'like', "%{$term}%");
        });
    }
}
