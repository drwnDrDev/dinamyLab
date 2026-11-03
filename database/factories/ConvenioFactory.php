<?php

namespace Database\Factories;

use App\Models\Empresa;
use App\Models\TipoDocumento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Convenio>
 */
class ConvenioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tipo_documento_id' => TipoDocumento::factory(),
            'empresa_id' => Empresa::factory(),
            'numero_documento' => fake()->unique()->numerify('#########'),
            'razon_social' => fake()->company(),
            'numero_contrato' => fake()->optional()->bothify('CT-####'),
        ];
    }
}
