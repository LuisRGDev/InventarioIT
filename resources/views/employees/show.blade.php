<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('employees.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $employee->name }}</h2>
                    <div class="flex items-center gap-2 mt-0.5">
                        @if($employee->employee_code)
                            <p class="text-sm text-gray-500 font-mono"># {{ $employee->employee_code }}</p>
                        @endif
                        @if($employee->domain_account)
                            <span class="text-gray-300">•</span>
                            <p class="text-sm text-gray-500 font-mono" title="Usuario Active Directory">
                                <svg class="w-3.5 h-3.5 inline-block mr-0.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                {{ $employee->domain_account }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('employees.edit', $employee) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('employees.history', $employee) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Historial
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Columna principal --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Tarjeta de perfil --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6 flex items-start gap-5">
                            {{-- Avatar grande --}}
                            @php
                                $initials = collect(explode(' ', $employee->name))
                                    ->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                                $badgeMap = [
                                    'green'  => 'bg-green-100 text-green-800',
                                    'yellow' => 'bg-yellow-100 text-yellow-800',
                                    'red'    => 'bg-red-100 text-red-800',
                                ];
                                $badge = $badgeMap[$employee->status->color()] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <div class="w-16 h-16 rounded-2xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-indigo-700 font-bold text-xl">{{ $initials }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $employee->name }}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                        {{ $employee->status->label() }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $employee->position }} — {{ $employee->department }}</p>
                                <div class="flex flex-wrap gap-4 mt-3">
                                    <a href="mailto:{{ $employee->email }}"
                                       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-indigo-600 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $employee->email }}
                                    </a>
                                    @if($employee->phone)
                                        <span class="inline-flex items-center gap-1.5 text-sm text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            {{ $employee->phone }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $computers = $employee->currentAssignments->filter(fn($a) => $a->device && $a->device->category->isComputer());
                        $smartphones = $employee->currentAssignments->filter(fn($a) => $a->device && $a->device->category->isSmartphone());
                        $others = $employee->currentAssignments->filter(fn($a) => $a->device && !$a->device->category->isComputer() && !$a->device->category->isSmartphone());
                    @endphp

                    {{-- Equipos de Cómputo --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Equipos de Cómputo
                            </h3>
                            <span class="text-xs font-medium text-gray-400">{{ $computers->count() }} equipo(s)</span>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse($computers as $assignment)
                                @include('employees.partials.assignment-row', ['assignment' => $assignment])
                            @empty
                                <div class="px-6 py-8 text-center text-sm text-gray-400">Sin equipos de cómputo asignados</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Celulares --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Celulares
                            </h3>
                            <span class="text-xs font-medium text-gray-400">{{ $smartphones->count() }} celular(es)</span>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse($smartphones as $assignment)
                                @include('employees.partials.assignment-row', ['assignment' => $assignment])
                            @empty
                                <div class="px-6 py-8 text-center text-sm text-gray-400">Sin celulares asignados</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Otros Periféricos --}}
                    @if($others->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-700">Otros / Periféricos</h3>
                            <span class="text-xs font-medium text-gray-400">{{ $others->count() }} equipo(s)</span>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($others as $assignment)
                                @include('employees.partials.assignment-row', ['assignment' => $assignment])
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Notas --}}
                    @if($employee->notes)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="font-semibold text-gray-700">Notas</h3>
                            </div>
                            <div class="p-6">
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $employee->notes }}</p>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Columna lateral --}}
                <div class="space-y-6">

                    {{-- Acciones rápidas --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-700">Acciones</h3>
                        </div>
                        <div class="p-4 space-y-1">
                            <a href="{{ route('assignments.assign', ['selectedEmployeeId' => $employee->id]) }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Asignar equipo
                            </a>
                            <a href="{{ route('assignments.assign-phone-line', ['selectedEmployeeId' => $employee->id]) }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Asignar línea celular
                            </a>
                            <a href="{{ route('assignments.assign-extension', ['selectedEmployeeId' => $employee->id]) }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Asignar extensión
                            </a>
                            <a href="{{ route('assignments.replace', $employee->id) }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 rounded-lg transition group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                Reemplazar equipo
                            </a>
                            <a href="{{ route('employees.edit', $employee) }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar información
                            </a>
                            <a href="{{ route('employees.history', $employee) }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Ver historial
                            </a>
                            @if($employee->currentAssignments->count() === 0)
                                <hr class="my-1 border-gray-100">
                                <form method="POST" action="{{ route('employees.destroy', $employee) }}"
                                      onsubmit="return confirm('¿Seguro que deseas eliminar a {{ addslashes($employee->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Eliminar empleado
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Resumen --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-700">Resumen</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $employee->department }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Puesto</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->position }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Equipos activos</dt>
                                <dd class="mt-1 text-2xl font-bold text-indigo-600">{{ $employee->currentAssignments->count() }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Registrado desde</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->created_at->translatedFormat('d \d\e F, Y') }}</dd>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
