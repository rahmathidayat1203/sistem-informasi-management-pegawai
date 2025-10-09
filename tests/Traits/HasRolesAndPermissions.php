<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Golongan;
use App\Models\UnitKerja;
use App\Models\JenisCuti;
use App\Models\SisaCuti;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

trait HasRolesAndPermissions
{
    /**
     * Create admin kepegawaian user.
     */
    protected function createAdminKepegawaian(): User
    {
        $role = Role::firstOrCreate(['name' => 'Admin Kepegawaian']);
        
        return User::factory()->create([
            'email' => 'admin.kepegawaian@test.com',
            'name' => 'Admin Kepegawaian Test'
        ])->assignRole($role);
    }

    /**
     * Create pimpinan user.
     */
    protected function createPimpinan(): User
    {
        $role = Role::firstOrCreate(['name' => 'Pimpinan']);
        
        return User::factory()->create([
            'email' => 'pimpinan@test.com',
            'name' => 'Pimpinan Test'
        ])->assignRole($role);
    }

    /**
     * Create admin keuangan user.
     */
    protected function createAdminKeuangan(): User
    {
        $role = Role::firstOrCreate(['name' => 'Admin Keuangan']);
        
        return User::factory()->create([
            'email' => 'admin.keuangan@test.com',
            'name' => 'Admin Keuangan Test'
        ])->assignRole($role);
    }

    /**
     * Create pegawai user.
     */
    protected function createPegawai(string $email = 'pegawai@test.com'): array
    {
        $role = Role::firstOrCreate(['name' => 'Pegawai']);
        
        $pegawai = Pegawai::factory()->create([
            'NIP' => '1234567890123456',
            'nama_lengkap' => 'Test Pegawai'
        ]);

        $user = User::factory()->create([
            'email' => $email,
            'name' => 'Test Pegawai User',
            'pegawai_id' => $pegawai->id
        ])->assignRole($role);

        return [$user, $pegawai];
    }

    /**
     * Setup complete pegawai data.
     */
    protected function setupCompletePegawai(): array
    {
        // Create required master data
        $jabatan = Jabatan::factory()->create(['nama' => 'Programmer']);
        $golongan = Golongan::factory()->create(['nama' => 'III/a']);
        $unitKerja = UnitKerja::factory()->create(['nama' => 'Bidang E-Government']);

        [$user, $pegawai] = $this->createPegawai();
        
        $pegawai->update([
            'jabatan_id' => $jabatan->id,
            'golongan_id' => $golongan->id,
            'unit_kerja_id' => $unitKerja->id
        ]);

        // Setup sisa cuti
        $currentYear = now()->year;
        for ($i = 0; $i < 3; $i++) {
            SisaCuti::factory()->create([
                'pegawai_id' => $pegawai->id,
                'tahun' => $currentYear - $i,
                'jatah_cuti' => 12,
                'sisa_cuti' => 12 - $i
            ]);
        }

        return [$user, $pegawai];
    }

    /**
     * Setup cuti jenis.
     */
    protected function setupJenisCuti(): array
    {
        $jenisCuti = [];
        
        $jenisCuti['tahunan'] = JenisCuti::factory()->create(['nama' => 'Cuti Tahunan']);
        $jenisCuti['sakit'] = JenisCuti::factory()->create(['nama' => 'Cuti Sakit']);
        $jenisCuti['besar'] = JenisCuti::factory()->create(['nama' => 'Cuti Besar']);
        
        return $jenisCuti;
    }

    /**
     * Login as user.
     */
    protected function actingAsUser(User $user): self
    {
        return $this->actingAs($user);
    }

    /**
     * Login as admin kepegawaian.
     */
    protected function actingAsAdminKepegawai(): self
    {
        $user = $this->createAdminKepegawaian();
        return $this->actingAs($user);
    }

    /**
     * Login as pimpinan.
     */
    protected function actingAsPimpinan(): self
    {
        $user = $this->createPimpinan();
        return $this->actingAs($user);
    }

    /**
     * Login as admin keuangan.
     */
    protected function actingAsAdminKeuangan(): self
    {
        $user = $this->createAdminKeuangan();
        return $this->actingAs($user);
    }

    /**
     * Login as pegawai.
     */
    protected function actingAsPegawai(): self
    {
        [$user] = $this->setupCompletePegawai();
        return $this->actingAs($user);
    }
}
