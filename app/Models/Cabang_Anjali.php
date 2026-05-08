<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang_Anjali extends Model
{
    protected $table = 'cabang_anjali';

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

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'cabang_anjali_id');
    }

    public function operasional()
    {
        return $this->hasMany(Operasional_Anjali::class, 'cabang_anjali_id');
    }

    public function menuTerapi()
    {
        return $this->hasMany(Menu_Terapi::class, 'cabang_anjali_id');
    }

    // public function reviews()
    // {
    //     return $this->hasMany(Review::class, 'reviewer_type', 'cabang_anjali')
    //                 ->where('reviewer_type', 'Cabang Anjali');
    // }

    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function scopeSearch($query, $term)
    {
        return $query->when($term, function ($q) use ($term) {
            $q->where('nama_cabang', 'like', "%{$term}%")
              ->orWhere('alamat_cabang', 'like', "%{$term}%")
              ->orWhere('no_telp_cabang', 'like', "%{$term}%")
              ->orWhere('email_cabang', 'like', "%{$term}%");
        });
    }
}
