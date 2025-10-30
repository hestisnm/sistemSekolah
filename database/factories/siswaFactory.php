<?php

namespace Database\Factories;
use App\Models\admin;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\siswa>
 */
class siswaFactory extends Factory
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
            'nama' => $this->faker->name,
            'tb' => $this->faker->numberBetween(140, 180),
            'bb' => $this->faker->numberBetween(35, 80),
            'id' => admin::factory()->create(['role' => 'siswa'])->id,
        ];
    }
}
