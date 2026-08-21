<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('phone-lines.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Línea: {{ $phoneLine->number }}
                    </h2>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('phone-lines.edit', $phoneLine) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('phone-lines.history', $phoneLine) }}"
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
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-700">Información de la Línea</h3>
                            @php
                                $badgeClasses = match($phoneLine->status->value) {
                                    'disponible' => 'bg-green-100 text-green-800',
                                    'asignada' => 'bg-blue-100 text-blue-800',
                                    'baja' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                {{ $phoneLine->status->label() }}
                            </span>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-8">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Número Telefónico</dt>
                                <dd class="mt-1 text-sm font-mono text-gray-900 font-bold">{{ $phoneLine->number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Plan de Datos</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $phoneLine->data_plan ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Costo del Plan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $phoneLine->plan_cost ? '$' . number_format($phoneLine->plan_cost, 2) : '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Registro en sistema</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $phoneLine->created_at->translatedFormat('d \d\e F, Y') }}</dd>
                            </div>
                        </div>
                    </div>

                    {{-- Notas --}}
                    @if($phoneLine->notes)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="font-semibold text-gray-700">Notas</h3>
                            </div>
                            <div class="p-6">
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $phoneLine->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Columna lateral --}}
                <div class="space-y-6">
                    {{-- Asignación actual --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-700">Asignación actual</h3>
                        </div>
                        <div class="p-6">
                            @if($phoneLine->currentAssignment && $phoneLine->currentAssignment->employee)
                                @php $assignment = $phoneLine->currentAssignment; @endphp
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-indigo-700 font-semibold text-sm">
                                            {{ strtoupper(substr($assignment->employee->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $assignment->employee->name }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $assignment->employee->department ?? '—' }}</p>
                                        <p class="text-xs text-gray-400 mt-2">
                                            Asignado el {{ $assignment->assigned_at->translatedFormat('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <p class="text-sm text-gray-400">Sin asignación activa</p>
                                    <p class="text-xs text-gray-500 mt-2 italic text-center px-4">
                                        Nota: Las líneas telefónicas se asignan desde el panel global de asignaciones o al editar el empleado.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Acciones rápidas --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-700">Acciones</h3>
                        </div>
                        <div class="p-4 space-y-2">
                            <a href="{{ route('phone-lines.edit', $phoneLine) }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar información
                            </a>
                            <a href="{{ route('phone-lines.history', $phoneLine) }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Ver historial completo
                            </a>
                            @unless($phoneLine->currentAssignment)
                                <hr class="my-1 border-gray-100">
                                <a href="{{ route('assignments.assign-phone-line', ['selectedPhoneId' => $phoneLine->id]) }}"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-indigo-700 hover:bg-indigo-50 rounded-lg transition group">
                                    <svg class="w-4 h-4 text-indigo-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Asignar línea
                                </a>
                                <form method="POST" action="{{ route('phone-lines.destroy', $phoneLine) }}"
                                      onsubmit="return confirm('¿Seguro que deseas eliminar esta línea telefónica? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition group">
                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Eliminar línea
                                    </button>
                                </form>
                            @endunless
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
