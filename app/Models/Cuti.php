<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cuti extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cuti';

    protected $fillable = [
        'pegawai_id',
        'jenis_cuti_id',
        'pimpinan_approver_id',
        'tgl_pengajuan',
        'tgl_mulai',
        'tgl_selesai',
        'keterangan',
        'status_persetujuan',
        'dokumen_pendukung',
        'alokasi_sisa_cuti',
    ];

    protected $dates = ['tgl_pengajuan', 'tgl_mulai', 'tgl_selesai'];

    protected $casts = [
        'alokasi_sisa_cuti' => 'array',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class);
    }

    public function pimpinanApprover()
    {
        return $this->belongsTo(User::class, 'pimpinan_approver_id');
    }
}
