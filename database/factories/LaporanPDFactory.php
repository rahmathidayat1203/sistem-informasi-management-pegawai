<?php

namespace Database\Factories;

use App\Models\LaporanPD;
use App\Models\PerjalananDinas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LaporanPD>
 */
class LaporanPDFactory extends Factory
{
    protected $model = LaporanPD::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'perjalanan_dinas_id' => PerjalananDinas::factory(),
            'file_laporan' => 'laporan_pd/' . fake()->uuid() . '.pdf',
            'tgl_unggah' => fake()->dateTimeBetween('-1 week', 'now'),
            'status_verifikasi' => fake()->randomElement(['Belum Diverifikasi', 'Disetujui', 'Perbaikan']),
            'catatan_verifikasi' => fake()->randomElement([null, fake()->sentence(5)]),
            'alasan_penolakan' => fake()->randomElement([null, fake()->sentence(5)]),
            'admin_keuangan_verifier_id' => User::role('Admin Keuangan')->inRandomOrder()->first(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the laporan PD is unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_verifikasi' => 'Belum Diverifikasi',
            'admin_keuangan_verifier_id' => null,
            'catatan_verifikasi' => null,
            'alasan_penolakan' => null,
        ]);
    }

    /**
     * Indicate that the laporan PD is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_verifikasi' => 'Disetujui',
            'alasan_penolakan' => null,
        ]);
    }

    /**
     * Indicate that the laporan PD is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_verifikasi' => 'Perbaikan',
            'catatan_verifikasi' => null,
        ]);
    }

    /**
     * Indicate that the laporan PD was uploaded today.
     */
    public function uploadedToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'tgl_unggah' => now(),
        ]);
    }

    /**
     * Indicate that the laporan PD has a file.
     */
    public function withFile(string $fileName = null): static
    {
        return $this->state(fn (array $attributes) => [
            'file_laporan' => $fileName ?: 'laporan_pd/' . fake()->uuid() . '.pdf',
        ]);
    }
}
