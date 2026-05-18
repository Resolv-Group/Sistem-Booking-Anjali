<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $table = 'pasiens';

    protected $fillable = [
        'user_id',
        'pasien_public_id',
        'nik',
        'nama_pasien',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_telp',
        'email',
        'kode_referral',
        'poin_referral',
        'membership_tier',
        'foto_path',
        'foto_mime',
    ];

    public function fotoUrl()
    {
        return $this->foto_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->foto_path)
            : null;
    }

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookingPatients()
    {
        return $this->hasMany(BookingPatient::class);
    }

    public function scopeSearch($query, $term)
    {
        return $query->when($term, function ($q) use ($term) {
            $q->where('nama_pasien', 'like', "%{$term}%")
              ->orWhere('no_telp', 'like', "%{$term}%")
              ->orWhere('nik', 'like', "%{$term}%");
        });
    }
}
