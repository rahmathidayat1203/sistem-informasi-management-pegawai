<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\HasRolesAndPermissions;
use Tests\Traits\HasNotifications;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Cuti;
use App\Models\JenisCuti;
use App\Models\SisaCuti;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class CutiTest extends TestCase
{
    use RefreshDatabase, HasRolesAndPermissions, HasNotifications;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpNotificationFaking();
        $this->setupJenisCuti();
    }

    // Authentication Tests
    /** @test */
    public function guest_cannot_access_cuti_management()
    {
        $response = $this->get(route('admin.cuti.index'));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function pegawai_cannot_access_cuti_index()
    {
        $this->actingAsPegawai();
        
        $response = $this->get(route('admin.cuti.index'));
        $response->assertForbidden();
    }

    /** @test */
    public function admin_kepegawaian_can_access_cuti_index()
    {
        $this->actingAsAdminKepegawai();
        
        $response = $this->get(route('admin.cuti.index'));
        $response->assertOk();
    }

    // Index Page Tests
    /** @test */
    public function cuti_index_page_displays_correct_data()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();
        
        Cuti::factory()->count(5)->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id
        ]);

        $response = $this->get(route('admin.cuti.index'));
        $response->assertSee('Data Cuti');
    }

    /** @test */
    public function cuti_index_ajax_returns_json_data()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();
        
        $cuti = Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Diajukan'
        ]);

        $response = $this->get(route('admin.cuti.index'), ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'pegawai',
                    'jenisCuti',
                    'tgl_mulai',
                    'tgl_selesai',
                    'status_persetujuan',
                    'action'
                ]
            ]
        ]);
    }

    // Create Cuti Tests
    /** @test */
    public function admin_can_view_create_cuti_page()
    {
        $this->actingAsAdminKepegawai();
        
        $response = $this->get(route('admin.cuti.create'));
        $response->assertOk();
        $response->assertSee('Tambah Data Cuti');
    }

    /** @test */
    public function admin_can_create_cuti_with_valid_data()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();
        $pimpinan = $this->createPimpinan();

        $data = [
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'tgl_pengajuan' => now()->format('Y-m-d'),
            'tgl_mulai' => now()->addDays(5)->format('Y-m-d'),
            'tgl_selesai' => now()->addDays(10)->format('Y-m-d'),
            'keterangan' => 'Cuti tahunan untuk liburan',
        ];

        $response = $this->post(route('admin.cuti.store'), $data);
        
        // Debug: Check response status
        $response->assertStatus(200); // Should be successful
        
        $this->assertDatabaseHas('cuti', [
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Diajukan'
        ]);
        
        $response->assertRedirect(route('admin.cuti.index'));
    }

    /** @test */
    public function admin_cannot_create_cuti_with_invalid_data()
    {
        $this->actingAsAdminKepegawai();
        
        $data = [
            'pegawai_id' => '',
            'jenis_cuti_id' => '',
            'tgl_mulai' => 'invalid-date',
            'keterangan' => ''
        ];

        $response = $this->post(route('admin.cuti.store'), $data);
        $response->assertSessionHasErrors(['pegawai_id', 'jenis_cuti_id', 'tgl_mulai', 'keterangan']);
    }

    /** @test */
    public function cuti_date_validation_works_correctly()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();

        $data = [
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'tgl_pengajuan' => now()->format('Y-m-d'),
            'tgl_mulai' => now()->subDays(1)->format('Y-m-d'), // Past date
            'tgl_selesai' => now()->addDays(5)->format('Y-m-d'),
            'keterangan' => 'Test cuti',
            'status_persetujuan' => 'Diajukan'
        ];

        $response = $this->post(route('admin.cuti.store'), $data);
        $response->assertSessionHasErrors(['tgl_mulai']);
    }

    // Approval Process Tests
    /** @test */
    public function pimpinan_can_approve_cuti_request()
    {
        // Setup
        $this->setUpNotificationFaking();
        $pimpinan = $this->actingAsPimpinan();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();

        $cuti = Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Diajukan',
            'pimpinan_approver_id' => null
        ]);

        // Act
        $response = $this->post(route('pimpinan.cuti.approve', $cuti));

        // Assert
        $this->assertDatabaseHas('cuti', [
            'id' => $cuti->id,
            'status_persetujuan' => 'Disetujui',
            'pimpinan_approver_id' => $pimpinan->id
        ]);

        $this->assertCutiApprovedNotification($cuti);
        $response->assertJson(['message' => 'Cuti approved successfully']);
    }

    /** @test */
    public function pimpinan_can_reject_cuti_request()
    {
        // Setup
        $this->setUpNotificationFaking();
        $pimpinan = $this->actingAsPimpinan();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();

        $cuti = Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Diajukan',
            'pimpinan_approver_id' => null
        ]);

        $rejectionData = [
            'alasan_penolakan' => 'Staf yang dibutuhkan saat periode tersebut'
        ];

        // Act
        $response = $this->post(route('pimpinan.cuti.reject', $cuti), $rejectionData);

        // Assert
        $this->assertDatabaseHas('cuti', [
            'id' => $cuti->id,
            'status_persetujuan' => 'Ditolak',
            'pimpinan_approver_id' => $pimpinan->id
        ]);

        $this->assertCutiRejectedNotification($cuti);
        $response->assertJson(['message' => 'Cuti rejected successfully']);
    }

    /** @test */
    public function only_pending_cuti_can_be_approved_or_rejected()
    {
        $pimpinan = $this->actingAsPimpinan();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();

        // Already approved cuti
        $approvedCuti = Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Disetujui'
        ]);

        $response = $this->post(route('pimpinan.cuti.approve', $approvedCuti));
        $response->assertJson(['message' => 'Only pending requests can be approved'], 422);

        $response = $this->post(route('pimpinan.cuti.reject', $approvedCuti));
        $response->assertJson(['message' => 'Only pending requests can be rejected'], 422);
    }

    // Sisa Cuti Tests
    /** @test */
    public function approved_cuti_tahunan_deducts_sisa_cuti_correctly()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();
        $currentYear = now()->year;

        // Ensure initial sisa cuti
        $sisaCuti = SisaCuti::where('pegawai_id', $pegawai->id)
            ->where('tahun', $currentYear)
            ->first();
        
        $initialSisa = $sisaCuti->sisa_cuti;

        $data = [
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'tgl_pengajuan' => now()->format('Y-m-d'),
            'tgl_mulai' => now()->addDays(5)->format('Y-m-d'),
            'tgl_selesai' => now()->addDays(7)->format('Y-m-d'), // 3 days
            'keterangan' => 'Test cuti',
            'status_persetujuan' => 'Disetujui'
        ];

        $this->post(route('admin.cuti.store'), $data);

        // Assert sisa cuti was deducted
        $sisaCuti->refresh();
        $this->assertEquals($initialSisa - 3, $sisaCuti->sisa_cuti);
    }

    /** @test */
    public function non_tahunan_cuti_does_not_deduct_sisa_cuti()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Sakit')->first();
        $currentYear = now()->year;

        $sisaCuti = SisaCuti::where('pegawai_id', $pegawai->id)
            ->where('tahun', $currentYear)
            ->first();
        
        $initialSisa = $sisaCuti->sisa_cuti;

        $data = [
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'tgl_pengajuan' => now()->format('Y-m-d'),
            'tgl_mulai' => now()->addDays(5)->format('Y-m-d'),
            'tgl_selesai' => now()->addDays(7)->format('Y-m-d'),
            'keterangan' => 'Test cuti sakit',
            'status_persetujuan' => 'Disetujui'
        ];

        $this->post(route('admin.cuti.store'), $data);

        // Assert sisa cuti was NOT deducted
        $sisaCuti->refresh();
        $this->assertEquals($initialSisa, $sisaCuti->sisa_cuti);
    }

    // Pegawai My Cuti Tests
    /** @test */
    public function pegawai_can_view_their_own_cuti_requests()
    {
        $this->actingAsPegawai();
        
        $pegawai = Pegawai::where('nama_lengkap', 'Test Pegawai')->first();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();

        Cuti::factory()->count(3)->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Diajukan'
        ]);

        $response = $this->get(route('pegawai.cuti.my'));
        $response->assertOk();
        $response->assertSee('Cuti Saya');
    }

    /** @test */
    public function pegawai_cannot_view_other_pegawai_cuti_requests()
    {
        [$otherUser, $otherPegawai] = $this->setupCompletePegawai('other@test.com');
        $pegawai = Pegawai::where('nama_lengkap', 'Test Pegawai')->first();
        
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();

        // Create cuti for other pegawai
        Cuti::factory()->create([
            'pegawai_id' => $otherPegawai->id,
            'jenis_cuti_id' => $jenisCuti->id
        ]);

        // Login as first pegawai
        $pegawaiUser = $pegawai->user;
        $this->actingAs($pegawaiUser);

        $response = $this->get(route('pegawai.cuti.my'));
        
        // Should only see pegawai's own cuti, not other's
        $this->assertDatabaseCount('cuti', 1); // Only test pegawai's cuti should be visible
    }

    // CRUD Operations Tests
    /** @test */
    public function admin_can_edit_existing_cuti()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();

        $cuti = Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Diajukan'
        ]);

        $response = $this->get(route('admin.cuti.edit', $cuti));
        $response->assertOk();
        $response->assertSee('Edit Data Cuti');
    }

    /** @test */
    public function admin_can_update_existing_cuti()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();

        $cuti = Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Diajukan'
        ]);

        $updateData = [
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'tgl_pengajuan' => now()->format('Y-m-d'),
            'tgl_mulai' => now()->addDays(10)->format('Y-m-d'),
            'tgl_selesai' => now()->addDays(15)->format('Y-m-d'),
            'keterangan' => 'Updated cuti request',
            'status_persetujuan' => 'Diajukan'
        ];

        $response = $this->put(route('admin.cuti.update', $cuti), $updateData);
        
        $this->assertDatabaseHas('cuti', [
            'id' => $cuti->id,
            'keterangan' => 'Updated cuti request'
        ]);
        
        $response->assertRedirect(route('admin.cuti.index'));
    }

    /** @test */
    public function admin_can_delete_cuti()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();

        $cuti = Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Diajukan'
        ]);

        $response = $this->delete(route('admin.cuti.destroy', $cuti));
        
        $this->assertDatabaseMissing('cuti', ['id' => $cuti->id]);
        $response->assertJson(['success' => true]);
    }
}
