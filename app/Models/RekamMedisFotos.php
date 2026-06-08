<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekamMedisFotos extends Model
{
    protected $table = 'rekam_medis_fotos';

    protected $fillable = [
        'rekam_medis_id',
        'foto',
        'foto_mime',
    ];

    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class, 'rekam_medis_id');
    }
}
