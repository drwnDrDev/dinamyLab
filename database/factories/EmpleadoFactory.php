<?php


namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Persona>
 */
class EmpleadoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cargo' => fake()->jobTitle(),
            'firma' => null,
            'tipo_documento_id' => 1,
            'numero_documento' => fake()->unique()->numerify('#########'),
            'fecha_nacimiento' => fake()->date(),
            'user_id' => fake()->numberBetween(1, 5),
            'empresa_id' => 1,
        ];
    }
}
