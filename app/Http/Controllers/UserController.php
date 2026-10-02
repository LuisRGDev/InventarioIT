<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\LoginAudit;
use App\Models\User;
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

        return view('users.index', compact('users', 'recentAudits'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'active' => $request->boolean('active'),
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
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

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'active' => $willBeActive,
        ]);

        if (! empty($data['password'])) {
            $user->update(['password' => $data['password']]);
        }

        $user->syncRoles([$data['role']]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }
}
