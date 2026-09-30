<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Cubre el panel de administración de usuarios (antes solo existía
 * tinker/seeder para crear cuentas o asignar roles): Admin TI puede
 * listar/crear/editar usuarios y (des)activar cuentas; cualquier otro rol
 * recibe 403. También cubre el candado de "no te quedes sin admins": no se
 * puede desactivar ni quitarle el rol Admin TI al único administrador
 * activo del sistema.
 */
class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([Roles::ADMIN, Roles::TECNICO, Roles::SOLO_LECTURA] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    protected function adminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::ADMIN);

        return $user;
    }

    public function test_non_admin_roles_are_forbidden_from_every_users_route(): void
    {
        $tecnico = User::factory()->create();
        $tecnico->assignRole(Roles::TECNICO);

        $this->actingAs($tecnico)->get('/users')->assertForbidden();
        $this->actingAs($tecnico)->get('/users/create')->assertForbidden();
        $this->actingAs($tecnico)->post('/users', [])->assertForbidden();
    }

    public function test_admin_can_view_the_users_index(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->get('/users')->assertOk();
    }

    public function test_admin_can_create_a_user_with_a_role(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Nuevo Técnico',
            'email' => 'nuevo.tecnico@middleby.com',
            'password' => 'ClaveFuerte!9',
            'password_confirmation' => 'ClaveFuerte!9',
            'role' => Roles::TECNICO,
            'active' => '1',
        ]);

        $response->assertRedirect(route('users.index'));

        $created = User::where('email', 'nuevo.tecnico@middleby.com')->firstOrFail();
        $this->assertTrue($created->hasRole(Roles::TECNICO));
        $this->assertTrue($created->active);
        $this->assertTrue(Hash::check('ClaveFuerte!9', $created->password));
    }

    public function test_weak_password_is_rejected_when_creating_a_user(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->post('/users', [
            'name' => 'Usuario Débil',
            'email' => 'debil@middleby.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => Roles::TECNICO,
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'debil@middleby.com']);
    }

    public function test_admin_can_deactivate_another_user(): void
    {
        $admin = $this->adminUser();
        $target = User::factory()->create(['active' => true]);
        $target->assignRole(Roles::TECNICO);

        $response = $this->actingAs($admin)->put("/users/{$target->id}", [
            'name' => $target->name,
            'email' => $target->email,
            'role' => Roles::TECNICO,
            // "active" ausente = checkbox sin marcar = desactivar
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertFalse($target->fresh()->active);
    }

    public function test_admin_can_change_another_users_role(): void
    {
        $admin = $this->adminUser();
        $target = User::factory()->create();
        $target->assignRole(Roles::SOLO_LECTURA);

        $this->actingAs($admin)->put("/users/{$target->id}", [
            'name' => $target->name,
            'email' => $target->email,
            'role' => Roles::TECNICO,
            'active' => '1',
        ]);

        $target->refresh();
        $this->assertTrue($target->hasRole(Roles::TECNICO));
        $this->assertFalse($target->hasRole(Roles::SOLO_LECTURA));
    }

    public function test_blank_password_on_update_does_not_change_the_existing_password(): void
    {
        $admin = $this->adminUser();
        $target = User::factory()->create();
        $originalHash = $target->password;

        $this->actingAs($admin)->put("/users/{$target->id}", [
            'name' => $target->name,
            'email' => $target->email,
            'role' => Roles::TECNICO,
            'active' => '1',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $this->assertSame($originalHash, $target->fresh()->password);
    }

    public function test_cannot_deactivate_the_last_active_admin(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->put("/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => Roles::ADMIN,
            // sin "active": intento de autodesactivarse
        ]);

        $response->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->active);
    }

    public function test_cannot_demote_the_last_active_admin(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->put("/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => Roles::TECNICO,
            'active' => '1',
        ]);

        $response->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->hasRole(Roles::ADMIN));
    }

    public function test_can_deactivate_an_admin_when_another_active_admin_remains(): void
    {
        $admin = $this->adminUser();
        $secondAdmin = User::factory()->create();
        $secondAdmin->assignRole(Roles::ADMIN);

        $response = $this->actingAs($admin)->put("/users/{$secondAdmin->id}", [
            'name' => $secondAdmin->name,
            'email' => $secondAdmin->email,
            'role' => Roles::ADMIN,
            // sin "active": se desactiva, pero $admin sigue activo
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertFalse($secondAdmin->fresh()->active);
    }
}
