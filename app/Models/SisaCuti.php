<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import untuk relasi belongsTo

class SisaCuti extends Model
{
    protected $table = 'sisa_cuti';
    // Kolom yang bisa diisi (mass assignable)
    protected $fillable = [
        'pegawai_id',
        'tahun',
        'jatah_cuti',
        'sisa_cuti',
    ];

    // Casting untuk memastikan tipe data kolom tertentu
    protected $casts = [
        'tahun' => 'integer', // Pastikan tahun disimpan sebagai integer
        'jatah_cuti' => 'integer',
        'sisa_cuti' => 'integer',
    ];

    // Relasi: SisaCuti belongs to Pegawai
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

}
