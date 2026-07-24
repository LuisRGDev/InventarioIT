<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('devices.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ $device->brand }} {{ $device->model }}
                    </h2>
                    <p class="text-sm text-gray-500 font-mono mt-0.5">S/N: {{ $device->serial_number }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('devices.edit', $device) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('devices.history', $device) }}"
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

            {{-- Alertas --}}
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

                    {{-- Info general --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-700">Información del equipo</h3>
                            @php
                                $badgeMap = [
                                    'green'  => 'bg-green-100 text-green-800',
                                    'blue'   => 'bg-blue-100 text-blue-800',
                                    'yellow' => 'bg-yellow-100 text-yellow-800',
                                    'gray'   => 'bg-gray-100 text-gray-800',
                                    'red'    => 'bg-red-100 text-red-800',
                                ];
                                $badge = $badgeMap[$device->status->color()] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                {{ $device->status->label() }}
                            </span>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-8">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $device->category?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Marca / Modelo</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $device->brand }} {{ $device->model }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Número de serie</dt>
                                <dd class="mt-1 text-sm font-mono text-gray-900">{{ $device->serial_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de PC (Hostname)</dt>
                                <dd class="mt-1 text-sm font-mono text-gray-900">{{ $device->computer_name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">MAC Ethernet (LAN)</dt>
                                <dd class="mt-1 text-sm font-mono text-gray-900">{{ $device->mac_address_ethernet ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">MAC WiFi (WLAN)</dt>
                                <dd class="mt-1 text-sm font-mono text-gray-900">{{ $device->mac_address_wifi ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de compra</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $device->purchase_date ? $device->purchase_date->translatedFormat('d \d\e F, Y') : '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Registro en sistema</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $device->created_at->translatedFormat('d \d\e F, Y') }}</dd>
                            </div>
                        </div>
                    </div>

                    {{-- Garantía --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-700">Garantía</h3>
                        </div>
                        <div class="p-6">
                            @if($device->warranty_expires_at)
                                @php
                                    $daysLeft = now()->diffInDays($device->warranty_expires_at, false);
                                @endphp
                                @if($daysLeft > 30)
                                    <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                                        <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-green-800">Garantía activa</p>
                                            <p class="text-xs text-green-600 mt-0.5">
                                                Vence el {{ $device->warranty_expires_at->translatedFormat('d \d\e F, Y') }} ({{ $daysLeft }} días restantes)
                                            </p>
                                        </div>
                                    </div>
                                @elseif($daysLeft > 0)
                                    <div class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                                        <svg class="w-6 h-6 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-amber-800">Garantía próxima a vencer</p>
                                            <p class="text-xs text-amber-600 mt-0.5">
                                                Vence el {{ $device->warranty_expires_at->translatedFormat('d \d\e F, Y') }} ({{ $daysLeft }} días restantes)
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                                        <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-red-800">Garantía vencida</p>
                                            <p class="text-xs text-red-600 mt-0.5">
                                                Venció el {{ $device->warranty_expires_at->translatedFormat('d \d\e F, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <p class="text-sm text-gray-400 italic">No se registró información de garantía para este equipo.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Notas --}}
                    @if($device->notes)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="font-semibold text-gray-700">Notas</h3>
                            </div>
                            <div class="p-6">
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $device->notes }}</p>
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
                            @if($device->currentAssignment && $device->currentAssignment->employee)
                                @php $assignment = $device->currentAssignment; @endphp
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
                                <div class="mt-4 space-y-2">
                                    <a href="{{ route('assignments.return', $device->id) }}"
                                       class="flex items-center justify-center gap-2 w-full px-3 py-2 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                        Registrar retorno
                                    </a>
                                    <a href="{{ route('assignments.replace', $assignment->employee_id) }}"
                                       class="flex items-center justify-center gap-2 w-full px-3 py-2 text-sm font-medium text-purple-700 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                        Reemplazar equipo
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <p class="text-sm text-gray-400">Sin asignación activa</p>
                                    @if($device->status->value === 'disponible')
                                        <a href="{{ route('assignments.assign') }}"
                                           class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Asignar equipo
                                        </a>
                                    @endif
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
                            <a href="{{ route('devices.edit', $device) }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar información
                            </a>
                            <a href="{{ route('devices.history', $device) }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Ver historial completo
                            </a>
                            @unless($device->currentAssignment)
                                <hr class="my-1 border-gray-100">
                                <form method="POST" action="{{ route('devices.destroy', $device) }}"
                                      onsubmit="return confirm('¿Seguro que deseas eliminar este equipo? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition group">
                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Eliminar equipo
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
