<?php

namespace Database\Factories;
use App\Models\Guru;
use App\Models\walas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\walas>
 */
class walasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jenjang' => $this->faker->randomElement(['X', 'XI', 'XII']),
            'namakelas' => $this->faker->randomElement(['A', 'B', 'C']),
            'tahunajaran' => '2025/2026',
            'idguru' => Guru::factory()
        ];
    }
}
