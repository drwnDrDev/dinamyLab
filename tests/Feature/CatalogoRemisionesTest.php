<?php

use App\Models\IpsExterna;
use App\Models\Remision;
use App\Models\User;
use App\Models\ValorRemision;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\ValorRemisionSeeder;

it('importa el CSV sin duplicados y conserva costos ausentes y precios originales', function () {
    $this->seed(ValorRemisionSeeder::class);
    IpsExterna::where('nombre', 'Idime')->update(['nit' => '123-4', 'telefono' => '3001234567']);
    $this->seed(ValorRemisionSeeder::class);

    expect(Remision::count())->toBe(51);
    expect(IpsExterna::count())->toBe(3);
    expect(ValorRemision::count())->toBe(53);
    expect(IpsExterna::where('nombre', 'Idime')->first())
        ->nit->toBe('123-4')->telefono->toBe('3001234567');
    expect(IpsExterna::where('nombre', 'Colcan')->first())->nit->toBeNull()->telefono->toBeNull();
    expect(Remision::where('cup', '901101')->first()->valores()->with('ipsExterna')->get()->pluck('ipsExterna.nombre')->sort()->values()->all())
        ->toBe(['Colcan', 'Idime']);
    expect(Remision::where('cup', '20261')->first()->valores->first()->valor_remitido)->toBeNull();
    expect(Remision::where('cup', '904927')->first()->valores->first())
        ->precio->toBe('14000.00')->valor_remitido->toBe('95000.00');
});

it('entrega el catálogo completo con las tarifas e IPS a quien puede crear órdenes', function () {
    $this->seed([RolesYPermisosSeeder::class, ValorRemisionSeeder::class]);
    $this->getJson('/api/remisiones')->assertUnauthorized();
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum')->getJson('/api/remisiones')->assertForbidden();
    $user->assignRole('prestador');

    $respuesta = $this->getJson('/api/remisiones')->assertOk()->assertJsonCount(51, 'data')
        ->assertJsonStructure(['data' => [['id', 'cup', 'nombre', 'valores' => [
            ['id', 'precio', 'valor_remitido', 'ips_externa_id', 'ips_externa' => ['id', 'nombre', 'nit', 'telefono']],
        ]]]]);
    $baciloscopia = collect($respuesta->json('data'))->firstWhere('cup', '901101');
    expect($baciloscopia['valores'])->toHaveCount(2);
});
