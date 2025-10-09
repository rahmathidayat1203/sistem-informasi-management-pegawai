<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class LaporanPDVerified extends Notification implements ShouldQueue
{
    use Queueable;

    public $laporanPD;
    public $perjalananDinas;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($laporanPD, $status = 'verified')
    {
        $this->laporanPD = $laporanPD;
        $this->perjalananDinas = $laporanPD->perjalananDinas;
        $this->status = $status;
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
        $statusText = $this->status === 'verified' ? 'Disetujui' : 'Ditolak';
        $statusEmoji = $this->status === 'verified' ? '✅' : '❌';
        $pegawai = $this->perjalananDinas->pegawai->first();
        
        $mailMessage = (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("{$statusEmoji} Laporan Perjalanan Dinas {$statusText} - SIMPEG Diskominfo Palembang")
            ->greeting('Halo Yth. ' . $notifiable->name . ',')
            ->line("Kami ingin memberitahu bahwa laporan perjalanan dinas telah **{$statusText}** oleh Admin Keuangan. Berikut detailnya:")
            ->line('**Nama Pegawai:** ' . ($pegawai ? $pegawai->nama_lengkap : 'N/A'))
            ->line('**Tempat Tujuan:** ' . $this->perjalananDinas->tempat_tujuan)
            ->line('**Tanggal Berangkat:** ' . \Carbon\Carbon::parse($this->perjalananDinas->tgl_berangkat)->format('d M Y'))
            ->line('**Tanggal Kembali:** ' . \Carbon\Carbon::parse($this->perjalananDinas->tgl_kembali)->format('d M Y'))
            ->line('**Maksud Perjalanan:** ' . $this->perjalananDinas->maksud_perjalanan)
            ->line('**Status Verifikasi:** ' . $statusText);
        
        if ($this->status === 'verified' && $this->laporanPD->catatan_verifikasi) {
            $mailMessage->line('**Catatan Verifikasi:** ' . $this->laporanPD->catatan_verifikasi);
        } elseif ($this->status === 'rejected' && $this->laporanPD->alasan_penolakan) {
            $mailMessage->line('**Alasan Penolakan:** ' . $this->laporanPD->alasan_penolakan);
        }
        
        return $mailMessage
            ->action('Lihat Detail Laporan', route('admin.laporan_pd.show', $this->laporanPD->id))
            ->line('Laporan ini tersedia untuk pengarsipan dan pelaporan lebih lanjut.')
            ->line('Terima kasih telah menggunakan sistem SIMPEG.')
            ->salutation('Salam,\\nTIM SIMPEG Diskominfo Palembang');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusText = $this->status === 'verified' ? 'Disetujui' : 'Ditolak';
        $pegawai = $this->perjalananDinas->pegawai->first();
        
        return [
            'title' => "Laporan Perjalanan Dinas {$statusText}",
            'message' => "Laporan perjalanan dinas ke {$this->perjalananDinas->tempat_tujuan} telah {$statusText} oleh Admin Keuangan",
            'laporan_pd_id' => $this->laporanPD->id,
            'perjalanan_dinas_id' => $this->perjalananDinas->id,
            'pegawai_nama' => $pegawai ? $pegawai->nama_lengkap : 'N/A',
            'tempat_tujuan' => $this->perjalananDinas->tempat_tujuan,
            'status' => $this->status,
            'action_url' => route('admin.laporan_pd.show', $this->laporanPD->id),
            'icon' => $this->status === 'verified' ? 'check-circle' : 'times-circle',
            'color' => $this->status === 'verified' ? 'success' : 'danger'
        ];
    }
}
