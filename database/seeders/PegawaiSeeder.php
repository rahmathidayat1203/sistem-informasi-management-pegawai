<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Golongan;
use App\Models\UnitKerja;
use App\Models\User;
use Spatie\Permission\Models\Role;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $jabatan1 = Jabatan::firstOrCreate(['nama' => 'Staff Administrasi'], [
            'nama' => 'Staff Administrasi',
            'deskripsi' => 'Staf administrasi umum'
        ]);
        
        $jabatan2 = Jabatan::firstOrCreate(['nama' => 'Programmer'], [
            'nama' => 'Programmer',
            'deskripsi' => 'Software Developer'
        ]);
        
        $golongan = Golongan::firstOrCreate(['nama' => 'III/a'], [
            'nama' => 'III/a',
            'deskripsi' => 'Penata Muda'
        ]);
        
        $unitKerja = UnitKerja::firstOrCreate(['nama' => 'IT Support'], [
            'nama' => 'IT Support',
            'deskripsi' => 'Divisi IT Support'
        ]);
        
        $pegawaiRole = Role::firstOrCreate(['name' => 'Pegawai']);
        
        // Create sample pegawai
        $pegawais = [
            [
                'NIP' => '198705012008011001',
                'nama_lengkap' => 'Budi Santoso',
                'tempat_lahir' => 'Jakarta',
                'tgl_lahir' => '1987-05-01',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'alamat' => 'Jl. Sudirman No. 123 Jakarta',
                'no_telp' => '081234567890',
                'jabatan_id' => $jabatan1->id,
                'golongan_id' => $golongan->id,
                'unit_kerja_id' => $unitKerja->id,
                'email' => 'budi.santoso@example.com',
                'username' => 'budi.santoso'
            ],
            [
                'NIP' => '199003152012031002',
                'nama_lengkap' => 'Siti Nurhaliza',
                'tempat_lahir' => 'Surabaya',
                'tgl_lahir' => '1990-03-15',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'alamat' => 'Jl. Gajah Mada No. 456 Surabaya',
                'no_telp' => '082345678901',
                'jabatan_id' => $jabatan2->id,
                'golongan_id' => $golongan->id,
                'unit_kerja_id' => $unitKerja->id,
                'email' => 'siti.nurhaliza@example.com',
                'username' => 'siti.nurhaliza'
            ],
            [
                'NIP' => '199206252015091003',
                'nama_lengkap' => 'Ahmad Fauzi',
                'tempat_lahir' => 'Bandung',
                'tgl_lahir' => '1992-06-25',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'alamat' => 'Jl. Asia Afrika No. 789 Bandung',
                'no_telp' => '083456789012',
                'jabatan_id' => $jabatan2->id,
                'golongan_id' => $golongan->id,
                'unit_kerja_id' => $unitKerja->id,
                'email' => 'ahmad.fauzi@example.com',
                'username' => 'ahmad.fauzi'
            ]
        ];
        
        foreach ($pegawais as $pegawaiData) {
            // Extract user data
            $email = $pegawaiData['email'];
            $username = $pegawaiData['username'];
            $fullName = $pegawaiData['nama_lengkap'];
            
            // Remove user-only data
            unset($pegawaiData['email']);
            unset($pegawaiData['username']);
            
            // Create pegawai
            $pegawai = Pegawai::firstOrCreate(['NIP' => $pegawaiData['NIP']], $pegawaiData);
            
            // Create user for pegawai
            if (!$pegawai->user) {
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $fullName,
                        'username' => $username,
                        'password' => bcrypt('password'),
                    ]
                );
                
                // Assign Pegawai role
                $user->assignRole($pegawaiRole);
            }
        }
    }
}
