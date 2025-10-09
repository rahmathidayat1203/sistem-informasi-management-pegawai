<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class CutiRejected extends Notification implements ShouldQueue
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
            ->subject('❌ Pengajuan Cuti Ditolak - SIMPEG Diskominfo Palembang')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Kami ingin memberitahu bahwa pengajuan cuti Anda telah **ditolak**. Berikut detailnya:')
            ->line('**Jenis Cuti:** ' . $this->cuti->jenisCuti->nama)
            ->line('**Tanggal Diajukan:** ' . \Carbon\Carbon::parse($this->cuti->tgl_pengajuan)->format('d M Y'))
            ->line('**Ditolak oleh:** ' . ($this->cuti->pimpinanApprover->name ?? 'Pimpinan'))
            ->line('**Alasan Penolakan:** ' . $this->extractAlasanPenolakan())
            ->line('**Keterangan:** ' . $this->cuti->keterangan)
            ->action('Lihat Detail Cuti', route('pegawai.cuti.my'))
            ->line('Jika ada pertanyaan, silakan hubungi admin kepegawaian atau pimpinan terkait.')
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
            'title' => 'Pengajuan Cuti Ditolak',
            'message' => "Pengajuan cuti {$this->cuti->jenisCuti->nama} Anda ditolak oleh pimpinan",
            'cuti_id' => $this->cuti->id,
            'jenis_cuti' => $this->cuti->jenisCuti->nama,
            'rejected_by' => $this->cuti->pimpinanApprover->name,
            'alasan' => $this->extractAlasanPenolakan(),
            'action_url' => route('pegawai.cuti.my'),
            'icon' => 'times-circle',
            'color' => 'danger'
        ];
    }

    private function extractAlasanPenolakan()
    {
        if (strpos($this->cuti->keterangan, 'Alasan Penolakan:') !== false) {
            $parts = explode('Alasan Penolakan:', $this->cuti->keterangan);
            return trim($parts[1] ?? '');
        }
        return 'Tidak ada alasan spesifik';
    }
}
