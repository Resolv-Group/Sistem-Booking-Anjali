<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TherapistSchedule extends Model
{
    use HasFactory;

    protected $table = 'terapis_operasional';

    protected $fillable = [

        'terapis_id',

        'hari',

        'waktu_mulai',

        'kuota',

        'status',
    ];

    public function therapist()
    {
        return $this->belongsTo(
            Karyawan::class,
            'terapis_id'
        );
    }

    public function sessions()
    {
        return $this->hasMany(
            TherapistSession::class,
            'operasional_id'
        );
    }
}
