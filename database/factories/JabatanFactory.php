<?php

namespace Database\Factories;

use App\Models\Jabatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jabatan>
 */
class JabatanFactory extends Factory
{
    protected $model = Jabatan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->jobTitle,
            'deskripsi' => $this->faker->sentence(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate IT related jabatan.
     */
    public function it(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => $this->faker->randomElement([
                'Programmer',
                'System Analyst', 
                'Database Administrator',
                'Network Engineer',
                'IT Support',
                'Software Engineer',
                'Web Developer',
                'DevOps Engineer'
            ]),
        ]);
    }

    /**
     * Indicate administrative jabatan.
     */
    public function administrative(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => $this->faker->randomElement([
                'Staff Administrasi',
                'Sekretaris',
                'Bendahara',
                'Analis Keuangan',
                'Manajer SDM'
            ]),
        ]);
    }

    /**
     * Indicate leadership jabatan.
     */
    public function leadership(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => $this->faker->randomElement([
                'Kepala Seksi',
                'Kepala Bidang',
                'Wakil Kepala Dinas',
                'Kepala Dinas'
            ]),
        ]);
    }
}
