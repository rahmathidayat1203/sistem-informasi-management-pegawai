<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendidikan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pendidikan';

    protected $fillable = [
        'pegawai_id',
        'jenjang',
        'nama_institusi',
        'jurusan',
        'tahun_lulus',
        'nomor_ijazah',
        'file_ijazah',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
