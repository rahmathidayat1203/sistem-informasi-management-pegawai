<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test admin user
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@sipeg.test',
            'password' => bcrypt('password'),
            'username' => 'testadmin',
        ]);

        // Assign admin role
        $admin->assignRole('Admin Kepegawaian');
        
        // Create a test employee user
        $pegawai = User::create([
            'name' => 'Test Pegawai',
            'email' => 'pegawai@sipeg.test',
            'password' => bcrypt('password'),
            'username' => 'pegawai',
        ]);

        // Assign pegawai role
        $pegawai->assignRole('Pegawai');
        
        // Create a test pimpinan user
        $pimpinan = User::create([
            'name' => 'Test Pimpinan',
            'email' => 'pimpinan@sipeg.test',
            'password' => bcrypt('password'),
            'username' => 'pimpinan',
        ]);

        // Assign pimpinan role
        $pimpinan->assignRole('Pimpinan');
        
        // Create a test admin keuangan user
        $adminKeuangan = User::create([
            'name' => 'Test Admin Keuangan',
            'email' => 'keuangan@sipeg.test',
            'password' => bcrypt('password'),
            'username' => 'keuangan',
        ]);

        // Assign admin keuangan role
        $adminKeuangan->assignRole('Admin Keuangan');
    }
}