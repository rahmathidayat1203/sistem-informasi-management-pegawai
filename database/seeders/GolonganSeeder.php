<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GolonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('golongan')->insert([
            ['nama' => 'III/a', 'deskripsi' => 'Penata Muda'],
            ['nama' => 'III/b', 'deskripsi' => 'Penata Muda Tingkat I'],
            ['nama' => 'III/c', 'deskripsi' => 'Penata'],
            ['nama' => 'III/d', 'deskripsi' => 'Penata Tingkat I'],
            ['nama' => 'IV/a', 'deskripsi' => 'Pembina'],
        ]);
    }
}