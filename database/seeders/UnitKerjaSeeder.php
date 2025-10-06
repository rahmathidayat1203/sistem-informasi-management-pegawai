<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('unit_kerja')->insert([
            ['nama' => 'Sekretariat', 'deskripsi' => 'Bagian Sekretariat Dinas'],
            ['nama' => 'Bidang E-Government', 'deskripsi' => 'Bidang yang mengurusi aplikasi dan sistem informasi'],
            ['nama' => 'Bidang TIK', 'deskripsi' => 'Bidang yang mengurusi infrastruktur dan jaringan'],
            ['nama' => 'Bidang Komunikasi Publik', 'deskripsi' => 'Bidang yang mengurusi hubungan masyarakat'],
        ]);
    }
}