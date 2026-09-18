<?php

namespace Tests\Feature;

use Tests\TestCase;

class ConcurrencyAndEdgeCasesTest extends TestCase
{
    /**
     * Verificar que el catálogo de productos responde 200 con formato JSON.
     */
    public function test_kiosco_api_productos_returns_json(): void
    {
        $response = $this->getJson('/kiosco/api/productos');
        $response->assertStatus(200)
                 ->assertJsonStructure(['categorias', 'productos', 'combos', 'sedes']);
    }

    /**
     * Verificar que el endpoint de huella responde 200 con formato JSON.
     */
    public function test_kiosco_api_huella_returns_json(): void
    {
        $response = $this->getJson('/kiosco/api/huella');
        $response->assertStatus(200)
                 ->assertJsonStructure(['version']);
    }

    /**
     * Verificar que peticiones con caracteres inusuales no provocan error 500.
     */
    public function test_edge_case_special_chars_query(): void
    {
        $response = $this->get("/kiosco?buscar=" . urlencode("' OR 1=1 --"));
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    /**
     * Verificar que verificación de cédula con parámetros erróneos no provoca 500.
     */
    public function test_edge_case_verificar_cedula_invalid(): void
    {
        $response = $this->getJson('/registro/verificar-cedula?cedula=invalid_cedula_string');
        $this->assertNotEquals(500, $response->getStatusCode());
    }
}
