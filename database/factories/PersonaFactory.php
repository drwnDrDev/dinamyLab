<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Persona>
 */
class PersonaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'primer_nombre' => fake()->firstName(),
            'segundo_nombre' => fake()->optional()->firstName(),
            'primer_apellido' => fake()->lastName(),
            'segundo_apellido' => fake()->optional()->lastName(),
            'tipo_documento_id' => fake()->numberBetween(1, 13),
            'numero_documento' => fake()->unique()->numerify('##########'),
            'fecha_nacimiento' => fake()->date(),
            'sexo' => fake()->optional()->randomElement(['M', 'F']),
            'pais_origen' => '170',
        ];
    }

    /** datos de  contacto opcionales */
    public function withContactInfo(): static
    {
        return $this->afterCreating(function ($persona) {
            // Crear dirección
            $persona->direccion()->create([
                'direccion' => fake()->streetName(),
                'municipio_id' => fake()->randomElement([13440, 25878, 25843, 25817, 25805, 25797, 25781]),
                'codigo_postal' => fake()->postcode(),
                'zona' => fake()->randomElement(['02', '01']),
                'pais_id' => '170', // Colombia
            ]);

            // Crear teléfono
            $persona->telefonos()->create([
                'numero' => fake()->phoneNumber(),

            ]);

            // Crear correo electrónico
            $persona->email()->create([
                'email' => fake()->unique()->safeEmail(),
            ]);


            $persona->afiliacionSalud()->create([
                'eps' => fake()->company() . ' EPS',
                'tipo_afiliacion' => fake()->randomElement(["12","12","12","11","05"]),
            ]);
        });

    }
}
