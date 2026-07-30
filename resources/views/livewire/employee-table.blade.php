<div>
    {{-- Filtros Avanzados (Livewire) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Buscar Empleado</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nombre, correo, clave..." class="w-full pl-9 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            
            <div class="min-w-[150px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Departamento</label>
                <input type="text" wire:model.live.debounce.300ms="department" placeholder="Ej. TI, RRHH..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            
            <div class="min-w-[140px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Estatus</label>
                <select wire:model.live="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Todos</option>
                    @foreach(\App\Enums\EmployeeStatus::cases() as $s)
                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="button" wire:click="clearFilters" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Limpiar
                </button>
                <div wire:loading class="px-4 py-2 text-sm text-indigo-600 font-medium flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Actualizando...
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
        <div wire:loading.class="opacity-50" class="overflow-x-auto transition-opacity duration-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortByField('name')" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 group">
                            Empleado
                            @if($sortBy === 'name') <span class="text-indigo-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @else <span class="text-gray-300 opacity-0 group-hover:opacity-100">↕</span> @endif
                        </th>
                        <th wire:click="sortByField('department')" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 group">
                            Departamento / Puesto
                            @if($sortBy === 'department') <span class="text-indigo-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @else <span class="text-gray-300 opacity-0 group-hover:opacity-100">↕</span> @endif
                        </th>
                        <th wire:click="sortByField('email')" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 group">
                            Contacto
                            @if($sortBy === 'email') <span class="text-indigo-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @else <span class="text-gray-300 opacity-0 group-hover:opacity-100">↕</span> @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Equipos asignados</th>
                        <th wire:click="sortByField('status')" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 group">
                            Estatus
                            @if($sortBy === 'status') <span class="text-indigo-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @else <span class="text-gray-300 opacity-0 group-hover:opacity-100">↕</span> @endif
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($employees as $employee)
                        @php
                            $badgeMap = [
                                'green'  => 'bg-green-100 text-green-800',
                                'yellow' => 'bg-yellow-100 text-yellow-800',
                                'red'    => 'bg-red-100 text-red-800',
                            ];
                            $badge = $badgeMap[$employee->status->color()] ?? 'bg-gray-100 text-gray-800';
                            $initials = collect(explode(' ', $employee->name))
                                ->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->implode('');
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-indigo-700 font-semibold text-xs">{{ $initials }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('employees.show', $employee) }}" class="font-medium text-gray-900 hover:text-indigo-600 transition">
                                            {{ $employee->name }}
                                        </a>
                                        @if($employee->employee_code)
                                            <p class="text-xs text-gray-400 font-mono mt-0.5"># {{ $employee->employee_code }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $employee->department }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $employee->position }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700">{{ $employee->email }}</p>
                                @if($employee->phone)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $employee->phone }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold {{ $employee->current_assignments_count > 0 ? 'text-indigo-700' : 'text-gray-400' }}">
                                    {{ $employee->current_assignments_count }}
                                </span>
                                <span class="text-xs text-gray-400 ml-1">equipo{{ $employee->current_assignments_count != 1 ? 's' : '' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                    {{ $employee->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('employees.show', $employee) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Ver detalles">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('employees.history', $employee) }}" class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="Historial">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </a>
                                    <a href="{{ route('employees.edit', $employee) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-sm font-medium">No se encontraron empleados</p>
                                    <button wire:click="clearFilters" class="text-sm text-indigo-600 hover:underline">Limpiar Filtros</button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
