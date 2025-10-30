<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\konten>
 */
class kontenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'judul' => fake()->unique()->sentence(rand(4, 8)),
            'isi' => fake()->paragraphs(rand(2, 4), true),
            'detil' => fake()->paragraphs(rand(6, 8), true)
        ];
    }
}
