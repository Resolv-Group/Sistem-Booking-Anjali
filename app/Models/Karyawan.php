<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawans';

    protected $fillable = [
        'kode_karyawan',
        'nik',
        'nama_karyawan',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_telp',
        'email',
        'peran',
        'nilai_review',
        'deskripsi_review',
        'tanggal_bergabung',
        'cabang_anjali_id',
        'status_karyawan',
        'foto_path',
        'foto_mime',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_bergabung' => 'date',
        'nilai_review' => 'decimal:2',
    ];

    public function fotoUrl()
    {
        return $this->foto_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->foto_path)
            : null;
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang_Anjali::class, 'cabang_anjali_id');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal_Karyawan::class);
    }

    // public function bookingsAsTherapist()
    // {
    //     return $this->hasMany(Booking::class, 'terapis_id');
    // }

    // public function bookingsAsAdmin()
    // {
    //     return $this->hasMany(Booking::class, 'admin_id');
    // }

    // public function reviews()
    // {
    //     return $this->hasMany(Review::class, 'reviewer_type', 'peran')
    //                 ->where('reviewer_type', 'Karyawan');
    // }

    public function scopeActive($query)
    {
        return $query->where('status_karyawan', 'Aktif');
    }

    public function scopeAdminCabang($query)
    {
        return $query->where('peran', 'Admin Rumah Terapi');
    }

    public function scopeTerapis($query)
    {
        return $query->where('peran', 'Terapis');
    }

    public function scopeSearch($query, $term)
    {
        return $query->when($term, function ($q) use ($term) {
            $q->where('nama_karyawan', 'like', "%{$term}%")
              ->orWhere('kode_karyawan', 'like', "%{$term}%")
              ->orWhere('nik', 'like', "%{$term}%")
              ->orWhere('no_telp', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }
}
