<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * El auto-registro público está deshabilitado (ver routes/auth.php): las
     * cuentas de esta herramienta interna solo las crea un Admin TI. La ruta
     * 'register' no existe, por lo que debe devolver 404.
     */
    public function test_registration_route_is_disabled(): void
    {
        $response = $this->get('/register');

        $response->assertNotFound();
    }
}
