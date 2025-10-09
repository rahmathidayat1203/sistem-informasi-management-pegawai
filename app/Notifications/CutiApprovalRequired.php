<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class CutiApprovalRequired extends Notification implements ShouldQueue
{
    use Queueable;

    public $cuti;

    /**
     * Create a new notification instance.
     */
    public function __construct($cuti)
    {
        $this->cuti = $cuti;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pengajuan Cuti Menunggu Persetujuan',
            'message' => "{$this->cuti->pegawai->nama_lengkap} mengajukan cuti {$this->cuti->jenisCuti->nama} dari " . 
                        tanggal_indo($this->cuti->tgl_mulai) . " hingga " . tanggal_indo($this->cuti->tgl_selesai),
            'cuti_id' => $this->cuti->id,
            'pegawai_nama' => $this->cuti->pegawai->nama_lengkap,
            'jenis_cuti' => $this->cuti->jenisCuti->nama,
            'tanggal_mulai' => $this->cuti->tgl_mulai,
            'tanggal_selesai' => $this->cuti->tgl_selesai,
            'action_url' => route('pimpinan.cuti.approval'),
            'icon' => 'calendar-alt',
            'color' => 'warning'
        ];
    }
}
