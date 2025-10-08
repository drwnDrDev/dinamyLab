<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipoDocumento>
 */
class TipoDocumentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //             $table->string('nombre', 100)->unique();
            // $table->string('cod_rips', 10);
            // $table->string('cod_dian', 10)->nullable();
            // $table->boolean('es_nacional')->default(true);
            // $table->boolean('es_paciente')->default(true); // Se puede usar para filtrar tipos de documento solo para pacientes
            // $table->boolean('es_pagador')->default(false); // Solo para pagadores (DIAN)
            // $table->boolean('requiere_acudiente')->default(false);
            // $table->unsignedTinyInteger('edad_minima')->default(0); // Edad mínima para este tipo de documento
            // $table->unsignedTinyInteger('edad_maxima')->default(130);
            // $table->string('regex_validacion')->nullable(); // Expresión regular para validación
            // $table->unsignedTinyInteger('nivel')->default(0); // Nivel de importancia o jerarquía del tipo de documento
            'nombre' => fake()->unique()->word(),
            'cod_rips' => fake()->unique()->bothify('???###'),
            'cod_dian' => fake()->optional()->bothify('???###'),
            'es_nacional' => fake()->boolean(90), // 90% de probabilidad de ser true
            'es_paciente' => fake()->boolean(80), // 80% de probabilidad de ser true
            'es_pagador' => fake()->boolean(20), // 20% de probabilidad de ser true
            'requiere_acudiente' => fake()->boolean(10), // 10% de probabilidad de ser true
            'edad_minima' => fake()->numberBetween(0, 18),
            'edad_maxima' => fake()->numberBetween(18, 130),
            'regex_validacion' => null, // Puede ser nulo
            'nivel' => fake()->numberBetween(0, 10),
            
        ];
    }
}
