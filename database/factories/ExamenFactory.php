<?php

namespace Database\Factories;

use App\Models\CodigoCup;
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
        // Crear un código CUP único para este examen
        $codigoCup = fake()->unique()->numerify('######');
        
        CodigoCup::create([
            'codigo' => $codigoCup,
            'nombre' => fake()->words(4, true),
            'grupo' => fake()->optional()->word(),
            'activo' => true,
            'nivel' => 1,
        ]);

        return [
            'nombre' => fake()->words(3, true),
            'cup' => $codigoCup,
            'valor' => fake()->randomFloat(2, 50, 500),
            'descripcion' => fake()->optional()->sentence(),
            'nombre_alternativo' => fake()->optional()->words(2, true),
        ];
    }
}
