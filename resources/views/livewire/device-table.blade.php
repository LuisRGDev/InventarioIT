<div>
    {{-- Filtros Avanzados (Livewire) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Buscar Equipo</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Serial, marca, modelo..." class="w-full pl-9 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>
                
                <div class="min-w-[180px]" x-data="{ open: false }" @click.outside="open = false">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Categoría</label>
                    <div class="relative">
                        <button type="button" @click="open = !open" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-left bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 flex items-center justify-between">
                            <span>
                                @if(empty($category_ids))
                                    Todas
                                @else
                                    {{ count($category_ids) }} seleccionada{{ count($category_ids) > 1 ? 's' : '' }}
                                @endif
                            </span>
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg py-1 max-h-56 overflow-y-auto">
                            @foreach($categories as $cat)
                                <label class="flex items-center gap-2 px-3 py-1.5 text-sm hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" wire:model.live="category_ids" value="{{ $cat->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    {{ $cat->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Modelo</label>
                    <select wire:model.live="model_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Todos</option>
                        @foreach($models as $m)
                            <option value="{{ $m->id }}">{{ $m->brand }} - {{ $m->model }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="min-w-[140px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Estatus</label>
                    <select wire:model.live="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Todos</option>
                        @foreach(\App\Enums\DeviceStatus::cases() as $s)
                            <option value="{{ $s->value }}">{{ $s->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[140px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Condición</label>
                    <select wire:model.live="condition" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Todas</option>
                        @foreach(\App\Enums\DeviceCondition::cases() as $c)
                            <option value="{{ $c->value }}">{{ $c->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            {{-- Fechas --}}
            <div class="flex flex-wrap gap-3 items-end">
                <div class="min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Registrado Desde</label>
                    <input type="date" wire:model.live="date_from" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div class="min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Registrado Hasta</label>
                    <input type="date" wire:model.live="date_to" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                
                <div class="flex gap-2">
                    <button type="button" wire:click="clearFilters" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Limpiar Filtros
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
    </div>

    {{-- Resumen por estatus (Opcional, si queremos que sea reactivo) --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
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

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
        <div wire:loading.class="opacity-50" class="overflow-x-auto transition-opacity duration-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortByField('brand')" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 group">
                            Equipo 
                            @if($sortBy === 'brand') 
                                <span class="text-indigo-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @else
                                <span class="text-gray-300 opacity-0 group-hover:opacity-100">↕</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th wire:click="sortByField('serial_number')" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 group">
                            Serial
                            @if($sortBy === 'serial_number') 
                                <span class="text-indigo-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @else
                                <span class="text-gray-300 opacity-0 group-hover:opacity-100">↕</span>
                            @endif
                        </th>
                        <th wire:click="sortByField('status')" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 group">
                            Estatus
                            @if($sortBy === 'status') 
                                <span class="text-indigo-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @else
                                <span class="text-gray-300 opacity-0 group-hover:opacity-100">↕</span>
                            @endif
                        </th>
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
                                    <div class="text-xs text-gray-400 mt-0.5 font-mono"><span class="text-gray-300">ETH</span> {{ $device->mac_address_ethernet }}</div>
                                @endif
                                @if($device->mac_address_wifi)
                                    <div class="text-xs text-gray-400 mt-0.5 font-mono"><span class="text-gray-300">WiFi</span> {{ $device->mac_address_wifi }}</div>
                                @endif
                                @if($device->phone_number)
                                    <div class="text-xs text-gray-400 mt-0.5 font-mono"><span class="text-gray-300">Tel</span> {{ $device->phone_number }}</div>
                                @endif
                                @if($device->imei)
                                    <div class="text-xs text-gray-400 mt-0.5 font-mono"><span class="text-gray-300">IMEI</span> {{ $device->imei }}</div>
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
                                    <a href="{{ route('devices.show', $device) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Ver detalles">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('devices.history', $device) }}" class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="Historial">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </a>
                                    <a href="{{ route('devices.edit', $device) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Editar">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm font-medium">No se encontraron equipos con los filtros actuales</p>
                                    <button wire:click="clearFilters" class="text-sm text-indigo-600 hover:underline">Limpiar Filtros</button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($devices->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $devices->links() }}
            </div>
        @endif
    </div>
</div>
