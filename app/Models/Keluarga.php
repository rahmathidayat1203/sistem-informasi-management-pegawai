<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Keluarga extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'keluarga';

    protected $fillable = [
        'pegawai_id',
        'nama_lengkap',
        'hubungan',
        'nik',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'pekerjaan',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
