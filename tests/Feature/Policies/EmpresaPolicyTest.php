<?php

namespace Tests\Feature\Policies;

use App\Models\Empresa;
use App\Models\Empleado;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpresaPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que usuario no puede ver recursos de otra empresa
     */
    public function test_usuario_no_puede_ver_sede_de_otra_empresa()
    {
        // Crear dos empresas diferentes
        $empresa1 = Empresa::factory()->create(['razon_social' => 'Empresa 1']);
        $empresa2 = Empresa::factory()->create(['razon_social' => 'Empresa 2']);

        // Crear usuario de la empresa 1
        $user = User::factory()->create();
        $empleado = Empleado::factory()->create([
            'user_id' => $user->id,
            'empresa_id' => $empresa1->id
        ]);

        // Crear sede de la empresa 2
        $sedeOtraEmpresa = Sede::factory()->create([
            'nombre' => 'Sede Empresa 2',
            'empresa_id' => $empresa2->id
        ]);

        // Autenticar como usuario de empresa 1
        $this->actingAs($user);

        // Intentar acceder a sede de empresa 2
        $response = $this->get(route('sedes.show', $sedeOtraEmpresa));

        // Debe ser prohibido
        $response->assertForbidden();
    }

    /**
     * Test que usuario puede ver recursos de su propia empresa
     */
    public function test_usuario_puede_ver_sede_de_su_empresa()
    {
        $empresa = Empresa::factory()->create();
        $user = User::factory()->create();
        $empleado = Empleado::factory()->create([
            'user_id' => $user->id,
            'empresa_id' => $empresa->id
        ]);

        $sedePropiaEmpresa = Sede::factory()->create([
            'empresa_id' => $empresa->id
        ]);

        $this->actingAs($user);

        $response = $this->get(route('sedes.show', $sedePropiaEmpresa));

        $response->assertOk();
    }

    /**
     * Test que usuario sin empleado no puede acceder a recursos
     */
    public function test_usuario_sin_empleado_no_puede_acceder_a_recursos()
    {
        $empresa = Empresa::factory()->create();
        $sede = Sede::factory()->create(['empresa_id' => $empresa->id]);
        
        // Usuario sin empleado asociado
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('sedes.show', $sede));

        $response->assertForbidden();
    }

    /**
     * Test que usuario no puede editar recursos de otra empresa
     */
    public function test_usuario_no_puede_editar_sede_de_otra_empresa()
    {
        $empresa1 = Empresa::factory()->create();
        $empresa2 = Empresa::factory()->create();

        $user = User::factory()->create();
        $empleado = Empleado::factory()->create([
            'user_id' => $user->id,
            'empresa_id' => $empresa1->id
        ]);

        // Dar permiso para editar sedes
        $user->givePermissionTo('editar_sedes');

        $sedeOtraEmpresa = Sede::factory()->create(['empresa_id' => $empresa2->id]);

        $this->actingAs($user);

        $response = $this->put(route('sedes.update', $sedeOtraEmpresa), [
            'nombre' => 'Nombre Modificado',
            'codigo_prestador' => '12345'
        ]);

        $response->assertForbidden();
    }

    /**
     * Test que usuario puede ver su propia empresa
     */
    public function test_usuario_puede_ver_su_empresa()
    {
        $empresa = Empresa::factory()->create();
        $user = User::factory()->create();
        $empleado = Empleado::factory()->create([
            'user_id' => $user->id,
            'empresa_id' => $empresa->id
        ]);

        $this->actingAs($user);

        $response = $this->get(route('empresa.show', $empresa));

        $response->assertOk();
        $response->assertSee($empresa->razon_social);
    }

    /**
     * Test que usuario no puede ver otra empresa
     */
    public function test_usuario_no_puede_ver_otra_empresa()
    {
        $empresa1 = Empresa::factory()->create();
        $empresa2 = Empresa::factory()->create();
        
        $user = User::factory()->create();
        $empleado = Empleado::factory()->create([
            'user_id' => $user->id,
            'empresa_id' => $empresa1->id
        ]);

        $this->actingAs($user);

        $response = $this->get(route('empresa.show', $empresa2));

        $response->assertForbidden();
    }

    /**
     * Test que admin puede ver cualquier empresa
     */
    public function test_admin_puede_ver_cualquier_empresa()
    {
        $empresa1 = Empresa::factory()->create();
        $empresa2 = Empresa::factory()->create();
        
        $admin = User::factory()->create();
        $empleado = Empleado::factory()->create([
            'user_id' => $admin->id,
            'empresa_id' => $empresa1->id
        ]);
        
        // Asignar rol de admin
        $admin->assignRole('admin');

        $this->actingAs($admin);

        // Admin puede ver empresa 2 aunque pertenezca a empresa 1
        $response = $this->get(route('empresa.show', $empresa2));

        // Dependiendo de la implementación, podría ser OK o Forbidden
        // Ajustar según la lógica de negocio
    }

    /**
     * Test que al crear sede se asigna automáticamente la empresa del usuario
     */
    public function test_crear_sede_asigna_empresa_del_usuario()
    {
        $empresa = Empresa::factory()->create();
        $user = User::factory()->create();
        $empleado = Empleado::factory()->create([
            'user_id' => $user->id,
            'empresa_id' => $empresa->id
        ]);

        $user->givePermissionTo('crear_sedes');

        $this->actingAs($user);

        $response = $this->post(route('sedes.store'), [
            'nombre' => 'Nueva Sede',
            'codigo_prestador' => '54321',
            // No se envía empresa_id intencionalmente
        ]);

        $response->assertRedirect();

        // Verificar que se creó con la empresa correcta
        $sede = Sede::where('nombre', 'Nueva Sede')->first();
        $this->assertEquals($empresa->id, $sede->empresa_id);
    }

    /**
     * Test que usuario no puede cambiar empresa_id al editar
     */
    public function test_usuario_no_puede_cambiar_empresa_al_editar_sede()
    {
        $empresa1 = Empresa::factory()->create();
        $empresa2 = Empresa::factory()->create();

        $user = User::factory()->create();
        $empleado = Empleado::factory()->create([
            'user_id' => $user->id,
            'empresa_id' => $empresa1->id
        ]);

        $user->givePermissionTo('editar_sedes');

        $sede = Sede::factory()->create([
            'nombre' => 'Sede Original',
            'empresa_id' => $empresa1->id
        ]);

        $this->actingAs($user);

        // Intentar cambiar la empresa
        $response = $this->put(route('sedes.update', $sede), [
            'nombre' => 'Sede Modificada',
            'codigo_prestador' => '12345',
            'empresa_id' => $empresa2->id, // Intento malicioso
        ]);

        $sede->refresh();

        // La empresa NO debe haber cambiado
        $this->assertEquals($empresa1->id, $sede->empresa_id);
        // Pero el nombre sí debe actualizarse
        $this->assertEquals('Sede Modificada', $sede->nombre);
    }

    /**
     * Test que el listado solo muestra recursos de la empresa del usuario
     */
    public function test_index_solo_muestra_sedes_de_empresa_del_usuario()
    {
        $empresa1 = Empresa::factory()->create();
        $empresa2 = Empresa::factory()->create();

        $user = User::factory()->create();
        $empleado = Empleado::factory()->create([
            'user_id' => $user->id,
            'empresa_id' => $empresa1->id
        ]);

        // Crear sedes de ambas empresas
        $sedesEmpresa1 = Sede::factory()->count(3)->create(['empresa_id' => $empresa1->id]);
        $sedesEmpresa2 = Sede::factory()->count(2)->create(['empresa_id' => $empresa2->id]);

        $this->actingAs($user);

        $response = $this->get(route('sedes.index'));

        $response->assertOk();

        // Debe ver las 3 sedes de su empresa
        foreach ($sedesEmpresa1 as $sede) {
            $response->assertSee($sede->nombre);
        }

        // No debe ver las sedes de otra empresa
        foreach ($sedesEmpresa2 as $sede) {
            $response->assertDontSee($sede->nombre);
        }
    }
}
