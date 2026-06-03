<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Karyawan extends Model
{
    use HasUuids;

    protected $table = 'karyawans';

    protected $fillable = [
        'user_id',
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
        'kolaborasi_id',
        'status_karyawan',
        'foto',
        'foto_mime',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_bergabung' => 'date',
        'nilai_review' => 'decimal:2',
    ];

    public function uniqueIds(): array
    {
        return ['kode_karyawan'];
    }

    public function fotoUrl()
    {
        return $this->foto_path
            ? Storage::disk('public')->url($this->foto_path)
            : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kolaborasi()
    {
        return $this->belongsTo(Kolaborasi::class);
    }

    public function sessions()
    {
        return $this->hasMany(
            TherapistSession::class,
            'terapis_id'
        );
    }

    public function layanans()
    {
        return $this->belongsToMany(
            Layanan::class,
            'terapis_layanan',
            'terapis_id',
            'layanan_id'
        );
    }
}
