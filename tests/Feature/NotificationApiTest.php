<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\HasRolesAndPermissions;
use Tests\Traits\HasNotifications;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Cuti;
use App\Models\JenisCuti;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Carbon\Carbon;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase, HasRolesAndPermissions, HasNotifications;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpNotificationFaking();
        $this->setupJenisCuti();
    }

    // Index Page Tests
    /** @test */
    public function guest_cannot_access_notifications_index()
    {
        $response = $this->get(route('notifications.index'));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_access_notifications_index()
    {
        $this->actingAsPegawai();
        
        $response = $this->get(route('notifications.index'));
        $response->assertOk();
        $response->assertSee('Pusat Notifikasi');
    }

    /** @test */
    public function notifications_index_displays_paginated_notifications()
    {
        $this->actingAsPegawai();
        
        $user = auth()->user();
        
        // Create some test notifications
        for ($i = 0; $i < 25; $i++) {
            $user->notify(new \App\Notifications\CutiApproved($this->createTestCuti()));
        }

        $response = $this->get(route('notifications.index'));
        $response->assertSee('Pusat Notifikasi');
        
        // Should see pagination links
        $response->assertSee('Next');
    }

    // Recent Notifications API Tests
    /** @test */
    public function recent_notifications_api_returns_latest_5_notifications()
    {
        $this->actingAsPegawai();
        
        $user = auth()->user();
        
        // Create 10 notifications
        for ($i = 0; $i < 10; $i++) {
            $user->notify(new \App\Notifications\CutiApproved($this->createTestCuti()));
        }

        $response = $this->get(route('notifications.recent'));
        
        $response->assertOk();
        $response->assertJsonStructure(['html']);
        
        // Should contain HTML for notifications
        $response->assertSee('Pengajuan Cuti Disetujui');
        
        // Should return maximum 5 notifications in dropdown
        $html = $response->json('html');
        $notificationCount = substr_count($html, 'notification-item_');
        $this->assertLessThanOrEqual(5, $notificationCount);
    }

    /** @test */
    public function recent_notifications_handles_empty_list()
    {
        $this->actingAsPegawai();
        
        $response = $this->get(route('notifications.recent'));
        
        $response->assertOk();
        $response->assertJsonStructure(['html']);
        
        $html = $response->json('html');
        $this->assertStringContainsString('Belum ada notifikasi', $html);
    }

    /** @test */
    public function recent_notifications_shows_unread_indicator()
    {
        $this->actingAsPegawai();
        
        $user = auth()->user();
        
        $cuti = $this->createTestCuti();
        $notification = new \App\Notifications\CutiApproved($cuti);
        $notification->id = 'test-notification-id';
        
        // Send notification and leave it unread
        $user->notify($notification);

        $response = $this->get(route('notifications.recent'));
        
        $html = $response->json('html');
        $this->assertStringContainsString('fw-bold', $html); // Unread indicator
        $this->assertStringContainsString('bg-warning', $html); // Unread badge color
    }

    // Unread Count Tests
    /** @test */
    public function unread_count_api_returns_correct_number()
    {
        $this->actingAsPegawai();
        
        $user = auth()->user();
        
        // Initially should have 0 unread
        $response = $this->get(route('notifications.unread-count'));
        $response->assertJson(['count' => 0]);
        
        // Create 3 notifications
        for ($i = 0; $i < 3; $i++) {
            $user->notify(new \App\Notifications\CutiApproved($this->createTestCuti()));
        }

        $response = $this->get(route('notifications.unread-count'));
        $response->assertJson(['count' => 3]);
    }

    /** @test */
    public function unread_count_excludes_read_notifications()
    {
        $this->actingAsPegawai();
        
        $user = auth()->user();
        
        // Create 2 notifications, mark 1 as read
        $notification1 = new \App\Notifications\CutiApproved($this->createTestCuti());
        $notification2 = new \App\Notifications\CutiApproved($this->createTestCuti());
        
        $user->notify($notification1);
        $user->notify($notification2);
        
        // Mark first notification as read
        $dbNotification1 = $user->notifications()->first();
        $dbNotification1->markAsRead();
        
        $response = $this->get(route('notifications.unread-count'));
        $response->assertJson(['count' => 1]);
    }

    // Mark as Read Tests
    /** @test */
    public function mark_notification_as_read_api_works()
    {
        $this->actingAsPegawai();
        
        $user = auth()->user();
        
        $cuti = $this->createTestCuti();
        $notification = new \App\Notifications\CutiApproved($cuti);
        
        $user->notify($notification);
        
        $dbNotification = $user->notifications()->first();
        $this->assertNull($dbNotification->read_at);
        
        $response = $this->post(route('notifications.read', $dbNotification->id));
        $response->assertJson(['success' => true]);
        
        $dbNotification->refresh();
        $this->assertNotNull($dbNotification->read_at);
    }

    /** @test */
    public function user_cannot_mark_other_users_notification_as_read()
    {
        $this->actingAsPegawai();
        
        // Create another user
        $otherUser = User::factory()->create();
        
        $cuti = $this->createTestCuti();
        $notification = new \App\Notifications\CutiApproved($cuti);
        
        $otherUser->notify($notification);
        
        $dbNotification = $otherUser->notifications()->first();
        
        // Try to mark other user's notification as read
        $response = $this->post(route('notifications.read', $dbNotification->id));
        $response->assertForbidden();
    }

    /** @test */
    public function mark_all_as_read_api_works()
    {
        $this->actingAsPegawai();
        
        $user = auth()->user();
        
        // Create 5 notifications
        for ($i = 0; $i < 5; $i++) {
            $user->notify(new \App\Notifications\CutiApproved($this->createTestCuti()));
        }
        
        // All should be unread
        $response = $this->get(route('notifications.unread-count'));
        $response->assertJson(['count' => 5]);
        
        // Mark all as read
        $response = $this->post(route('notifications.read-all'));
        $response->assertJson(['success' => true]);
        
        // All should be read now
        $response = $this->get(route('notifications.unread-count'));
        $response->assertJson(['count' => 0]);
    }

    // Delete Notification Tests
    /** @test */
    public function delete_notification_api_works()
    {
        $this->actingAsPegawai();
        
        $user = auth()->user();
        
        $cuti = $this->createTestCuti();
        $notification = new \App\Notifications\CutiApproved($cuti);
        
        $user->notify($notification);
        
        $dbNotification = $user->notifications()->first();
        $this->assertNotNull($dbNotification);
        
        $response = $this->delete(route('notifications.destroy', $dbNotification->id));
        $response->assertJson(['success' => true]);
        
        $this->assertDatabaseMissing('notifications', ['id' => $dbNotification->id]);
    }

    /** @test */
    public function user_cannot_delete_other_users_notification()
    {
        $this->actingAsPegawai();
        
        // Create another user
        $otherUser = User::factory()->create();
        
        $cuti = $this->createTestCuti();
        $notification = new \App\Notifications\CutiApproved($cuti);
        
        $otherUser->notify($notification);
        
        $dbNotification = $otherUser->notifications()->first();
        
        // Try to delete other user's notification
        $response = $this->delete(route('notifications.destroy', $dbNotification->id));
        $response->assertForbidden();
        
        // Notification should still exist
        $this->assertDatabaseHas('notifications', ['id' => $dbNotification->id]);
    }

    // Notification Data Structure Tests
    /** @test */
    public function recent_notifications_html_contains_required_elements()
    {
        $this->actingAsPegawai();
        
        $user = auth()->user();
        
        $cuti = $this->createTestCuti();
        $notification = new \App\Notifications\CutiApproved($cuti);
        
        $user->notify($notification);

        $response = $this->get(route('notifications.recent'));
        
        $html = $response->json('html');
        
        // Should contain notification elements
        $this->assertStringContainsString('dropdown-item', $html);
        $this->assertStringContainsString('avatar', $html);
        $this->assertStringContainsString('far fa-clock', $html);
        $this->assertStringContainsString('Pengajuan Cuti Disetujui', $html);
    }

    /** @test */
    public function notification_click_navigation_works()
    {
        $this->actingAsPegawai();
        
        $user = auth()->user();
        
        $cuti = $this->createTestCuti();
        $notification = new \App\Notifications\CutiApproved($cuti);
        
        $user->notify($notification);

        $dbNotification = $user->notifications()->first();
        
        // Test the click handler (this tests the structure more than actual click)
        $response = $this->get(route('notifications.recent'));
        
        $html = $response->json('html');
        
        // Should contain the onclick handler
        $this->assertStringContainsString('handleDropdownNotificationClick', $html);
        $this->assertStringContainsString($dbNotification->id, $html);
    }

    // Performance Tests
    /** @test */
    public function recent_notifications_api_is_fast()
    {
        $this->actingAsPegawai();
        
        $user = auth()->user();
        
        // Create many notifications (this shouldn't affect performance since we only fetch 5)
        for ($i = 0; $i < 100; $i++) {
            $user->notify(new \App\Notifications\CutiApproved($this->createTestCuti()));
        }

        $startTime = microtime(true);
        
        $response = $this->get(route('notifications.recent'));
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        
        $response->assertOk();
        
        // Should complete in less than 200ms
        $this->assertLessThan(200, $executionTime);
    }

    /** @test */
    public function unread_count_api_is_fast()
    {
        $this->actingAsPegawai();
        
        $user = auth()->user();
        
        // Create many notifications
        for ($i = 0; $i < 100; $i++) {
            $user->notify(new \App\Notifications\CutiApproved($this->createTestCuti()));
        }

        $startTime = microtime(true);
        
        $response = $this->get(route('notifications.unread-count'));
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;
        
        $response->assertJson(['count' => 100]);
        
        // Should complete in less than 100ms
        $this->assertLessThan(100, $executionTime);
    }

    // Integration Tests
    /** @test */
    public function notification_flow_from_cuti_approval_to_display()
    {
        // Setup
        $pimpinan = $this->actingAsPimpinan();
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();
        
        $cuti = Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Diajukan'
        ]);

        // Trigger approval
        $response = $this->post(route('pimpinan.cuti.approve', $cuti));
        $response->assertJson(['message' => 'Cuti approved successfully']);

        // Switch to pegawai and check notifications
        $this->actingAs($pegawaiUser);
        
        // Check unread count
        $response = $this->get(route('notifications.unread-count'));
        $response->assertJson(['count' => 1]);

        // Check recent notifications API
        $response = $this->get(route('notifications.recent'));
        $response->assertOk();
        
        $html = $response->json('html');
        $this->assertStringContainsString('Pengajuan Cuti Disetujui', $html);
        $this->assertStringContainsString('Cuti Tahunan', $html);

        // Check full notifications index
        $response = $this->get(route('notifications.index'));
        $response->assertOk();
        $response->assertSee('Pusat Notifikasi');
    }

    /** @test */
    public function notification_flow_from_laporan_verification_to_display()
    {
        // Setup
        $adminKeuangan = $this->actingAsAdminKeuangan();
        $pimpinan = $this->createPimpinan();
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        
        $perjalananDinas = \App\Models\PerjalananDinas::factory()->create([
            'tempat_tujuan' => 'Jakarta'
        ]);
        $perjalananDinas->pegawai()->attach($pegawai->id);

        $laporanPD = \App\Models\LaporanPD::factory()->create([
            'perjalanan_dinas_id' => $perjalananDinas->id,
            'status_verifikasi' => 'Belum Diverifikasi'
        ]);

        // Trigger verification
        $response = $this->post(route('keuangan.laporan_pd.verify', $laporanPD), [
            'catatan_verifikasi' => 'Laporan lengkap'
        ]);
        $response->assertJson(['message' => 'Laporan PD verified successfully']);

        // Switch to pimpinan and check notifications
        $this->actingAs($pimpinan);
        
        // Check unread count
        $response = $this->get(route('notifications.unread-count'));
        $response->assertJson(['count' => 1]);

        // Check recent notifications API
        $response = $this->get(route('notifications.recent'));
        $response->assertOk();
        
        $html = $response->json('html');
        $this->assertStringContainsString('Laporan Perjalanan Dinas Disetujui', $html);
        $this->assertStringContainsString('Jakarta', $html);
    }

    // Helper Methods
    protected function createTestCuti(): \App\Models\Cuti
    {
        [$pegawaiUser, $pegawai] = $this->setupCompletePegawai();
        $jenisCuti = JenisCuti::where('nama', 'Cuti Tahunan')->first();
        
        return Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'status_persetujuan' => 'Disetujui'
        ]);
    }
}
