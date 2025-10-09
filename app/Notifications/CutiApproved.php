<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class CutiApproved extends Notification implements ShouldQueue
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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('✅ Pengajuan Cuti Disetujui - SIMPEG Diskominfo Palembang')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Kami ingin memberitahu bahwa pengajuan cuti Anda telah **disetujui**. Berikut detailnya:')
            ->line('**Jenis Cuti:** ' . $this->cuti->jenisCuti->nama)
            ->line('**Tanggal Mulai:** ' . \Carbon\Carbon::parse($this->cuti->tgl_mulai)->format('d M Y'))
            ->line('**Tanggal Selesai:** ' . \Carbon\Carbon::parse($this->cuti->tgl_selesai)->format('d M Y'))
            ->line('**Disetujui oleh:** ' . ($this->cuti->pimpinanApprover->name ?? 'Pimpinan'))
            ->line('**Keterangan:** ' . $this->cuti->keterangan)
            ->action('Lihat Detail Cuti', route('pegawai.cuti.my'))
            ->line('Terima kasih telah menggunakan sistem SIMPEG.')
            ->salutation('Salam,\nTIM SIMPEG Diskominfo Palembang');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pengajuan Cuti Disetujui',
            'message' => "Pengajuan cuti {$this->cuti->jenisCuti->nama} Anda telah disetujui oleh pimpinan",
            'cuti_id' => $this->cuti->id,
            'jenis_cuti' => $this->cuti->jenisCuti->nama,
            'approved_by' => $this->cuti->pimpinanApprover->name,
            'action_url' => route('pegawai.cuti.my'),
            'icon' => 'check-circle',
            'color' => 'success'
        ];
    }
}
