<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Regresión: la sesión autenticada debe sobrevivir a una petición stateful
 * a la API (la que dispara el dashboard vía resources/js/obtenerStaticos.js).
 *
 * En el navegador cada petición es un proceso PHP nuevo, así que el driver
 * de sesión se resuelve de cero leyendo la cookie. En tests el driver queda
 * cacheado en memoria, por eso aquí lo olvidamos y enviamos la cookie cruda.
 */
test('la sesión autenticada sobrevive a una petición stateful a la API', function () {
    config(['session.driver' => 'database']);

    Route::middleware('api')->get('/__test-api-session', function (Request $request) {
        return response()->json([
            'session_id' => $request->session()->getId(),
            'has_auth' => $request->session()->has(Auth::guard('web')->getName()),
        ]);
    });

    $user = User::factory()->create();

    $login = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $login->assertRedirect(route('dashboard', absolute: false));

    $cookieName = config('session.cookie');
    $rawCookie = $login->getCookie($cookieName, decrypt: false)->getValue();
    $loginSessionId = $login->getCookie($cookieName)->getValue();

    // Simula un proceso PHP nuevo: sin driver de sesión cacheado.
    $this->app['session']->forgetDrivers();

    $response = $this->withUnencryptedCookie($cookieName, $rawCookie)
        ->withHeader('Referer', 'http://localhost')
        ->get('/__test-api-session');

    $response->assertOk()
        ->assertJson([
            'session_id' => $loginSessionId,
            'has_auth' => true,
        ]);
});
