<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pasien extends Model
{
    use HasUuids;

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

    public function uniqueIds(): array
    {
        return ['pasien_public_id'];
    }

    public function fotoUrl()
    {
        return $this->foto_path
            ? Storage::disk('public')->url($this->foto_path)
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
