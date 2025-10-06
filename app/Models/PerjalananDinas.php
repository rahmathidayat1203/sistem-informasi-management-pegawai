<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerjalananDinas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'perjalanan_dinas';

    protected $fillable = [
        'nomor_surat_tugas',
        'maksud_perjalanan',
        'tempat_tujuan',
        'tgl_berangkat',
        'tgl_kembali',
        'pimpinan_pemberi_tugas_id',
    ];

    protected $dates = ['tgl_berangkat', 'tgl_kembali'];

    public function pimpinanPemberiTugas()
    {
        return $this->belongsTo(User::class, 'pimpinan_pemberi_tugas_id');
    }

    public function pegawai()
    {
        return $this->belongsToMany(Pegawai::class, 'pegawai_perjalanan_dinas');
    }

    public function laporanPD()
    {
        return $this->hasOne(LaporanPD::class);
    }
}
