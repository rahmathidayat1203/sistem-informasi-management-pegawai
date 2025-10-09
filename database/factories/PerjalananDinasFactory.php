<?php

namespace Database\Factories;

use App\Models\PerjalananDinas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PerjalananDinas>
 */
class PerjalananDinasFactory extends Factory
{
    protected $model = PerjalananDinas::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('next week', '+3 months');
        $endDate = (clone $startDate)->modify('+' . rand(1, 7) . ' days');

        return [
            'nomor_surat_tugas' => 'ST/' . fake()->numberBetween(100, 999) . '/' . date('Y'),
            'maksud_perjalanan' => fake()->sentence(6),
            'tempat_tujuan' => fake()->city() . ', ' . fake()->country(),
            'tgl_berangkat' => $startDate,
            'tgl_kembali' => $endDate,
            'pimpinan_pemberi_tugas_id' => User::role('Pimpinan')->inRandomOrder()->first(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the perjalanan dinas is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'tgl_berangkat' => fake()->dateTimeBetween('now', '+1 month'),
            'tgl_kembali' => fake()->dateTimeBetween('+1 month', '+2 months'),
        ]);
    }

    /**
     * Indicate that the perjalanan dinas is currently ongoing.
     */
    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'tgl_berangkat' => fake()->dateTimeBetween('-5 days', 'now'),
            'tgl_kembali' => fake()->dateTimeBetween('now', '+5 days'),
        ]);
    }

    /**
     * Indicate that the perjalanan dinas is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'tgl_berangkat' => fake()->dateTimeBetween('-2 months', '-1 month'),
            'tgl_kembali' => fake()->dateTimeBetween('-1 month', '-2 weeks'),
        ]);
    }
}
