<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
        'foto',
        'foto_mime',
        'golongan_darah',
        'tinggi_badan',
        'berat_badan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date'
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

    public function getAgeAttribute()
{
    if (!$this->tanggal_lahir) return null;

    return \Carbon\Carbon::parse($this->tanggal_lahir)->age;
}

    public function getFotoAttribute($value): ?string
    {
        if (is_null($value)) {
            return null;
        }
        if (is_resource($value)) {
            return base64_encode(stream_get_contents($value));
        }

        return $value; // already base64 string
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

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referer_id');
    }

    public function referee()
    {
        return $this->hasOne(Referral::class, 'referee_id');
    }

    public static function generateUniqueReferralCode(): string
    {
        do {
            $code = 'ANJALI-' . strtoupper(\Illuminate\Support\Str::random(5));
        } while (self::where('kode_referral', $code)->exists());

        return $code;
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
