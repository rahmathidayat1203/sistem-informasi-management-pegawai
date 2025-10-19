<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Cuti;

class CutiApprovalRequired extends Notification implements ShouldQueue
{
    use Queueable;

    public $cutiId;

    /**
     * Create a new notification instance.
     */
    public function __construct($cuti)
    {
        // Store only the ID to avoid serialization issues
        $this->cutiId = $cuti->id;
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
        $cuti = Cuti::with(['pegawai', 'jenisCuti'])->find($this->cutiId);
        
        if (!$cuti) {
            return [
                'title' => 'Pengajuan Cuti Menunggu Persetujuan',
                'message' => "Pengajuan cuti tidak ditemukan",
                'cuti_id' => $this->cutiId,
                'pegawai_nama' => 'Data tidak ditemukan',
                'jenis_cuti' => 'Data tidak ditemukan',
                'tanggal_mulai' => null,
                'tanggal_selesai' => null,
                'action_url' => route('pimpinan.cuti.approval'),
                'icon' => 'calendar-alt',
                'color' => 'warning'
            ];
        }

        return [
            'title' => 'Pengajuan Cuti Menunggu Persetujuan',
            'message' => "{$cuti->pegawai->nama_lengkap} mengajukan cuti {$cuti->jenisCuti->nama} dari " . 
                        tanggal_indo($cuti->tgl_mulai) . " hingga " . tanggal_indo($cuti->tgl_selesai),
            'cuti_id' => $cuti->id,
            'pegawai_nama' => $cuti->pegawai->nama_lengkap,
            'jenis_cuti' => $cuti->jenisCuti->nama,
            'tanggal_mulai' => $cuti->tgl_mulai,
            'tanggal_selesai' => $cuti->tgl_selesai,
            'action_url' => route('pimpinan.cuti.approval'),
            'icon' => 'calendar-alt',
            'color' => 'warning'
        ];
    }
}
