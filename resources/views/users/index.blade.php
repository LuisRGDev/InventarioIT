<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-900 leading-tight tracking-tight flex items-center gap-2.5">
                    <span class="w-2.5 h-7 bg-gradient-to-b from-middleby-600 to-amber-500 rounded-full inline-block shadow-sm"></span>
                    {{ __('Administración de Usuarios') }}
                </h2>
                <p class="text-xs sm:text-sm font-semibold text-slate-500 mt-1">Crea cuentas, asigna roles y activa o desactiva accesos. Solo visible para Admin TI.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('users.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-middleby-800 to-middleby-700 hover:from-middleby-700 hover:to-middleby-600 text-white text-sm font-black rounded-2xl shadow-md hover:shadow-lg transition-all duration-200 inline-flex items-center gap-2 active:scale-95 group h-[44px]">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-white group-hover:rotate-90 transition-transform duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <span>Nuevo Usuario</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-2xl shadow-xs flex items-center justify-between animate-fade-in">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0 font-extrabold">✓</div>
                        <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-2xl shadow-xs flex items-center justify-between animate-fade-in">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0 font-extrabold">!</div>
                        <p class="text-sm font-bold text-rose-900">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Barra de Búsqueda y Filtros --}}
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                    <div class="sm:col-span-6">
                        <label for="user-search" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Buscar por nombre o correo</label>
                        <div class="relative">
                            <input type="text" id="user-search" name="search" value="{{ request('search') }}" placeholder="Ej. Ana, ana@middleby.com..."
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition"/>
                            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="user-role" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Rol</label>
                        <select id="user-role" name="role" class="w-full py-2.5 px-3 bg-slate-50/70 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition">
                            <option value="">Todos los roles</option>
                            @foreach ([\App\Support\Roles::ADMIN, \App\Support\Roles::TECNICO, \App\Support\Roles::SOLO_LECTURA] as $roleOption)
                                <option value="{{ $roleOption }}" @selected(request('role') === $roleOption)>{{ $roleOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button type="submit" class="w-full py-2.5 px-4 bg-middleby-700 hover:bg-middleby-800 text-white font-black text-sm rounded-xl transition shadow-sm hover:shadow-md active:scale-95 text-center">
                            Buscar
                        </button>
                        @if(request()->anyFilled(['search', 'role']))
                            <a href="{{ route('users.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-extrabold text-sm" title="Limpiar Filtros" aria-label="Limpiar filtros">
                                ✕
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla de Usuarios --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
                @if($users->isEmpty())
                    <div class="p-16 text-center">
                        <h3 class="text-xl font-extrabold text-slate-800">No se encontraron usuarios</h3>
                        <p class="text-sm text-slate-500 max-w-md mx-auto mt-2 font-medium">Ajusta los filtros de búsqueda o crea el primer usuario.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-100 text-xs font-extrabold uppercase text-slate-500 tracking-wider">
                                    <th class="py-4 px-6">Usuario</th>
                                    <th class="py-4 px-6">Rol</th>
                                    <th class="py-4 px-6">Estado</th>
                                    <th class="py-4 px-6">2FA</th>
                                    <th class="py-4 px-6 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($users as $listedUser)
                                    <tr class="hover:bg-slate-50/60 transition duration-150">
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="font-extrabold text-slate-900 text-base">
                                                {{ $listedUser->name }}
                                                @if($listedUser->id === auth()->id())
                                                    <span class="ml-1 text-xs font-bold text-middleby-600">(tú)</span>
                                                @endif
                                            </div>
                                            <div class="text-xs font-semibold text-slate-500">{{ $listedUser->email }}</div>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                {{ $listedUser->roles->pluck('name')->join(', ') ?: 'Sin rol' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            @if($listedUser->active)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    ● Activo
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold bg-slate-100 text-slate-500 border border-slate-200">
                                                    ● Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            @if($listedUser->hasTwoFactorEnabled())
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">🔒 Activo</span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold bg-amber-50 text-amber-700 border border-amber-200">Pendiente</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <a href="{{ route('users.edit', $listedUser) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition font-bold text-xs inline-flex items-center gap-1">
                                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                <span>Editar</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($users->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                            {{ $users->links() }}
                        </div>
                    @endif
                @endif
            </div>

            {{-- Bitácora de accesos recientes --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">Últimos accesos (más recientes primero)</h3>
                </div>
                @if($recentAudits->isEmpty())
                    <p class="p-6 text-sm text-slate-500 font-medium">Aún no hay registros de acceso.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-100 text-xs font-extrabold uppercase text-slate-500 tracking-wider">
                                    <th class="py-3 px-6">Correo</th>
                                    <th class="py-3 px-6">Resultado</th>
                                    <th class="py-3 px-6">IP</th>
                                    <th class="py-3 px-6">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($recentAudits as $audit)
                                    <tr>
                                        <td class="py-3 px-6 font-semibold text-slate-700">{{ $audit->email }}</td>
                                        <td class="py-3 px-6">
                                            @if(str_starts_with((string) $audit->reason, 'two_factor_reset'))
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700" title="{{ $audit->reason }}">2FA restablecido por un admin</span>
                                            @elseif($audit->successful)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700">
                                                    Exitoso
                                                    @if($audit->reason === 'recovery_code_used') (código de respaldo) @endif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700">
                                                    Fallido
                                                    @if($audit->reason === 'inactive_account') (cuenta desactivada) @endif
                                                    @if($audit->reason === 'invalid_two_factor_code') (código 2FA incorrecto) @endif
                                                    @if($audit->reason === 'invalid_recovery_code') (código de respaldo incorrecto) @endif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 text-slate-500 font-mono text-xs">{{ $audit->ip_address ?? '—' }}</td>
                                        <td class="py-3 px-6 text-slate-500 text-xs">{{ $audit->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
