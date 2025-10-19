<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pegawai';

    protected $fillable = [
        'NIP',
        'nama_lengkap',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'agama',
        'alamat',
        'no_telp',
        'foto_profil',
        'jabatan_id',
        'golongan_id',
        'unit_kerja_id',
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function golongan()
    {
        return $this->belongsTo(Golongan::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'pegawai_id');
    }

    public function pendidikan()
    {
        return $this->hasMany(Pendidikan::class);
    }

    public function keluarga()
    {
        return $this->hasMany(Keluarga::class);
    }

    public function riwayatPangkat()
    {
        return $this->hasMany(RiwayatPangkat::class);
    }

    public function riwayatJabatan()
    {
        return $this->hasMany(RiwayatJabatan::class);
    }

    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }

    public function perjalananDinas()
    {
        return $this->belongsToMany(PerjalananDinas::class, 'pegawai_perjalanan_dinas');
    }

    public function laporanPD()
    {
        return $this->hasMany(LaporanPD::class);
    }

    /**
     * Scope for searching pegawai by nama or NIP.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama_lengkap', 'LIKE', '%'.$search.'%')
              ->orWhere('NIP', 'LIKE', '%'.$search.'%');
        });
    }

    public function sisaCuti()
    {
        return $this->hasMany(SisaCuti::class);
    }
}