<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-middleby-900 leading-tight tracking-tight flex items-center gap-2.5">
                <span class="w-2 h-6 bg-gradient-to-b from-amber-500 to-middleby-700 rounded-full inline-block shadow-sm"></span>
                Historial de Asignaciones
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('assignments.assign') }}" class="px-4 py-2 bg-gradient-to-r from-middleby-800 to-middleby-700 text-white text-sm font-bold rounded-xl hover:from-middleby-700 hover:to-middleby-600 transition shadow-sm hover:shadow-md inline-flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Asignación
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                
                {{-- Filtros --}}
                <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <form method="GET" action="{{ route('assignments.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                        <select name="status" onchange="this.form.submit()" class="border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 min-w-[200px]">
                            <option value="">Todas las asignaciones</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Solo Activas</option>
                            <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Hist├│ricas (Retornadas)</option>
                        </select>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">Equipo</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Empleado</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Asignaci├│n</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Retorno</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($assignments as $assignment)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($assignment->device?->category?->isComputer())
                                                <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @elseif($assignment->device?->category?->isSmartphone())
                                                <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    @if($assignment->device)
                                                    <a href="{{ route('devices.show', $assignment->device) }}" class="hover:text-indigo-600 hover:underline">
                                                        {{ $assignment->device->brand }} {{ $assignment->device->model }}
                                                    </a>
                                                    @else
                                                    <span class="text-gray-400 italic">Equipo eliminado</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500 font-mono mt-0.5">SN: {{ $assignment->device?->serial_number ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">
                                            @if($assignment->employee)
                                            <a href="{{ route('employees.show', $assignment->employee) }}" class="hover:text-indigo-600 hover:underline">
                                                {{ $assignment->employee->name }}
                                            </a>
                                            @else
                                                <div class="text-sm text-gray-500">Empleado eliminado</div>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $assignment->employee?->department ?? "" }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-gray-900">{{ $assignment->assigned_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">Por: {{ $assignment->assignedBy->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($assignment->returned_at)
                                            <div class="text-gray-900">{{ $assignment->returned_at->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Por: {{ $assignment->returnedBy?->name ?? 'N/A' }}</div>
                                        @else
                                            <span class="text-gray-400 italic text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($assignment->returned_at)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                                Retornado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                                Activo
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        No se encontraron asignaciones que coincidan con los filtros.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assignments->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-white">
                        {{ $assignments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>


