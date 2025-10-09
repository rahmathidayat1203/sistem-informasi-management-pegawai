<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\HasRolesAndPermissions;
use Tests\Traits\HasNotifications;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\PerjalananDinas;
use App\Models\LaporanPD;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class LaporanPDTest extends TestCase
{
    use RefreshDatabase, HasRolesAndPermissions, HasNotifications;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpNotificationFaking();
        
        // Setup storage for file uploads
        Storage::fake('public');
    }

    // Authentication Tests
    /** @test */
    public function guest_cannot_access_laporan_pd()
    {
        $response = $this->get(route('admin.laporan_pd.index'));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function pegawai_cannot_access_laporan_pd_index()
    {
        $this->actingAsPegawai();
        
        $response = $this->get(route('admin.laporan_pd.index'));
        $response->assertForbidden();
    }

    /** @test */
    public function admin_kepegawaian_can_access_laporan_pd()
    {
        $this->actingAsAdminKepegawai();
        
        $response = $this->get(route('admin.laporan_pd.index'));
        $response->assertOk();
    }

    /** @test */
    public function admin_keuangan_can_access_laporan_pd_verification()
    {
        $this->actingAsAdminKeuangan();
        
        $response = $this->get(route('keuangan.laporan_pd.verification'));
        $response->assertOk();
    }

    // Regular Laporan PD Management Tests
    /** @test */
    public function admin_can_view_laporan_pd_index()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);
        
        LaporanPD::factory()->count(3)->create([
            'perjalanan_dinas_id' => $perjalananDinas->id
        ]);

        $response = $this->get(route('admin.laporan_pd.index'));
        $response->assertOk();
        $response->assertSee('Data Laporan Perjalanan Dinas');
    }

    /** @test */
    public function laporan_pd_index_ajax_returns_json_data()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = PerjalananDinas::factory()->create([
            'nomor_surat_tugas' => 'ST/001/2023'
        ]);
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $laporanPD = LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Belum Diverifikasi'
        ]);

        $response = $this->get(route('admin.laporan_pd.index'), ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'perjalananDinas',
                    'tgl_unggah',
                    'status_verifikasi',
                    'action'
                ]
            ]
        ]);
    }

    // Create Laporan PD Tests
    /** @test */
    public function admin_can_create_laporan_pd_with_file_upload()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $file = UploadedFile::fake()->create('laporan.pdf', 1000);

        $data = [
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'file_laporan' => $file,
            'status_verifikasi' => 'Belum Diverifikasi'
        ];

        $response = $this->post(route('admin.laporan_pd.store'), $data);
        
        $this->assertDatabaseHas('laporan_pd', [
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Belum Diverifikasi'
        ]);
        
        // Check file was stored
        $laporanPD = LaporanPD::where('perjalanan_dinas_id', $perjalananDinas->id)->first();
        $this->assertNotNull($laporanPD->file_laporan);
        
        $response->assertRedirect(route('admin.laporan_pd.index'));
    }

    /** @test */
    public function laporan_pd_validation_works_correctly()
    {
        $this->actingAsAdminKepegawai();
        
        $data = [
            'perjalanan_dinas_id' => '',
            'status_verifikasi' => 'invalid_status'
        ];

        $response = $this->post(route('admin.laporan_pd.store'), $data);
        $response->assertSessionHasErrors(['perjalanan_dinas_id', 'status_verifikasi']);
    }

    /** @test */
    public function laporan_pd_file_upload_validation_works()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        // Test invalid file type
        $invalidFile = UploadedFile::fake()->create('document.txt', 1000);
        
        $data = [
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'file_laporan' => $invalidFile,
            'status_verifikasi' => 'Belum Diverifikasi'
        ];

        $response = $this->post(route('admin.laporan_pd.store'), $data);
        $response->assertSessionHasErrors(['file_laporan']);
    }

    // Verification Dashboard Tests (Admin Keuangan)
    /** @test */
    public function verification_dashboard_displays_only_unverified_reports()
    {
        $this->actingAsAdminKeuangan();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $pimpinan = $this->createPimpinan();
        $adminKeuangan = $this->createAdminKeuangan();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        // Create reports with different statuses
        LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Belum Diverifikasi'
        ]);
        
        LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Disetujui',
            'admin_keuangan_verifier_id' => $adminKeuangan->id
        ]);

        LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Perbaikan',
            'admin_keuangan_verifier_id' => $adminKeuangan->id
        ]);

        $response = $this->get(route('keuangan.laporan_pd.verification'));
        $response->assertOk();
        $response->assertSee('Verifikasi Laporan Perjalanan Dinas');
    }

    /** @test */
    public function verification_stats_api_returns_correct_data()
    {
        $this->actingAsAdminKeuangan();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $adminKeuangan = $this->createAdminKeuangan();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        // Create reports with different statuses and dates
        LaporanPD::factory()->count(3)->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Belum Diverifikasi'
        ]);
        
        LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Disetujui',
            'admin_keuangan_verifier_id' => $adminKeuangan->id,
            'updated_at' => now() // Today
        ]);

        LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Perbaikan',
            'admin_keuangan_verifier_id' => $adminKeuangan->id,
            'updated_at' => now() // Today
        ]);

        $response = $this->get(route('keuangan.laporan_pd.verificationStats'));
        $response->assertJson([
            'pending' => 3,
            'approved_today' => 1,
            'rejected_today' => 1
        ]);
    }

    // Verification Process Tests
    /** @test */
    public function admin_keuangan_can_verify_laporan_pd()
    {
        // Setup
        $this->setUpNotificationFaking();
        $this->actingAsAdminKeuangan();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $pimpinan = $this->createPimpinan();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $laporanPD = LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Belum Diverifikasi'
        ]);

        $verifyData = [
            'catatan_verifikasi' => 'Laporan lengkap dan sesuai format'
        ];

        // Act
        $response = $this->post(route('keuangan.laporan_pd.verify', $laporanPD), $verifyData);

        // Assert
        $this->assertDatabaseHas('laporan_pd', [
            'id' => $laporanPD->id,
            'status_verifikasi' => 'Disetujui',
            'admin_keuangan_verifier_id' => auth()->id(),
            'catatan_verifikasi' => 'Laporan lengkap dan sesuai format'
        ]);

        // Assert notification sent to pimpinan
        $this->assertLaporanPDVerifiedNotification($laporanPD, 'verified');
        
        $response->assertJson(['message' => 'Laporan PD verified successfully and notifications sent to pimpinan']);
    }

    /** @test */
    public function admin_keuangan_can_reject_laporan_pd()
    {
        // Setup
        $this->setUpNotificationFaking();
        $this->actingAsAdminKeuangan();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $laporanPD = LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Belum Diverifikasi'
        ]);

        $rejectData = [
            'alasan_penolakan' => 'Laporan tidak lengkap, missing signature page'
        ];

        // Act
        $response = $this->post(route('keuangan.laporan_pd.reject', $laporanPD), $rejectData);

        // Assert
        $this->assertDatabaseHas('laporan_pd', [
            'id' => $laporanPD->id,
            'status_verifikasi' => 'Perbaikan',
            'admin_keuangan_verifier_id' => auth()->id(),
            'alasan_penolakan' => 'Laporan tidak lengkap, missing signature page'
        ]);

        // Assert notification sent to pimpinan
        $this->assertLaporanPDVerifiedNotification($laporanPD, 'rejected');
        
        $response->assertJson(['message' => 'Laporan PD rejected successfully and notifications sent to pimpinan']);
    }

    /** @test */
    public function verification_requires_rejection_reason()
    {
        $this->actingAsAdminKeuangan();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $laporanPD = LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Belum Diverifikasi'
        ]);

        // Try to reject without reason
        $response = $this->post(route('keuangan.laporan_pd.reject', $laporanPD), []);
        $response->assertSessionHasErrors(['alasan_penolakan']);
    }

    /** @test */
    public function only_unverified_reports_can_be_verified()
    {
        $this->actingAsAdminKeuangan();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        // Already verified report
        $verifiedReport = LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Disetujui'
        ]);

        // Already rejected report
        $rejectedReport = LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Perbaikan'
        ]);

        $response = $this->post(route('keuangan.laporan_pd.verify', $verifiedReport));
        $response->assertJson(['message' => 'Only unverified reports can be verified'], 422);

        $response = $this->post(route('keuangan.laporan_pd.reject', $rejectedReport));
        $response->assertJson(['message' => 'Only unverified reports can be rejected'], 422);
    }

    // Pegawai My Reports Tests
    /** @test */
    public function pegawai_can_view_their_laporan_reports()
    {
        $this->actingAsPegawai();
        
        $pegawai = Pegawai::where('nama_lengkap', 'Test Pegawai')->first();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        LaporanPD::factory()->count(3)->create([
            'perjalanan_dinas_id' => $perjalananDinas->id
        ]);

        $response = $this->get(route('pegawai.laporan_pd.my'));
        $response->assertOk();
        $response->assertSee('Laporan Perjalanan Dinas Saya');
    }

    /** @test */
    public function pegawai_can_create_laporan_for_their_assignment()
    {
        $this->actingAsPegawai();
        
        $pegawai = Pegawai::where('nama_lengkap', 'Test Pegawai')->first();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $file = UploadedFile::fake()->create('laporan-pegawai.pdf', 800);

        $data = [
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'file_laporan' => $file,
            'status_verifikasi' => 'Belum Diverifikasi'
        ];

        $response = $this->post(route('pegawai.laporan_pd.store'), $data);
        
        $this->assertDatabaseHas('laporan_pd', [
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Belum Diverifikasi'
        ]);
    }

    // Update and Delete Tests
    /** @test */
    public function admin_can_update_laporan_pd()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $laporanPD = LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Belum Diverifikasi'
        ]);

        // Update with new file
        $newFile = UploadedFile::fake()->create('laporan-updated.pdf', 1200);
        
        $updateData = [
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'file_laporan' => $newFile,
            'status_verifikasi' => 'Belum Diverifikasi'
        ];

        $response = $this->put(route('admin.laporan_pd.update', $laporanPD), $updateData);
        
        $laporanPD->refresh();
        $this->assertStringContains('laporan-updated', $laporanPD->file_laporan);
        
        $response->assertRedirect(route('admin.laporan_pd.index'));
    }

    /** @test */
    public function admin_can_delete_laporan_pd()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        // Create laporan with file
        $file = UploadedFile::fake()->create('laporan-to-delete.pdf', 500);
        $laporanPD = LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'file_laporan' => 'laporan_pd/' . $file->hashName()
        ]);

        $response = $this->delete(route('admin.laporan_pd.destroy', $laporanPD));
        
        $this->assertDatabaseMissing('laporan_pd', ['id' => $laporanPD->id]);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function unique_perjalanan_dinas_constraint()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        // Create first laporan
        $laporanPD1 = LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Belum Diverifikasi'
        ]);

        // Try to create second laporan for same perjalanan dinas
        $file = UploadedFile::fake()->create('duplicate.pdf', 500);
        
        $data = [
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'file_laporan' => $file,
            'status_verifikasi' => 'Belum Diverifikasi'
        ];

        $response = $this->post(route('admin.laporan_pd.store'), $data);
        $response->assertSessionHasErrors(['perjalanan_dinas_id']);
    }
}
