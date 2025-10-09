<?php

namespace Database\Factories;

use App\Models\SisaCuti;
use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SisaCuti>
 */
class SisaCutiFactory extends Factory
{
    protected $model = SisaCuti::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pegawai_id' => Pegawai::factory(),
            'tahun' => $this->faker->numberBetween(2020, now()->year),
            'jatah_cuti' => 12,
            'sisa_cuti' => $this->faker->numberBetween(0, 12),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that sisa cuti is for current year.
     */
    public function forCurrentYear(): static
    {
        return $this->state(fn (array $attributes) => [
            'tahun' => now()->year,
        ]);
    }

    /**
     * Indicate that sisa cuti is full (12 days).
     */
    public function full(): static
    {
        return $this->state(fn (array $attributes) => [
            'jatah_cuti' => 12,
            'sisa_cuti' => 12,
        ]);
    }

    /**
     * Indicate that sisa cuti is partially used.
     */
    public function partiallyUsed(): static
    {
        $used = rand(1, 8);
        return $this->state(fn (array $attributes) => [
            'jatah_cuti' => 12,
            'sisa_cuti' => 12 - $used,
        ]);
    }
}
