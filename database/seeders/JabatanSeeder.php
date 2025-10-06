<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jabatan')->insert([
            ['nama' => 'Kepala Dinas', 'deskripsi' => 'Kepala Dinas Komunikasi dan Informatika'],
            ['nama' => 'Sekretaris Dinas', 'deskripsi' => 'Sekretaris Dinas Komunikasi dan Informatika'],
            ['nama' => 'Analis Sistem Informasi', 'deskripsi' => 'Staff di Bidang E-Government'],
            ['nama' => 'Pranata Komputer', 'deskripsi' => 'Staff di Bidang TIK'],
        ]);
    }
}