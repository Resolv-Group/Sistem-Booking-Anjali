<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operasional_Anjali extends Model
{
    protected $table = 'operasional_anjali';

    protected $fillable = [
        'cabang_anjali_id',
        'hari',
        'jam_buka',
        'jam_tutup',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'hari' => 'integer',
        'jam_buka' => 'time',
        'jam_tutup' => 'time',
        'status' => 'boolean',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang_Anjali::class, 'cabang_anjali_id');
    }

    public function scopeByCabang($query, $cabangId)
    {
        return $query->where('cabang_anjali_id', $cabangId);
    }

    public function scopeByHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', false);
    }
}
