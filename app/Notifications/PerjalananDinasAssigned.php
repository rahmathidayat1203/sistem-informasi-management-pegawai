<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PerjalananDinasAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public $perjalananDinas;

    /**
     * Create a new notification instance.
     */
    public function __construct($perjalananDinas)
    {
        $this->perjalananDinas = $perjalananDinas;
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
        $pegawaiNames = $this->perjalananDinas->pegawai->pluck('nama_lengkap')->join(', ');
        
        return [
            'title' => 'Penugasan Perjalanan Dinas',
            'message' => "Anda ditugaskan untuk perjalanan dinas ke {$this->perjalananDinas->tujuan}",
            'perjalanan_dinas_id' => $this->perjalananDinas->id,
            'tujuan' => $this->perjalananDinas->tujuan,
            'pegawai_names' => $pegawaiNames,
            'tanggal_berangkat' => $this->perjalananDinas->tgl_berangkat,
            'tanggal_kembali' => $this->perjalananDinas->tgl_kembali,
            'assigned_by' => $this->perjalananDinas->pimpinanPemberiTugas->name,
            'action_url' => route('pegawai.perjalanan_dinas.my'),
            'icon' => 'plane',
            'color' => 'info'
        ];
    }
}
