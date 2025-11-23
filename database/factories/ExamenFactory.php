<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Examen>
 */
class ExamenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->words(3, true),
            'cup' => fake()->optional()->numerify('######'),
            'valor' => fake()->randomFloat(2, 50, 500),
            'desripcion' => fake()->optional()->sentence(),
            'nombre_alternativo' => fake()->optional()->words(2, true),
        ];
    }
}
