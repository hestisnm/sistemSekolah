<?php

namespace Database\Factories;
use App\Models\admin;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\konten>
 */
class guruFactory extends Factory
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
          'admin_id' => admin::factory()->create(['role' => 'guru'])->id,
            'nama' => $this->faker->name,
            'mapel' => $this->faker->randomElement([
                'Matematika',
                'IPAS',
                'Bahasa Indonesia',
                'Informatika'
            ]),
        ];
    }
}
