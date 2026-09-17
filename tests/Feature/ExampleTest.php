<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Verificar que la ruta principal redirige al kiosco.
     */
    public function test_root_redirects_to_kiosco(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/kiosco');
    }

    /**
     * Verificar que el endpoint de salud responde correctamente.
     */
    public function test_health_check_returns_successful_response(): void
    {
        $response = $this->get('/up');

        $response->assertStatus(200);
    }

    /**
     * Verificar que la pantalla de inicio de sesión responde 200.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
