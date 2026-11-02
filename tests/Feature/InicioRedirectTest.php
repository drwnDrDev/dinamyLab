<?php

use App\Models\User;

test('inicio sin sede elegida redirige al dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/inicio')
        ->assertRedirect(route('dashboard'));
});
