<?php

namespace Database\Factories;

use App\Models\Cuti;
use App\Models\Pegawai;
use App\Models\JenisCuti;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cuti>
 */
class CutiFactory extends Factory
{
    protected $model = Cuti::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('+1 week', '+3 months');
        $endDate = (clone $startDate)->modify('+' . rand(1, 10) . ' days');
        $jumlahHari = $startDate->diff($endDate)->days + 1;

        return [
            'pegawai_id' => Pegawai::factory(),
            'jenis_cuti_id' => JenisCuti::factory(),
            'tgl_pengajuan' => fake()->dateTimeBetween('-1 month', '-1 day'),
            'tgl_mulai' => $startDate,
            'tgl_selesai' => $endDate,
            'keterangan' => fake()->sentence(10),
            'status_persetujuan' => fake()->randomElement(['Diajukan', 'Disetujui', 'Ditolak']),
            'pimpinan_approver_id' => User::factory()->create(), // akan dikaitkan nanti di trait
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the cuti is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_persetujuan' => 'Diajukan',
        ]);
    }

    /**
     * Indicate that the cuti is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_persetujuan' => 'Disetujui',
        ]);
    }

    /**
     * Indicate that the cuti is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_persetujuan' => 'Ditolak',
        ]);
    }

    /**
     * Indicate that the cuti is for this year with proper allocation.
     */
    public function forThisYear(): static
    {
        return $this->state(fn (array $attributes) => [
            'tgl_pengajuan' => fake()->dateTimeBetween('January 1st this year', 'now'),
            'tgl_mulai' => fake()->dateTimeBetween('now', '+3 months'),
            'tgl_selesai' => fake()->dateTimeBetween('+4 months', '+6 months'),
        ]);
    }
}
