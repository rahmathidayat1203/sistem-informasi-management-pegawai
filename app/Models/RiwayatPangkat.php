<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatPangkat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'riwayat_pangkat';

    protected $fillable = [
        'pegawai_id',
        'golongan_id',
        'nomor_sk',
        'tanggal_sk',
        'tmt_pangkat',
        'file_sk',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function golongan()
    {
        return $this->belongsTo(Golongan::class);
    }
}
