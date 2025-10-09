<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;
use App\Notifications\CutiApproved;
use App\Notifications\CutiRejected;
use App\Notifications\LaporanPDVerified;
use App\Notifications\PerjalananDinasAssigned;

trait HasNotifications
{
    /**
     * Setup notification fake.
     */
    protected function setUpNotificationFaking(): void
    {
        Notification::fake();
    }

    /**
     * Assert specific notification was sent.
     */
    protected function assertNotificationSent($notifiable, $notificationClass): self
    {
        Notification::assertSentTo($notifiable, $notificationClass);
        return $this;
    }

    /**
     * Assert notification was not sent.
     */
    protected function assertNotificationNotSent($notifiable, $notificationClass): self
    {
        Notification::assertNotSentTo($notifiable, $notificationClass);
        return $this;
    }

    /**
     * Assert notification sent count.
     */
    protected function assertNotificationSentCount($count): self
    {
        Notification::assertSentTimes($count);
        return $this;
    }

    /**
     * Get notification data.
     */
    protected function getNotificationData(\Illuminate\Notifications\Notification $notification): array
    {
        if (method_exists($notification, 'toArray')) {
            $notifiable = new AnonymousNotifiable();
            return $notification->toArray($notifiable);
        }
        
        return [];
    }

    /**
     * Assert email notification content.
     */
    protected function assertEmailNotificationContains(
        \Illuminate\Notifications\Notification $notification, 
        string $content
    ): self {
        $mailMessage = $notification->toMail(new AnonymousNotifiable());
        $mailContent = $mailMessage->intro . $mailMessage->outro . $mailMessage->actionText;
        
        $this->assertStringContainsString($content, $mailContent);
        return $this;
    }

    /**
     * Assert CutiApproved notification.
     */
    protected function assertCutiApprovedNotification(\App\Models\Cuti $cuti): self
    {
        return $this->assertNotificationSent($cuti->pegawai->user, CutiApproved::class);
    }

    /**
     * Assert CutiRejected notification.
     */
    protected function assertCutiRejectedNotification(\App\Models\Cuti $cuti): self
    {
        return $this->assertNotificationSent($cuti->pegawai->user, CutiRejected::class);
    }

    /**
     * Assert LaporanPDVerified notification to pimpinan.
     */
    protected function assertLaporanPDVerifiedNotification(\App\Models\LaporanPD $laporanPD, string $status = 'verified'): self
    {
        $pimpinans = \App\Models\User::role('Pimpinan')->get();
        
        foreach ($pimpinans as $pimpinan) {
            $this->assertNotificationSent($pimpinan, LaporanPDVerified::class);
        }
        
        return $this;
    }

    /**
     * Assert PerjalananDinasAssigned notification.
     */
    protected function assertPerjalananDinasAssignedNotification(\App\Models\PerjalananDinas $perjalananDinas): self
    {
        foreach ($perjalananDinas->pegawai as $pegawai) {
            if ($pegawai->user) {
                $this->assertNotificationSent($pegawai->user, PerjalananDinasAssigned::class);
            }
        }
        
        return $this;
    }
}
