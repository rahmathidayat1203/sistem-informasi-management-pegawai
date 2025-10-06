<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super-admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Create super admin user
        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'username' => 'admin',
            'password' => Hash::make('password')
        ]);

        // Assign role to user
        $superAdminUser->assignRole($superAdminRole);
    }
}