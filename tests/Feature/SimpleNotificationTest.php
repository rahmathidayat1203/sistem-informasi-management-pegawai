<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\HasRolesAndPermissions;
use Tests\Traits\HasNotifications;
use App\Models\User;
use App\Notifications\CutiApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Simple test to verify notification basic functionality
 */
class SimpleNotificationTest extends TestCase
{
    use RefreshDatabase, HasRolesAndPermissions, HasNotifications;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpNotificationFaking();
        $this->setupJenisCuti();
    }

    /** @test */
    public function basic_notification_creation_works()
    {
        // Create user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User'
        ]);

        // Create a simple notification
        $notification = new \App\Notifications\CutiApproved(null);
        $user->notify($notification);

        // Verify notification was sent
        $this->assertNotificationSent($user, \App\Notifications\CutiApproved::class);
    }

    /** @test */
    public function notification_data_structure_is_correct()
    {
        // Create user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User'
        ]);

        // Create cuti for testing
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();
        $pimpinan = $this->createPimpinan();

        $cuti = \App\Models\Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Disetujui',
            'pimpinan_approver_id' => $pimpinan->id
        ]);

        // Create notification
        $notification = new \App\Notifications\CutiApproved($cuti);
        $user->notify($notification);

        // Test data structure
        $data = $this->getNotificationData($notification);
        
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('cuti_id', $data);
        $this->assertArrayHasKey('icon', $data);
        $this->assertArrayHasKey('color', $data);
    }

    /** @test */
    public function email_notification_generation_works()
    {
        $user = User::factory()->create();
        [$user, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();
        
        $cuti = \App\Models\Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Disetujui'
        ]);

        $notification = new \App\Notifications\CutiApproved($cuti);
        
        $mailMessage = $notification->toMail($user);
        
        $this->assertEquals('✅ Pengajuan Cuti Disetujui - SIMPEG Diskominfo Palembang', $mailMessage->subject);
        $this->assertStringContainsString($pegawai->name, $mailMessage->greeting);
    }

    /** @test */
    public function can_create_multiple_notifications()
    {
        $user = User::factory()->create();
        [$user, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();
        
        // Create multiple notifications
        for ($i = 0; $i < 5; $i++) {
            $cuti = \App\Models\Cuti::factory()->create([
                'pegawai_id' => $pegawai->id,
                'jenis_cuti_id' => $jenisCuti->id,
                'status_persetujuan' => 'Disetujui'
            ]);
            
            $notification = new \App\Notifications\CutiApproved($cuti);
            $user->notify($notification);
        }

        // Check all notifications were sent
        $this->assertNotificationSentTimes(5, \App\Notifications\CutiApproved::class, $user);
    }

    /** @test */
    public function unread_count_tracking_works()
    {
        $user = User::factory()->create();
        
        // Create 3 notifications
        for ($i = 0; $i < 3; $i++) {
            $notification = new \App\Notifications\CutiApproved(null);
            $user->notify($notification);
        }

        // All should be unread initially
        $response = $this->get(route('notifications.unread-count'));
        $response->assertJson(['count' => 3]);

        // Mark one as read
        $notification = $user->notifications()->first();
        $notification->markAsRead();

        // Should now have 2 unread
        $response = $this->get(route('notifications.unread-count'));
        $response->assertJson(['count' => 2]);
    }

    private function createTestCuti(): \App\Models\Cuti
    {
        [$user, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();
        $pimpinan = $this->createPimpinan();

        return Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Disetujui',
            'pimpinan_approver_id' => $pimpinan->id
        ]);
    }
}
