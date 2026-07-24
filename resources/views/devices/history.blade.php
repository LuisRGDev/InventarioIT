<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('devices.show', $device) }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Historial de Asignaciones</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $device->brand }} {{ $device->model }} — <span class="font-mono">{{ $device->serial_number }}</span></p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Resumen del equipo --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-wrap items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $device->brand }} {{ $device->model }}</p>
                        <p class="text-xs font-mono text-gray-400 mt-0.5">{{ $device->serial_number }}</p>
                    </div>
                </div>
                <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $assignments->total() }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Asignaciones totales</p>
                </div>
                <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $assignments->where('returned_at', null)->count() }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Activas</p>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('devices.show', $device) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">
                        Ver detalle del equipo
                    </a>
                </div>
            </div>

            {{-- Tabla de historial --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-700">Registro de asignaciones</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Empleado</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Asignado el</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Retornado el</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Condición entrega</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Condición retorno</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registró</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($assignments as $assignment)
                                @php
                                    $condBadgeMap = [
                                        'blue'  => 'bg-blue-100 text-blue-800',
                                        'green' => 'bg-green-100 text-green-800',
                                        'red'   => 'bg-red-100 text-red-800',
                                        'gray'  => 'bg-gray-100 text-gray-800',
                                    ];
                                    $deliveryCond = $assignment->condition_on_delivery
                                        ? \App\Enums\DeviceCondition::tryFrom($assignment->condition_on_delivery)
                                        : null;
                                    $returnCond = $assignment->condition_on_return
                                        ? \App\Enums\DeviceCondition::tryFrom($assignment->condition_on_return)
                                        : null;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        @if($assignment->employee)
                                            <a href="{{ route('employees.show', $assignment->employee) }}"
                                               class="font-medium text-indigo-600 hover:text-indigo-800 text-sm">
                                                {{ $assignment->employee->name }}
                                            </a>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $assignment->employee->department ?? '' }}</p>
                                        @else
                                            <span class="text-gray-400 text-sm italic">Empleado eliminado</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $assignment->assigned_at->translatedFormat('d M Y') }}
                                        <p class="text-xs text-gray-400">{{ $assignment->assigned_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        @if($assignment->returned_at)
                                            {{ $assignment->returned_at->translatedFormat('d M Y') }}
                                            <p class="text-xs text-gray-400">{{ $assignment->returned_at->diffForHumans() }}</p>
                                        @else
                                            <span class="text-blue-600 text-xs font-medium">En uso</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($deliveryCond)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $condBadgeMap[$deliveryCond->color()] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $deliveryCond->label() }}
                                            </span>
                                        @else
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($returnCond)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $condBadgeMap[$returnCond->color()] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $returnCond->label() }}
                                            </span>
                                        @else
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $assignment->assignedBy?->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($assignment->returned_at)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                Retornado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Activo
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3 text-gray-400">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="text-sm font-medium">Este equipo no tiene historial de asignaciones</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assignments->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $assignments->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
