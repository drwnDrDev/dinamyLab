// Ejemplo de tests/Feature/TuPrueba.php

<?php

use App\Models\Sede;
use App\Models\User;

test('orden page is displayed for authenticated employee', function () {

    $sede = Sede::factory()->create();
    $usuario = User::factory()->create([
        'sede_id' => $sede->id,
        'role' => 'empleado',
    ]);
        $this->assertNotNull($usuario);


    $response = $this
        ->actingAs($usuario)
        ->get('/ordenes-medicas');

    $response->assertStatus(200);
});
