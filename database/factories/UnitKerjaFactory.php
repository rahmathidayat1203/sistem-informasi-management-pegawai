<?php

namespace Database\Factories;

use App\Models\UnitKerja;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UnitKerja>
 */
class UnitKerjaFactory extends Factory
{
    protected $model = UnitKerja::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitNames = [
            'Bidang E-Government',
            'Bidang Infrastruktur IT', 
            'Bidang Aplikasi Inovasi',
            'Seksi Data Center',
            'Bidang Kepegawaian',
            'Bidang Keuangan',
            'Seksi Umum',
            'Seksi Tata Usaha',
            'Bidang Persandian',
            'Seksi Pengembangan Sistem'
        ];

        return [
            'nama' => $this->faker->randomElement($unitNames),
            'deskripsi' => $this->faker->sentence(6),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate IT related unit.
     */
    public function it(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => $this->faker->randomElement([
                'Bidang E-Government',
                'Bidang Infrastruktur IT',
                'Bidang Aplikasi Inovasi',
                'Seksi Data Center'
            ]),
        ]);
    }

    /**
     * Indicate administrative unit.
     */
    public function administrative(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => $this->faker->randomElement([
                'Bidang Kepegawaian',
                'Bidang Keuangan',
                'Seksi Umum',
                'Seksi Tata Usaha'
            ]),
        ]);
    }
}
