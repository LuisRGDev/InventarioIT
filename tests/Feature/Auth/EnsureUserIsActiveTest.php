<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Regresión: desactivar una cuenta desde el panel de administración antes
 * solo bloqueaba logins nuevos (el chequeo vivía únicamente en
 * LoginForm::authenticate()). Alguien con sesión ya abierta seguía
 * teniendo acceso completo hasta que su sesión expirara sola o cerrara
 * sesión por su cuenta. Este middleware corta el acceso en la siguiente
 * petición.
 *
 * Nota: no se prueba aquí el camino de las acciones AJAX de Livewire
 * (/livewire/update) por separado. Livewire salta a propósito todo el
 * mecanismo de "persistent middleware" cuando detecta una petición de
 * prueba (ver PersistentMiddleware::boot(), el comentario literal es
 * "Only apply middleware to requests hitting the Livewire update
 * endpoint, and not any fake requests such as a test" — y
 * HandleRequests::isLivewireRoute() trae un @todo del propio paquete
 * reconociendo la limitación). Por eso tampoco existe ese tipo de test
 * para role:/permission:, que usa exactamente el mismo mecanismo
 * (ver AuthorizationTest.php). "active" queda registrado igual en
 * AppServiceProvider por consistencia y porque en producción sí aplica,
 * pero no es verificable con Volt::test()/Livewire::test().
 */
class EnsureUserIsActiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_deactivated_user_with_a_live_session_loses_access_on_the_next_request(): void
    {
        Role::firstOrCreate(['name' => Roles::TECNICO]);

        $user = User::factory()->create(['active' => true]);
        $user->assignRole(Roles::TECNICO);

        $this->actingAs($user);

        $this->get('/dashboard')->assertOk();

        $user->update(['active' => false]);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
