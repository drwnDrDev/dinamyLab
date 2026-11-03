<?php


namespace Database\Factories;

use App\Models\Empresa;
use App\Models\TipoDocumento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Empleado>
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
            // Las FK se crean con su factory: en la BD de tests no hay registros con id fijo
            'tipo_documento_id' => TipoDocumento::factory(),
            'numero_documento' => fake()->unique()->numerify('#########'),
            'fecha_nacimiento' => fake()->date(),
            'user_id' => User::factory(),
            'empresa_id' => Empresa::factory(),
        ];
    }
}
