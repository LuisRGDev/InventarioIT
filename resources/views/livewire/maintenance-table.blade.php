<div>
    {{-- Tarjetas KPI de Mantenimiento --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">En Taller / Proceso</p>
                <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ $stats['in_progress'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 shadow-xs">
                <svg class="w-6 h-6 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Programados</p>
                <p class="text-2xl font-extrabold text-indigo-600 mt-1">{{ $stats['scheduled'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Resueltos / Completados</p>
                <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['completed'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Preventivos</p>
                <p class="text-2xl font-extrabold text-sky-600 mt-1">{{ $stats['preventives'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-50 flex items-center justify-center text-sky-600 shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
        </div>
    </div>

    {{-- Filtros y Búsqueda (Livewire) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 mb-6">
        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[240px]">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Buscar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Serie, marca, título, descripción..." class="w-full pl-9 border border-slate-300 rounded-xl px-3.5 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition">
                    </div>
                </div>
                <div class="min-w-[170px]">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Estatus</label>
                    <select wire:model.live="status" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition">
                        <option value="">Todos los Estados</option>
                        @foreach(\App\Enums\MaintenanceStatus::cases() as $st)
                            <option value="{{ $st->value }}">{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[170px]">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tipo Servicio</label>
                    <select wire:model.live="type" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition">
                        <option value="">Todos los Tipos</option>
                        @foreach(\App\Enums\MaintenanceType::cases() as $tp)
                            <option value="{{ $tp->value }}">{{ $tp->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-4 items-end">
                <div class="min-w-[170px]">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Desde</label>
                    <input type="date" wire:model.live="date_from" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition">
                </div>
                <div class="min-w-[170px]">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Hasta</label>
                    <input type="date" wire:model.live="date_to" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition">
                </div>
                
                <div class="flex gap-2.5">
                    <button type="button" wire:click="clearFilters" class="px-4 py-2 bg-slate-100 text-slate-600 hover:text-slate-800 text-sm font-bold rounded-xl hover:bg-slate-200 transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Limpiar
                    </button>
                    <div wire:loading class="px-4 py-2 text-sm text-indigo-600 font-bold flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Buscando...
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla Principal --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden relative">
        <div wire:loading.class="opacity-50" class="overflow-x-auto transition-opacity duration-200">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Servicio / Equipo</th>
                        <th wire:click="sortByField('type')" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100 group">
                            Tipo
                            @if($sortBy === 'type') <span class="text-middleby-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @else <span class="text-slate-300 opacity-0 group-hover:opacity-100">↕</span> @endif
                        </th>
                        <th wire:click="sortByField('status')" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100 group">
                            Estatus
                            @if($sortBy === 'status') <span class="text-middleby-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @else <span class="text-slate-300 opacity-0 group-hover:opacity-100">↕</span> @endif
                        </th>
                        <th wire:click="sortByField('title')" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100 group">
                            Asunto / Síntomas
                            @if($sortBy === 'title') <span class="text-middleby-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @else <span class="text-slate-300 opacity-0 group-hover:opacity-100">↕</span> @endif
                        </th>
                        <th wire:click="sortByField('started_at')" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100 group">
                            Fechas Clave
                            @if($sortBy === 'started_at') <span class="text-middleby-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @else <span class="text-slate-300 opacity-0 group-hover:opacity-100">↕</span> @endif
                        </th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse ($maintenances as $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->device)
                                    <a href="{{ route('devices.show', $item->device) }}" class="group block">
                                        <div class="text-sm font-extrabold text-middleby-900 group-hover:text-middleby-700 transition">
                                            {{ $item->device->brand }} {{ $item->device->model }}
                                        </div>
                                        <div class="text-xs text-slate-500 font-mono">
                                            SN: {{ $item->device->serial_number }}
                                        </div>
                                        @if($item->device->computer_name)
                                            <span class="inline-block px-2 py-0.5 text-[10px] font-semibold bg-slate-100 text-slate-600 rounded mt-1">
                                                🖥️ {{ $item->device->computer_name }}
                                            </span>
                                        @endif
                                    </a>
                                @else
                                    <span class="text-sm text-slate-400 italic">Equipo eliminado o N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg border {{ $item->type->badgeClasses() }}">
                                    {{ $item->type->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-extrabold rounded-full border {{ $item->status->badgeClasses() }}">
                                    {{ $item->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-800 line-clamp-1">
                                    {{ $item->title }}
                                </div>
                                <div class="text-xs text-slate-500 line-clamp-1 mt-0.5 max-w-xs">
                                    {{ $item->description ?? 'Sin notas iniciales' }}
                                </div>
                                @if($item->user)
                                    <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                                        <span>👤 Por: <strong>{{ $item->user->name }}</strong></span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                                @if($item->started_at)
                                    <div class="font-semibold text-slate-700">Iniciado: {{ $item->started_at->format('d/M/Y H:i') }}</div>
                                @elseif($item->scheduled_at)
                                    <div class="font-semibold text-indigo-700">Agendado: {{ $item->scheduled_at->format('d/M/Y') }}</div>
                                @endif
                                
                                @if($item->completed_at)
                                    <div class="text-emerald-700 font-bold mt-0.5">✓ Cerrado: {{ $item->completed_at->format('d/M/Y') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('maintenances.show', $item) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-middleby-50 hover:text-middleby-700 text-slate-700 font-bold rounded-lg transition inline-flex items-center gap-1">
                                    <span>Ver / Gestionar</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="mx-auto w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                                </div>
                                <p class="text-base font-bold text-slate-700">No se encontraron registros de mantenimiento</p>
                                <button wire:click="clearFilters" class="text-xs font-bold text-middleby-600 hover:underline mt-1">Limpiar filtros de búsqueda</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($maintenances->hasPages())
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200/60">
                {{ $maintenances->links() }}
            </div>
        @endif
    </div>
</div>
