<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
uses(RefreshDatabase::class);


test('orden page is displayed for authenticated employee', function () {

    $usuarios = \App\Models\User::factory(100)->create();
    $this->assertNotNull($usuarios);
    $response = $this
        ->actingAs($usuarios->first())
        ->get('/orden');

});

