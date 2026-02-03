<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lapangan>
 */
class LapanganFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $daerahList = ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Medan', 'Semarang', 'Makassar'];
        $daerah = fake()->randomElement($daerahList);
        
        return [
            'nama' => 'Lapangan ' . fake()->company() . ' ' . fake()->randomElement(['Futsal', 'Sport', 'Arena', 'Center']),
            'lokasi' => fake()->streetAddress() . ', ' . $daerah,
            'daerah' => $daerah,
            'kapasitas' => fake()->numberBetween(10, 30),
            'harga_per_jam' => fake()->numberBetween(50000, 150000),
            'fasilitas' => fake()->randomElements(['lampu malam', 'toilet', 'parkir', 'kantin', 'ruang ganti', 'wifi'], 3),
            'foto' => null,
            'status' => 'aktif',
        ];
    }

    /**
     * Create an inactive lapangan
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'tidak_aktif',
        ]);
    }
}
