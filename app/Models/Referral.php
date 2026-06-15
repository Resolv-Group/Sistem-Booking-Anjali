<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referer_id',
        'referee_id',
        'points_awarded',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function referer()
    {
        return $this->belongsTo(Pasien::class, 'referer_id');
    }

    public function referee()
    {
        return $this->belongsTo(Pasien::class, 'referee_id');
    }
}
