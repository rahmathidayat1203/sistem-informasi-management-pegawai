<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pengaturan;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'nama_daerah' => 'Kota [Nama Daerah]',
            'nama_opd' => '[Nama OPD]',
            'alamat_opd' => '[Alamat Lengkap]',
            'telepon_opd' => '[Nomor Telepon]',
            'website_opd' => 'www.websitekota.go.id',
            'email_opd' => 'email@kota.go.id',
            'nama_kepala_opd' => '[Nama Kepala OPD]',
            'nip_kepala_opd' => '[NIP Kepala OPD]',
        ];

        foreach ($settings as $key => $value) {
            Pengaturan::set($key, $value);
        }
    }
}
