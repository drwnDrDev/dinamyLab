<?php

use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;

test('inicio sin sede elegida redirige al dashboard', function () {
    $this->seed(RolesYPermisosSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('prestador');

    $this->actingAs($user)
        ->get('/inicio')
        ->assertRedirect(route('dashboard'));
});
