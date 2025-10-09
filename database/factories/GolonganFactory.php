<?php

namespace Database\Factories;

use App\Models\Golongan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Golongan>
 */
class GolonganFactory extends Factory
{
    protected $model = Golongan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->randomElement([
                'III/a', 'III/b', 'III/c', 'III/d',
                'IV/a', 'IV/b', 'IV/c', 'IV/d',
                'II/a', 'II/b', 'II/c', 'II/d',
                'I/a', 'I/b', 'I/c', 'I/d'
            ]),
            'deskripsi' => $this->faker->sentence(8),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate junior level golongan.
     */
    public function junior(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => $this->faker->randomElement(['I/a', 'I/b', 'I/c', 'I/d', 'II/a', 'II/b']),
        ]);
    }

    /**
     * Indicate middle level golongan.
     */
    public function middle(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => $this->faker->randomElement(['II/c', 'II/d', 'III/a', 'III/b']),
        ]);
    }

    /**
     * Indicate senior level golongan.
     */
    public function senior(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => $this->faker->randomElement(['III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d']),
        ]);
    }
}
