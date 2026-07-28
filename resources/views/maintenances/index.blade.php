<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-bold text-xl text-middleby-900 leading-tight tracking-tight flex items-center gap-2.5">
                <span class="w-2 h-6 bg-gradient-to-b from-amber-500 to-middleby-700 rounded-full inline-block shadow-sm"></span>
                {{ __('Control y Bitácora de Mantenimientos IT') }}
            </h2>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('maintenances.export') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-sm hover:shadow-md transition inline-flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Exportar Excel</span>
                </a>
                <a href="{{ route('maintenances.create') }}" class="px-4 py-2 bg-gradient-to-r from-middleby-800 to-middleby-700 text-white text-sm font-bold rounded-xl hover:from-middleby-700 hover:to-middleby-600 transition shadow-sm hover:shadow-md inline-flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Registrar Mantenimiento</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-xs flex items-center justify-between animate-fade-in">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- Tarjetas KPI de Mantenimiento --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- En Taller / Proceso --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">En Taller / Proceso</p>
                        <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ $stats['in_progress'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 shadow-xs">
                        <svg class="w-6 h-6 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                </div>

                {{-- Programados --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Programados</p>
                        <p class="text-2xl font-extrabold text-indigo-600 mt-1">{{ $stats['scheduled'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>

                {{-- Completados --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Resueltos / Completados</p>
                        <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['completed'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                {{-- Preventivos Registrados --}}
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

            {{-- Filtros y Búsqueda --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
                <form method="GET" action="{{ route('maintenances.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[240px]">
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Serie, marca, título, descripción..."
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition">
                    </div>
                    <div class="min-w-[170px]">
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Estatus</label>
                        <select name="status" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition">
                            <option value="">Todos los Estados</option>
                            @foreach(\App\Enums\MaintenanceStatus::cases() as $st)
                                <option value="{{ $st->value }}" {{ request('status') == $st->value ? 'selected' : '' }}>
                                    {{ $st->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[170px]">
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tipo Servicio</label>
                        <select name="type" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition">
                            <option value="">Todos los Tipos</option>
                            @foreach(\App\Enums\MaintenanceType::cases() as $tp)
                                <option value="{{ $tp->value }}" {{ request('type') == $tp->value ? 'selected' : '' }}>
                                    {{ $tp->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2.5">
                        <button type="submit" class="px-5 py-2 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-700 transition shadow-sm inline-flex items-center gap-1.5 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Filtrar
                        </button>
                        @if(request()->hasAny(['search','status','type']))
                            <a href="{{ route('maintenances.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 hover:text-slate-800 text-sm font-bold rounded-xl hover:bg-slate-200 transition">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla Principal --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Servicio / Equipo</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Estatus</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Asunto / Síntomas</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fechas Clave</th>
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
                                            <span class="text-sm text-slate-400 font-italic">Equipo eliminado o N/A</span>
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
                                        <p class="text-xs text-slate-400 mt-1">Registra la primera intervención o limpieza de una computadora con el botón superior.</p>
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
    </div>
</x-app-layout>
