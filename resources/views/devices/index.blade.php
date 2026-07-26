<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-middleby-900 leading-tight tracking-tight flex items-center gap-2.5">
                <span class="w-2 h-6 bg-gradient-to-b from-amber-500 to-middleby-700 rounded-full inline-block shadow-sm"></span>
                Equipos de Cómputo e Infraestructura
            </h2>
            <div class="flex flex-wrap gap-3" x-data="{ showImportModal: false }">
                <button type="button" @click="showImportModal = true" class="px-4 py-2 bg-white text-emerald-700 border border-emerald-200 text-sm font-semibold rounded-xl hover:bg-emerald-50 transition shadow-xs inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Importar
                </button>
                <a href="{{ route('devices.export') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm hover:shadow-md inline-flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Exportar
                </a>
                <a href="{{ route('devices.create') }}" class="px-4 py-2 bg-gradient-to-r from-middleby-800 to-middleby-700 text-white text-sm font-bold rounded-xl hover:from-middleby-700 hover:to-middleby-600 transition shadow-sm hover:shadow-md inline-flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Equipo
                </a>

                {{-- Modal Importación --}}
                <div x-show="showImportModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div x-show="showImportModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div x-show="showImportModal" 
                             x-transition:enter="ease-out duration-300" 
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                             x-transition:leave="ease-in duration-200" 
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                             @click.away="showImportModal = false"
                             class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                            
                            <form action="{{ route('devices.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10 text-emerald-600">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Importar Equipos</h3>
                                            <div class="mt-2 text-sm text-gray-500">
                                                <p class="mb-3">Para que el sistema procese correctamente tus equipos, deben cumplir con un formato específico.</p>
                                                <a href="{{ route('devices.import.template') }}" class="text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                    Descargar Plantilla de Ejemplo
                                                </a>
                                                
                                                <div class="mt-5 border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:bg-gray-50 transition">
                                                    <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 sm:ml-3 sm:w-auto sm:text-sm">
                                        Subir e Importar
                                    </button>
                                    <button type="button" @click="showImportModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Alertas de sesión --}}
            @if (session('success'))
                <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Resumen por estatus --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                @foreach(\App\Enums\DeviceStatus::cases() as $statusItem)
                    @php
                        $count = $statsByStatus[$statusItem->value] ?? 0;
                        $colorMap = [
                            'green'  => 'bg-emerald-50/80 border-emerald-200 text-emerald-800',
                            'blue'   => 'bg-middleby-50/80 border-middleby-200 text-middleby-800',
                            'yellow' => 'bg-amber-50/80 border-amber-200 text-amber-800',
                            'gray'   => 'bg-slate-50/80 border-slate-200 text-slate-800',
                            'red'    => 'bg-red-50/80 border-red-200 text-red-800',
                        ];
                        $colorClass = $colorMap[$statusItem->color()] ?? 'bg-slate-50 border-slate-200 text-slate-800';
                    @endphp
                    <div class="border rounded-2xl p-4 text-center shadow-xs card-hover {{ $colorClass }}">
                        <p class="text-3xl font-extrabold tracking-tight">{{ $count }}</p>
                        <p class="text-xs font-bold uppercase tracking-wider mt-1.5 opacity-90">{{ $statusItem->label() }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Filtros --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <form method="GET" action="{{ route('devices.index') }}" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Serial, marca, modelo…"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div class="min-w-[150px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Categoría</label>
                        <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="">Todas</option>
                            @foreach(\App\Models\DeviceCategory::orderBy('name')->get() as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[140px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Estatus</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="">Todos</option>
                            @foreach(\App\Enums\DeviceStatus::cases() as $s)
                                <option value="{{ $s->value }}" {{ request('status') == $s->value ? 'selected' : '' }}>
                                    {{ $s->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                                class="px-5 py-2 bg-middleby-800 text-white text-sm font-semibold rounded-xl hover:bg-middleby-700 transition shadow-sm">
                            Filtrar
                        </button>
                        @if(request()->hasAny(['search','category','status']))
                            <a href="{{ route('devices.index') }}"
                               class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Equipo</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Serial</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estatus</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Asignaciones</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($devices as $device)
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
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $device->brand }} {{ $device->model }}</div>
                                        @if($device->mac_address_ethernet)
                                            <div class="text-xs text-gray-400 mt-0.5 font-mono">
                                                <span class="text-gray-300">ETH</span> {{ $device->mac_address_ethernet }}
                                            </div>
                                        @endif
                                        @if($device->mac_address_wifi)
                                            <div class="text-xs text-gray-400 mt-0.5 font-mono">
                                                <span class="text-gray-300">WiFi</span> {{ $device->mac_address_wifi }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $device->category?->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-sm text-gray-700">{{ $device->serial_number }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                            {{ $device->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $device->assignments_count }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('devices.show', $device) }}"
                                               class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                               title="Ver detalles">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('devices.history', $device) }}"
                                               class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition"
                                               title="Historial">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('devices.edit', $device) }}"
                                               class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                               title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('devices.destroy', $device) }}"
                                                  onsubmit="return confirm('¿Seguro que deseas eliminar este equipo?')"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                        title="Eliminar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3 text-gray-400">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="text-sm font-medium">No se encontraron equipos</p>
                                            <a href="{{ route('devices.create') }}" class="text-sm text-indigo-600 hover:underline">Registrar el primero</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($devices->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $devices->withQueryString()->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
