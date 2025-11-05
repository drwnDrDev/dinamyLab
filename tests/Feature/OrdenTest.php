<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Persona;
use App\Models\Examen;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdenTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_create_order_view_is_rendered_correctly(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/ordenes-medicas/create');

        $response->assertStatus(200);
        $response->assertSee('Crear Nueva Orden');
    }

    public function test_a_new_order_can_be_created(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $paciente = Persona::factory()->create();
        $examen1 = Examen::factory()->create(['valor' => 100]);
        $examen2 = Examen::factory()->create(['valor' => 50]);

        $orderData = [
            'numero_orden' => '12345',
            'paciente_id' => $paciente->id,
            'examenes' => [
                ['examen_id' => $examen1->id, 'cantidad' => 1],
                ['examen_id' => $examen2->id, 'cantidad' => 2],
            ],
            'total' => 200,
        ];

        $response = $this->postJson('/ordenes-medicas/store', $orderData);

        $response->assertStatus(201)
            ->assertJsonFragment(['numero' => '12345']);

        $this->assertDatabaseHas('ordenes_medicas', [
            'numero' => '12345',
            'paciente_id' => $paciente->id,
            'total' => 200,
        ]);
    }

    public function test_it_returns_validation_errors_if_data_is_missing(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/ordenes-medicas/store', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['paciente_id', 'examenes']);
    }
}