<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\AdminAudit;
use App\Models\LoginAudit;
use App\Models\User;
use App\Services\AdminAuditService;
use App\Services\TwoFactorService;
use App\Support\Roles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // in_array estricto en vez de pasar el valor directo a scopeRole():
        // spatie/laravel-permission resuelve el nombre con
        // Role::findByName() y lanza RoleDoesNotExist si no coincide con
        // ninguno de los 3 roles reales — un valor cualquiera en la
        // querystring (?role=lo-que-sea) tiraba la página con un 500 en
        // vez de simplemente ignorar un filtro inválido.
        if ($request->filled('role') && in_array($request->role, [Roles::ADMIN, Roles::TECNICO, Roles::SOLO_LECTURA], true)) {
            $query->role($request->role);
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        $recentAudits = LoginAudit::latest('created_at')->limit(15)->get();
        $adminAudits = AdminAudit::latest('created_at')->latest('id')->limit(15)->get();

        return view('users.index', compact('users', 'recentAudits', 'adminAudits'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request, AdminAuditService $audit): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'active' => $request->boolean('active'),
        ]);

        // La contraseña la fijó un admin: es temporal, el usuario debe
        // cambiarla en su primer inicio de sesión.
        $user->forceFill(['must_change_password' => true])->save();

        $user->assignRole($data['role']);

        $audit->record($request->user(), AdminAudit::USER_CREATED, $user, [
            'role' => $data['role'],
            'active' => $user->active,
            'temporary_password' => true,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user, AdminAuditService $audit): RedirectResponse
    {
        $data = $request->validated();
        $willBeActive = $request->boolean('active');
        $willBeAdmin = $data['role'] === Roles::ADMIN;

        // No se puede desactivar ni quitarle el rol Admin TI al único
        // administrador activo del sistema: eso dejaría la app sin nadie
        // que pueda volver a administrar usuarios, ni siquiera el propio
        // afectado.
        $isCurrentlyActiveAdmin = $user->active && $user->hasRole(Roles::ADMIN);
        $willRemainActiveAdmin = $willBeActive && $willBeAdmin;

        if ($isCurrentlyActiveAdmin && ! $willRemainActiveAdmin) {
            $otherActiveAdmins = User::active()->role(Roles::ADMIN)->where('id', '!=', $user->id)->count();

            if ($otherActiveAdmins === 0) {
                return back()->withInput()->with('error', 'No puedes quitar el rol de Admin TI ni desactivar a este usuario: es el único administrador activo del sistema.');
            }
        }

        $before = [
            'name' => $user->name,
            'email' => $user->email,
            'active' => $user->active,
            'role' => $user->getRoleNames()->first(),
        ];

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'active' => $willBeActive,
        ]);

        $details = [];

        if (! empty($data['password'])) {
            $user->update(['password' => $data['password']]);

            // Un admin que le fija la contraseña a OTRO usuario (típicamente
            // porque la olvidó o se sospecha de ella): la nueva es temporal,
            // debe cambiarla al entrar, y se cierran sus sesiones abiertas.
            // Si el admin cambia la suya, es la que eligió: no se fuerza nada.
            if ($user->id !== $request->user()->id) {
                $user->forceFill(['must_change_password' => true])->save();
                $user->invalidateSessions();
                $details['password_changed'] = true;
            }
        }

        $user->syncRoles([$data['role']]);

        $after = [
            'name' => $user->name,
            'email' => $user->email,
            'active' => $user->active,
            'role' => $data['role'],
        ];

        foreach ($after as $field => $value) {
            if ($before[$field] !== $value) {
                $details[$field] = ['from' => $before[$field], 'to' => $value];
            }
        }

        if ($details) {
            $audit->record($request->user(), AdminAudit::USER_UPDATED, $user, $details);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Quita el 2FA a un usuario (teléfono y códigos de respaldo perdidos) y
     * le cierra todas las sesiones; al volver a entrar tendrá que
     * configurarlo de nuevo. Queda registrado en la bitácora de acciones administrativas.
     * No se permite sobre uno mismo: si es el único admin y pierde todo,
     * la vía es el comando `php artisan 2fa:reset {correo}` en el servidor.
     */
    public function resetTwoFactor(User $user, TwoFactorService $twoFactor, AdminAuditService $audit): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes restablecer tu propio 2FA desde aquí. Pídele a otro administrador que lo haga.');
        }

        if (! $user->hasTwoFactorEnabled()) {
            return back()->with('error', 'Este usuario no tiene el 2FA activado.');
        }

        $twoFactor->reset($user);

        $audit->record(auth()->user(), AdminAudit::TWO_FACTOR_RESET, $user);

        return redirect()->route('users.edit', $user)->with('success', 'Se restableció el 2FA de '.$user->name.' y se cerraron sus sesiones.');
    }
}
