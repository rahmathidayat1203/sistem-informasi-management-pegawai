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
        // Create test users for each role
        $users = [
            [
                'name' => 'Test Admin Kepegawaian',
                'email' => 'admin@sipeg.test',
                'username' => 'admin',
                'role' => 'Admin Kepegawaian',
            ],
            [
                'name' => 'Test Pegawai',
                'email' => 'pegawai@sipeg.test',
                'username' => 'pegawai',
                'role' => 'Pegawai',
            ],
            [
                'name' => 'Test Pimpinan',
                'email' => 'pimpinan@sipeg.test',
                'username' => 'pimpinan',
                'role' => 'Pimpinan',
            ],
            [
                'name' => 'Test Admin Keuangan',
                'email' => 'keuangan@sipeg.test',
                'username' => 'keuangan',
                'role' => 'Admin Keuangan',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'password' => bcrypt('password'),
                ]
            );
            
            // Assign role
            $user->assignRole($userData['role']);
        }
    }
}