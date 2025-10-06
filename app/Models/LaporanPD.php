<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaporanPD extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'laporan_pd';

    protected $fillable = [
        'perjalanan_dinas_id',
        'file_laporan',
        'tgl_unggah',
        'status_verifikasi',
        'catatan_verifikasi',
        'admin_keuangan_verifier_id',
    ];

    protected $dates = ['tgl_unggah'];

    public function perjalananDinas()
    {
        return $this->belongsTo(PerjalananDinas::class);
    }

    public function adminKeuanganVerifier()
    {
        return $this->belongsTo(User::class, 'admin_keuangan_verifier_id');
    }
}
