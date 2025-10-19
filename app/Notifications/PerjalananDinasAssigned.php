<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\PerjalananDinas;

// Hapus ShouldQueue jika Anda tidak menggunakan queue
class PerjalananDinasAssigned extends Notification
{
    use Queueable;

    public $perjalananDinas;

    /**
     * Create a new notification instance.
     */
    public function __construct(PerjalananDinas $perjalananDinas)
    {
        // Pastikan relasi sudah di-load
        $this->perjalananDinas = $perjalananDinas->loadMissing(['pegawai', 'pimpinanPemberiTugas']);
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
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $pegawaiNames = $this->perjalananDinas->pegawai
            ->pluck('nama_lengkap')
            ->filter()
            ->join(', ') ?: 'Tidak ada pegawai';
        
        return [
            'title' => 'Penugasan Perjalanan Dinas',
            'message' => "Anda ditugaskan untuk perjalanan dinas ke {$this->perjalananDinas->tempat_tujuan}",
            'perjalanan_dinas_id' => $this->perjalananDinas->id,
            'nomor_surat_tugas' => $this->perjalananDinas->nomor_surat_tugas,
            'tujuan' => $this->perjalananDinas->tempat_tujuan,
            'maksud_perjalanan' => $this->perjalananDinas->maksud_perjalanan,
            'pegawai_names' => $pegawaiNames,
            'tanggal_berangkat' => $this->perjalananDinas->tgl_berangkat,
            'tanggal_kembali' => $this->perjalananDinas->tgl_kembali,
            'assigned_by' => $this->perjalananDinas->pimpinanPemberiTugas->name ?? 'System',
            'action_url' => route('pegawai.perjalanan_dinas.my'),
            'icon' => 'plane',
            'color' => 'info'
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}