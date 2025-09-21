<?php

it('displays the welcome page', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
  
});
