<?php

namespace Database\Factories;

use App\Models\JenisCuti;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JenisCuti>
 */
class JenisCutiFactory extends Factory
{
    protected $model = JenisCuti::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cutiTypes = [
            'Cuti Tahunan',
            'Cuti Sakit', 
            'Cuti Besar',
            'Cuti Hamil',
            'Cuti Melahirkan',
            'Cuti Karena Alasan Penting'
        ];

        return [
            'nama' => $this->faker->randomElement($cutiTypes),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate cuti tahunan.
     */
    public function tahunan(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'Cuti Tahunan',
        ]);
    }

    /**
     * Indicate cuti sakit.
     */
    public function sakit(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'Cuti Sakit',
        ]);
    }

    /**
     * Indicate cuti besar.
     */
    public function besar(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'Cuti Besar',
        ]);
    }

    /**
     * Indicate cuti hamil.
     */
    public function hamil(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'Cuti Hamil',
        ]);
    }

    /**
     * Indicate cuti melahirkan.
     */
    public function melahirkan(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'Cuti Melahirkan',
        ]);
    }
}
