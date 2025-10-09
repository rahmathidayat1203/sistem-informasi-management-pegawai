<?php

namespace Database\Factories;

use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Golongan;
use App\Models\UnitKerja;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pegawai>
 */
class PegawaiFactory extends Factory
{
    protected $model = Pegawai::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'NIP' => $this->faker->unique()->numerify('################'),
            'nama_lengkap' => $this->faker->name,
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'tempat_lahir' => $this->faker->city,
            'tgl_lahir' => $this->faker->dateTimeBetween('-50 years', '-20 years'),
            'agama' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha']),
            'alamat' => $this->faker->address,
            'no_telp' => $this->faker->phoneNumber,
            'jabatan_id' => Jabatan::factory(),
            'golongan_id' => Golongan::factory(),
            'unit_kerja_id' => UnitKerja::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Create pegawai with associated user.
     */
    public function withUser(): static
    {
        return $this->afterCreating(function (Pegawai $pegawai) {
            $pegawai->user()->create([
                'username' => strtolower(str_replace(' ', '.', $pegawai->nama_lengkap)),
                'email' => $pegawai->email,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'name' => $pegawai->nama_lengkap,
                'pegawai_id' => $pegawai->id,
            ]);
        });
    }
}
