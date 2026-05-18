<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operasional extends Model
{
    protected $table = 'operasional_rumah_terapi';

    protected $fillable = [
        'cabang_id',
        'hari',
        'waktu_buka',
        'waktu_tutup',
        'status_operasional',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'hari' => 'integer',
        'waktu_buka' => 'time',
        'waktu_tutup' => 'time',
        'status_operasional' => 'boolean',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function scopeByCabang($query, $cabangId)
    {
        return $query->where('cabang_id', $cabangId);
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
