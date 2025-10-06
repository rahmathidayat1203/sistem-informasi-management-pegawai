<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Pegawai permissions
            'view pegawai',
            'create pegawai',
            'edit pegawai',
            'delete pegawai',
            
            // Jabatan permissions
            'view jabatan',
            'create jabatan',
            'edit jabatan',
            'delete jabatan',
            
            // Golongan permissions
            'view golongan',
            'create golongan',
            'edit golongan',
            'delete golongan',
            
            // Unit Kerja permissions
            'view unit_kerja',
            'create unit_kerja',
            'edit unit_kerja',
            'delete unit_kerja',
            
            // Pendidikan permissions
            'view pendidikan',
            'create pendidikan',
            'edit pendidikan',
            'delete pendidikan',
            
            // Keluarga permissions
            'view keluarga',
            'create keluarga',
            'edit keluarga',
            'delete keluarga',
            
            // Riwayat Pangkat permissions
            'view riwayat_pangkat',
            'create riwayat_pangkat',
            'edit riwayat_pangkat',
            'delete riwayat_pangkat',
            
            // Riwayat Jabatan permissions
            'view riwayat_jabatan',
            'create riwayat_jabatan',
            'edit riwayat_jabatan',
            'delete riwayat_jabatan',
            
            // Jenis Cuti permissions
            'view jenis_cuti',
            'create jenis_cuti',
            'edit jenis_cuti',
            'delete jenis_cuti',
            
            // Cuti permissions
            'view cuti',
            'create cuti',
            'edit cuti',
            'delete cuti',
            'approve cuti',
            
            // Perjalanan Dinas permissions
            'view perjalanan_dinas',
            'create perjalanan_dinas',
            'edit perjalanan_dinas',
            'delete perjalanan_dinas',
            'assign perjalanan_dinas',
            
            // Laporan PD permissions
            'view laporan_pd',
            'create laporan_pd',
            'edit laporan_pd',
            'delete laporan_pd',
            'verify laporan_pd',
            
            // User management permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles if they don't exist
        $adminKepegawaian = Role::firstOrCreate(['name' => 'Admin Kepegawaian']);
        $pegawai = Role::firstOrCreate(['name' => 'Pegawai']);
        $pimpinan = Role::firstOrCreate(['name' => 'Pimpinan']);
        $adminKeuangan = Role::firstOrCreate(['name' => 'Admin Keuangan']);

        // Admin Kepegawaian permissions - full access
        $adminKepegawaian->syncPermissions(Permission::all());

        // Get specific permissions for each role
        $pegawaiPermissions = [
            'view pegawai', // can view their own profile
            'edit pegawai', // can edit their own profile
            'view pendidikan',
            'create pendidikan',
            'edit pendidikan',
            'delete pendidikan',
            'view keluarga',
            'create keluarga',
            'edit keluarga',
            'delete keluarga',
            'view riwayat_pangkat',
            'view riwayat_jabatan',
            'view cuti',
            'create cuti',
            'edit cuti',
            'delete cuti',
            'view perjalanan_dinas',
            'view laporan_pd',
        ];

        $pimpinanPermissions = [
            'view pegawai',
            'view jabatan',
            'view golongan',
            'view unit_kerja',
            'view cuti',
            'approve cuti',
            'view perjalanan_dinas',
            'create perjalanan_dinas',
            'edit perjalanan_dinas',
            'assign perjalanan_dinas',
            'view laporan_pd',
            'verify laporan_pd',
        ];

        $adminKeuanganPermissions = [
            'view laporan_pd',
            'verify laporan_pd',
            'view perjalanan_dinas',
        ];

        // Assign permissions to roles
        $pegawai->syncPermissions($pegawaiPermissions);
        $pimpinan->syncPermissions($pimpinanPermissions);
        $adminKeuangan->syncPermissions($adminKeuanganPermissions);
    }
}