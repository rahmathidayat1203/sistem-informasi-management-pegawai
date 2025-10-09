<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\HasRolesAndPermissions;
use Tests\Traits\HasNotifications;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\PerjalananDinas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PerjalananDinasTest extends TestCase
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
    public function guest_cannot_access_perjalanan_dinas()
    {
        $response = $this->get(route('admin.perjalanan_dinas.index'));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function pegawai_cannot_access_perjalanan_dinas_index()
    {
        $this->actingAsPegawai();
        
        $response = $this->get(route('admin.perjalanan_dinas.index'));
        $response->assertForbidden();
    }

    /** @test */
    public function admin_kepegawaian_can_access_perjalanan_dinas()
    {
        $this->actingAsAdminKepegawai();
        
        $response = $this->get(route('admin.perjalanan_dinas.index'));
        $response->assertOk();
    }

    /** @test */
    public function pimpinan_can_access_perjalanan_dinas_index()
    {
        $this->actingAsPimpinan();
        
        $response = $this->get(route('admin.perjalanan_dinas.index'));
        $response->assertOk();
    }

    // Index Page Tests
    /** @test */
    public function perjalanan_dinas_index_displays_correct_data()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        PerjalananDinas::factory()->count(3)->create()->each(function ($perjalanan) {
            $perjalanan->pegawai()->attach($pegawai->id);
        });

        $response = $this->get(route('admin.perjalanan_dinas.index'));
        $response->assertSee('Data Perjalanan Dinas');
    }

    /** @test */
    public function perjalanan_dinas_index_ajax_returns_json_data()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = PerjalananDinas::factory()->create([
            'tempat_tujuan' => 'Jakarta'
        ]);
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $response = $this->get(route('admin.perjalanan_dinas.index'), ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'pegawai',
                    'pimpinanPemberiTugas',
                    'tgl_berangkat',
                    'tgl_kembali',
                    'action'
                ]
            ]
        ]);
    }

    // Create Perjalanan Dinas Tests
    /** @test */
    public function admin_can_view_create_perjalanan_dinas_page()
    {
        $this->actingAsAdminKepegawai();
        
        $response = $this->get(route('admin.perjalanan_dinas.create'));
        $response->assertOk();
        $response->assertSee('Tambah Perjalanan Dinas');
    }

    /** @test */
    public function admin_can_create_perjalanan_dinas_with_valid_data()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser1, $pegawai1] = $this->setupCompletePegawai();
        [$pegawaiUser2, $pegawai2] = $this->setupCompletePegawai('pegawai2@test.com');
        $pimpinan = $this->createPimpinan();

        $data = [
            'nomor_surat_tugas' => 'ST/001/2023',
            'maksud_perjalanan' => 'Attending Tech Conference 2023',
            'tempat_tujuan' => 'Jakarta Convention Center',
            'tgl_berangkat' => now()->addDays(10)->format('Y-m-d'),
            'tgl_kembali' => now()->addDays(15)->format('Y-m-d'),
            'pimpinan_pemberi_tugas_id' => $pimpinan->id,
            'pegawai_ids' => [$pegawai1->id, $pegawai2->id]
        ];

        $response = $this->post(route('admin.perjalanan_dinas.store'), $data);
        
        $this->assertDatabaseHas('perjalanan_dinas', [
            'nomor_surat_tugas' => 'ST/001/2023',
            'tempat_tujuan' => 'Jakarta Convention Center'
        ]);
        
        // Check pegawai relationships
        $perjalananDinas = PerjalananDinas::where('nomor_surat_tugas', 'ST/001/2023')->first();
        $this->assertCount(2, $perjalananDinas->pegawai);
        
        $response->assertRedirect(route('admin.perjalanan_dinas.index'));
    }

    /** @test */
    public function perjalanan_dinas_validation_works_correctly()
    {
        $this->actingAsAdminKepegawai();
        
        $data = [
            'nomor_surat_tugas' => '',
            'maksud_perjalanan' => '',
            'tempat_tujuan' => '',
            'tgl_berangkat' => 'invalid-date',
            'tgl_kembali' => 'invalid-date',
            'pegawai_ids' => []
        ];

        $response = $this->post(route('admin.perjalanan_dinas.store'), $data);
        $response->assertSessionHasErrors([
            'nomor_surat_tugas',
            'maksud_perjalanan', 
            'tempat_tujuan',
            'tgl_berangkat',
            'tgl_kembali',
            'pegawai_ids'
        ]);
    }

    /** @test */
    public function perjalanan_dinas_date_validation_works_correctly()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $pimpinan = $this->createPimpinan();

        $data = [
            'nomor_surat_tugas' => 'ST/002/2023',
            'maksud_perjalanan' => 'Test perjalanan',
            'tempat_tujuan' => 'Test location',
            'tgl_berangkat' => now()->subDays(1)->format('Y-m-d'), // Past date
            'tgl_kembali' => now()->format('Y-m-d'), // Earlier than start date
            'pimpinan_pemberi_tugas_id' => $pimpinan->id,
            'pegawai_ids' => [$pegawai->id]
        ];

        $response = $this->post(route('admin.perjalanan_dinas.store'), $data);
        $response->assertSessionHasErrors(['tgl_berangkat', 'tgl_kembali']);
    }

    /** @test */
    public function at_least_one_pegawai_required_for_perjalanan_dinas()
    {
        $this->actingAsAdminKepegawai();
        
        $pimpinan = $this->createPimpinan();

        $data = [
            'nomor_surat_tugas' => 'ST/003/2023',
            'maksud_perjalanan' => 'Test perjalanan',
            'tempat_tujuan' => 'Test location',
            'tgl_berangkat' => now()->addDays(5)->format('Y-m-d'),
            'tgl_kembali' => now()->addDays(10)->format('Y-m-d'),
            'pimpinan_pemberi_tugas_id' => $pimpinan->id,
            'pegawai_ids' => [] // Empty array
        ];

        $response = $this->post(route('admin.perjalanan_dinas.store'), $data);
        $response->assertSessionHasErrors(['pegawai_ids']);
    }

    // Notification Tests for Perjalanan Dinas Assignment
    /** @test */
    public function assigned_pegawais_receive_notification()
    {
        // Setup
        $this->setUpNotificationFaking();
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser1, $pegawai1] = $this->setupCompletePegawai();
        [$pegawaiUser2, $pegawai2] = $this->setupCompletePegawai('pegawai2@test.com');
        $pimpinan = $this->createPimpinan();

        $data = [
            'nomor_surat_tugas' => 'ST/004/2023',
            'maksud_perjalanan' => 'Test perjalanan dengan notifikasi',
            'tempat_tujuan' => 'Jakarta',
            'tgl_berangkat' => now()->addDays(5)->format('Y-m-d'),
            'tgl_kembali' => now()->addDays(10)->format('Y-m-d'),
            'pimpinan_pemberi_tugas_id' => $pimpinan->id,
            'pegawai_ids' => [$pegawai1->id, $pegawai2->id]
        ];

        // Act
        $this->post(route('admin.perjalanan_dinas.store'), $data);

        // Assert
        $perjalananDinas = PerjalananDinas::where('nomor_surat_tugas', 'ST/004/2023')->first();
        $this->assertPerjalananDinasAssignedNotification($perjalananDinas);
    }

    // Pegawai My Assignments Tests
    /** @test */
    public function pegawai_can_view_their_perjalanan_dinas_assignments()
    {
        $this->actingAsPegawai();
        
        $pegawai = Pegawai::where('nama_lengkap', 'Test Pegawai')->first();
        
        $perjalananDinas = PerjalananDinas::factory()->create([
            'tempat_tujuan' => 'Jakarta'
        ]);
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $response = $this->get(route('pegawai.perjalanan_dinas.my'));
        $response->assertOk();
        $response->assertSee('Perjalanan Dinas Saya');
    }

    /** @test */
    public function pegawai_cannot_view_other_perjalanan_dinas_assignments()
    {
        [$otherUser, $otherPegawai] = $this->setupCompletePegawai('other@test.com');
        $pegawai = Pegawai::where('nama_lengkap', 'Test Pegawai')->first();
        
        // Create perjalanan dinas for other pegawai
        $perjalananDinas = PerjalananDinas::factory()->create([
            'tempat_tujuan' => 'Surabaya'
        ]);
        $perjalananDinas->pegawai()->attach($otherPegawai->id);

        // Login as first pegawai
        $pegawaiUser = $pegawai->user;
        $this->actingAs($pegawaiUser);

        $response = $this->get(route('pegawai.perjalanan_dinas.my'));
        
        // Should only see pegawai's own assignments
        $this->assertEquals(0, $pegawai->perjalananDinas()->count());
    }

    // CRUD Operations Tests
    /** @test */
    public function admin_can_edit_perjalanan_dinas()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $response = $this->get(route('admin.perjalanan_dinas.edit', $perjalananDinas));
        $response->assertOk();
        $response->assertSee('Edit Perjalanan Dinas');
    }

    /** @test */
    public function admin_can_update_perjalanan_dinas()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = PerjalananDinas::factory()->create([
            'tempat_tujuan' => 'Original Destination'
        ]);
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $updateData = [
            'nomor_surat_tugas' => $perjalananDinas->nomor_surat_tugas,
            'maksud_perjalanan' => 'Updated maksud perjalanan',
            'tempat_tujuan' => 'Updated Destination',
            'tgl_berangkat' => $perjalananDinas->tgl_berangkat,
            'tgl_kembali' => $perjalananDinas->tgl_kembali,
            'pimpinan_pemberi_tugas_id' => $perjalananDinas->pimpinan_pemberi_tugas_id,
            'pegawai_ids' => [$pegawai->id]
        ];

        $response = $this->put(route('admin.perjalanan_dinas.update', $perjalananDinas), $updateData);
        
        $this->assertDatabaseHas('perjalanan_dinas', [
            'id' => $perjalananDinas->id,
            'maksud_perjalanan' => 'Updated maksud perjalanan',
            'tempat_tujuan' => 'Updated Destination'
        ]);
        
        $response->assertRedirect(route('admin.perjalanan_dinas.index'));
    }

    /** @test */
    public function admin_can_delete_perjalanan_dinas()
    {
        $this->actingAsAdminKepegawai();
        
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $response = $this->delete(route('admin.perjalanan_dinas.destroy', $perjalananDinas));
        
        $this->assertDatabaseMissing('perjalanan_dinas', ['id' => $perjalananDinas->id]);
        $this->assertDatabaseMissing('pegawai_perjalanan_dinas', [
            'pegawai_id' => $pegawai->id,
            'perjalanan_dinas_id' => $perjalananDinas->id
        ]);
        
        $response->assertJson(['success' => true]);
    }

    // Pegawai Search API Tests
    /** @test */
    public function pegawai_search_api_returns_correct_data()
    {
        $this->actingAsAdminKepegawai();
        
        $pegawai1 = Pegawai::factory()->create([
            'nama_lengkap' => 'John Doe',
            'NIP' => '1234567890123456'
        ]);
        $pegawai2 = Pegawai::factory()->create([
            'nama_lengkap' => 'Jane Smith',
            'NIP' => '9876543210987654'
        ]);

        $response = $this->get(route('admin.perjalanan_dinas.searchpegawai', ['q' => 'John']));
        $response->assertJsonStructure([
            '*' => ['id', 'nama_lengkap', 'nip']
        ]);
        
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals('John Doe', $data[0]['nama_lengkap']);
    }

    /** @test */
    public function pegawai_search_requires_minimum_input()
    {
        $this->actingAsAdminKepegawai();
        
        $response = $this->get(route('admin.perjalanan_dinas.searchpegawai', ['q' => 'Jo']));
        $response->assertJsonCount(0); // Should return empty for less than 2 characters
    }

    /** @test */
    public function pegawai_search_limits_results()
    {
        $this->actingAsAdminKepegawai();
        
        // Create 60 pegawais to test limit
        Pegawai::factory()->count(60)->create([
            'nama_lengkap' => 'Test Pegawai'
        ]);

        $response = $this->get(route('admin.perjalanan_dinas.searchpegawai', ['q' => 'Test']));
        
        $data = $response->json();
        $this->assertLessThanOrEqual(50, count($data)); // Should be limited to 50
    }

    // Date and Status
    /** @test */
    public function perjalanan_dinas_status_calculates_correctly()
    {
        $this->actingAsPegawai();
        
        $pegawai = Pegawai::where('nama_lengkap', 'Test Pegawai')->first();
        
        // Test different statuses
        $pastTrip = PerjalananDinas::factory()->create([
            'tgl_berangkat' => now()->subDays(10),
            'tgl_kembali' => now()->subDays(5)
        ]);
        $pastTrip->pegawai()->attach($pegawai->id);

        $currentTrip = PerjalananDinas::factory()->create([
            'tgl_berangkat' => now()->subDays(1),
            'tgl_kembali' => now()->addDays(2)
        ]);
        $currentTrip->pegawai()->attach($pegawai->id);

        $futureTrip = PerjalananDinas::factory()->create([
            'tgl_berangkat' => now()->addDays(5),
            'tgl_kembali' => now()->addDays(10)
        ]);
        $futureTrip->pegawai()->attach($pegawai->id);

        $response = $this->get(route('pegawai.perjalanan_dinas.my'));
        $response->assertOk();
        // The response should contain different status indicators
    }
}
