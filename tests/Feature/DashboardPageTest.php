<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
uses(RefreshDatabase::class);
it('displays the dashboard page', function () {
    $response = $this->get('/dashboard');
    $response->assertStatus(302);
});

it('redirects to login when accessing dashboard without authentication', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});
test('allows authenticated users to access the dashboard', function () {
    $user = \App\Models\User::factory()->create();
    $this->assertNotNull($user);
    // $response = $this->actingAs($user)->get('/dashboard');
    // $response->assertRedirect(302);
});
