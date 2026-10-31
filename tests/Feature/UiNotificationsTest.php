<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class UiNotificationsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_shows_flash_notifications_after_a_redirect(): void
    {
        Route::middleware('web')->get('/_test/success-redirect', function () {
            return redirect('/login')->with('success', 'La operación se completó correctamente.');
        });

        $this->followingRedirects()
            ->get('/_test/success-redirect')
            ->assertOk()
            ->assertSee('Operación completada')
            ->assertSee('La operación se completó correctamente.');
    }

    public function test_announces_validation_errors_without_repeating_field_messages(): void
    {
        $errors = new ViewErrorBag;
        $errors->put('default', new MessageBag(['email' => ['El correo es obligatorio.']]));

        $this->withSession(['errors' => $errors])
            ->get('/login')
            ->assertOk()
            ->assertSee('No se pudo completar la solicitud.')
            ->assertSee('Revisa los mensajes junto a los campos')
            ->assertSee('El correo es obligatorio.');
    }

    public function test_explains_when_a_protected_page_redirects_to_login(): void
    {
        $this->withSession(['url.intended' => '/dashboard'])
            ->get('/login')
            ->assertOk()
            ->assertSee('Inicia sesión para continuar.');
    }

    public function test_shows_a_localized_page_for_missing_routes(): void
    {
        $this->get('/esta-pagina-no-existe')
            ->assertNotFound()
            ->assertSee('No encontramos esta página')
            ->assertSee('Volver al inicio')
            ->assertDontSee('Stack trace');
    }

    public function test_shows_a_generic_message_for_unexpected_server_errors(): void
    {
        config(['app.debug' => false]);

        Route::get('/_test/error', function () {
            throw new \RuntimeException('Detalle interno que no debe mostrarse.');
        });

        $this->get('/_test/error')
            ->assertServerError()
            ->assertSee('Tuvimos un problema')
            ->assertDontSee('Detalle interno que no debe mostrarse.');
    }

    public function test_keeps_api_errors_in_json_format(): void
    {
        $this->getJson('/api/ruta-inexistente')
            ->assertNotFound()
            ->assertJsonStructure(['message']);
    }
}
