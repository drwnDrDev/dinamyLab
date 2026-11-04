<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpresaPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesYPermisosSeeder::class);
    }

    public function test_agent_and_provider_cannot_access_system_configuration(): void
    {
        foreach (['agente', 'prestador'] as $role) {
            $user = User::factory()->create([
                'email' => "{$role}@example.test",
            ]);
            $user->assignRole($role);

            $this->actingAs($user)
                ->get('/admin/sedes/create')
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('empresa.show'))
                ->assertForbidden();
        }
    }

    public function test_admin_can_access_system_configuration(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/cups')
            ->assertOk();
    }
}
