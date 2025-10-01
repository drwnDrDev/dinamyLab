// Ejemplo de tests/Feature/TuPrueba.php

<?php

use App\Models\Sede;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

test('orden page is displayed for authenticated employee', function () {

    // Crear roles y permisos necesarios
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'agente']);
    Role::create(['name' => 'prestador']);
    Role::create(['name' => 'paciente']);
    Role::create(['name' => 'contable']);
    Permission::create(['name' => 'ver_resultado']);
    Permission::create(['name' => 'crear_persona']);
    Permission::create(['name' => 'editar_persona']);
    Permission::create(['name' => 'ver_persona']);
    Permission::create(['name' => 'ver_orden']);
    Permission::create(['name' => 'eliminar_persona']);
    Permission::create(['name' => 'ver_cuentas']);
    Permission::create(['name' => 'crear_cuenta']);
    Permission::create(['name' => 'editar_cuenta']);
    Permission::create(['name' => 'ver_examen']);
    Permission::create(['name' => 'crear_examen']);
    Permission::create(['name' => 'editar_examen']);
    Permission::create(['name' => 'ver_empresa']);
    Permission::create(['name' => 'ver_facturas']);



        $role = Role::findByName('prestador');
        $role->givePermissionTo(Permission::all());
    // Crear una sede y un empleado asociado

    $cedula = App\Models\TipoDocumento::factory()->create([
        'cod_dian' => 'CC',
        'nombre' => 'Cédula de ciudadanía',
    ]);

    $sede = Sede::factory()->create();
    $usuario = User::factory()->create();
    $usuario->assignRole('prestador');
    $empleado = $usuario->empleado()->create([
        'user_id' => $usuario->id,
        'cargo' => 'Agente',
        'numero_documento' => fake()->unique()->numerify('#########'),
        'fecha_nacimiento' => fake()->date(),
        'empresa_id' => $sede->empresa_id,
        'tipo_documento_id' => $cedula->id,
    ]);
    $empleado->sedes()->attach($sede->id);
    $this->assertNotNull($usuario);
    $this->assertNotNull($empleado);

    session()->put('sede', $sede);

    $response = $this
        ->actingAs($usuario)
        ->get('/ordenes-medicas');

    $response->assertStatus(302);

    $response->assertRedirect('/ordenes-medicas/create');

});

test('orden page is not accessible for unauthenticated user', function () {
    $response = $this->get('/ordenes-medicas');
    $response->assertStatus(302); // Redirige a la página de login
    $response->assertRedirect('/login');
});

// Otros tests relacionados con la página de órdenes médicas pueden ir aquí
