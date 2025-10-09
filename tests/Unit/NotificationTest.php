<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\Traits\HasRolesAndPermissions;
use Tests\Traits\HasNotifications;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Cuti;
use App\Models\JenisCuti;
use App\Models\PerjalananDinas;
use App\Models\LaporanPD;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationTest extends TestCase
{
    use RefreshDatabase, HasRolesAndPermissions, HasNotifications;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpNotificationFaking();
        $this->setupJenisCuti();
    }

    /** @test */
    public function cuti_approved_notification_contains_correct_data()
    {
        // Arrange
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();
        $pimpinan = $this->createPimpinan();

        $cuti = Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Diajukan',
            'pimpinan_approver_id' => $pimpinan->id
        ]);

        // Create and send notification
        $notification = new \App\Notifications\CutiApproved($cuti);
        $pegawaiUser->notify($notification);

        // Assert notification was sent
        $this->assertNotificationSent($pegawaiUser, \App\Notifications\CutiApproved::class);
        
        // Assert notification data
        $data = $this->getNotificationData($notification);
        $this->assertEquals('Pengajuan Cuti Disetujui', $data['title']);
        $this->assertStringContainsString('disetujui', $data['message']);
        $this->assertEquals($cuti->id, $data['cuti_id']);
        $this->assertEquals('success', $data['color']);
        $this->assertEquals('check-circle', $data['icon']);
    }

    /** @test */
    public function cuti_approved_notification_sends_email()
    {
        // Arrange
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();
        $pimpinan = $this->createPimpinan();

        $cuti = Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Disetujui',
            'pimpinan_approver_id' => $pimpinan->id
        ]);

        // Create notification
        $notification = new \App\Notifications\CutiApproved($cuti);
        
        // Assert email message is created
        $mailMessage = $notification->toMail($pegawaiUser);
        
        $this->assertStringContainsString('Cuti Disetujui', $mailMessage->subject);
        $this->assertStringContainsString($pegawaiUser->name, $mailMessage->greeting);
        $this->assertNotNull($mailMessage->actionUrl); // Action button exists
    }

    /** @test */
    public function cuti_rejected_notification_contains_correct_data()
    {
        // Arrange
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();
        $pimpinan = $this->createPimpinan();

        $cuti = Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Ditolak',
            'pimpinan_approver_id' => $pimpinan->id,
            'keterangan' => 'Cuti tahunan ditolak. Alasan Penolakan: Staff yang dibutuhkan'
        ]);

        // Act
        $notification = new \App\Notifications\CutiRejected($cuti);
        $pegawaiUser->notify($notification);

        // Assert
        $this->assertNotificationSent($pegawaiUser, \App\Notifications\CutiRejected::class);
        
        $data = $this->getNotificationData($notification);
        $this->assertEquals('Pengajuan Cuti Ditolak', $data['title']);
        $this->assertStringContainsString('ditolak', $data['message']);
        $this->assertEquals($cuti->id, $data['cuti_id']);
        $this->assertEquals('danger', $data['color']);
        $this->assertEquals('times-circle', $data['icon']);
        $this->assertStringContainsString('Staff yang dibutuhkan', $data['alasan']);
    }

    /** @test */
    public function laporan_pd_verified_notification_sent_to_pimpinan()
    {
        // Arrange
        $pimpinan = $this->createPimpinan();
        $adminKeuangan = $this->createAdminKeuangan();
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();

        $perjalananDinas = PerjalananDinas::factory()->create([
            'tempat_tujuan' => 'Jakarta'
        ]);
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $laporanPD = LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Disetujui',
            'admin_keuangan_verifier_id' => $adminKeuangan->id
        ]);

        // Act
        $notification = new \App\Notifications\LaporanPDVerified($laporanPD, 'verified');
        $pimpinan->notify($notification);

        // Assert
        $this->assertNotificationSent($pimpinan, \App\Notifications\LaporanPDVerified::class);
        
        $data = $this->getNotificationData($notification);
        $this->assertEquals('Laporan Perjalanan Dinas Disetujui', $data['title']);
        $this->assertStringContainsString('Jakarta', $data['message']);
        $this->assertEquals($laporanPD->id, $data['laporan_pd_id']);
        $this->assertEquals('success', $data['color']);
        $this->assertEquals('check-circle', $data['icon']);
    }

    /** @test */
    public function laporan_pd_rejected_notification_correct_status()
    {
        // Arrange
        $pimpinan = $this->createPimpinan();
        $adminKeuangan = $this->createAdminKeuangan();
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();

        $perjalananDinas = PerjalananDinas::factory()->create();
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $laporanPD = LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Perbaikan',
            'admin_keuangan_verifier_id' => $adminKeuangan->id,
            'alasan_penolakan' => 'Laporan tidak lengkap'
        ]);

        // Act
        $notification = new \App\Notifications\LaporanPDVerified($laporanPD, 'rejected');
        $pimpinan->notify($notification);

        // Assert
        $this->assertNotificationSent($pimpinan, \App\Notifications\LaporanPDVerified::class);
        
        $data = $this->getNotificationData($notification);
        $this->assertEquals('Laporan Perjalanan Dinas Ditolak', $data['title']);
        $this->assertEquals('rejected', $data['status']);
        $this->assertEquals('danger', $data['color']);
        $this->assertEquals('times-circle', $data['icon']);
    }

    /** @test */
    public function notification_via_channels_correct()
    {
        // Arrange
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();

        $cuti = Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Disetujui'
        ]);

        $notification = new \App\Notifications\CutiApproved($cuti);
        $notifiable = $pegawaiUser;

        // Act
        $channels = $notification->via($notifiable);

        // Assert
        $this->assertContains('mail', $channels);
        $this->assertContains('database', $channels);
        $this->assertCount(2, $channels);
    }

    /** @test */
    public function notification_database_structure_correct()
    {
        // Arrange
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();

        $cuti = Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Disetujui'
        ]);

        // Act
        $notification = new \App\Notifications\CutiApproved($cuti);
        $data = $notification->toArray($pegawaiUser);

        // Assert notification data structure
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('cuti_id', $data);
        $this->assertArrayHasKey('jenis_cuti', $data);
        $this->assertArrayHasKey('action_url', $data);
        $this->assertArrayHasKey('icon', $data);
        $this->assertArrayHasKey('color', $data);

        // Assert data types
        $this->assertIsString($data['title']);
        $this->assertIsString($data['message']);
        $this->assertIsInt($data['cuti_id']);
        $this->assertIsString($data['jenis_cuti']);
        $this->assertIsString($data['action_url']);
        $this->assertIsString($data['icon']);
        $this->assertIsString($data['color']);
    }
}
