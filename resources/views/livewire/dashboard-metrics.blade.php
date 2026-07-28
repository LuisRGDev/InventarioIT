<div class="space-y-8">
    {{-- Tarjetas de Métricas (KPIs) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Total Equipos --}}
        <div class="bg-gradient-to-br from-middleby-900 via-middleby-800 to-middleby-700 rounded-2xl shadow-premium p-6 text-white card-hover border border-middleby-600/50 relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-middleby-200">Total Equipos</p>
                    <p class="text-4xl font-extrabold text-white mt-2 tracking-tight">{{ $this->metrics['total'] }}</p>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/15 flex items-center justify-center text-amber-400 p-3.5 shadow-sm group-hover:rotate-12 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Equipos Asignados --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 card-hover group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Asignados (En Uso)</p>
                    <p class="text-4xl font-extrabold text-emerald-600 mt-2 tracking-tight">{{ $this->metrics['assigned'] }}</p>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 p-3.5 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Equipos Disponibles --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 card-hover group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Disponibles en Stock</p>
                    <p class="text-4xl font-extrabold text-middleby-700 mt-2 tracking-tight">{{ $this->metrics['available'] }}</p>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-middleby-50 border border-middleby-100 flex items-center justify-center text-middleby-700 p-3.5 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Equipos En Reparación / Obsoletos --}}
        <a href="{{ route('maintenances.index') }}" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 card-hover group block hover:border-amber-300 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Reparación / Taller</p>
                    <p class="text-4xl font-extrabold text-amber-600 mt-2 tracking-tight">{{ $this->metrics['inRepair'] }} <span class="text-sm text-slate-400 font-medium">/ {{ $this->metrics['maintenances'] }} activos</span></p>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 p-3.5 group-hover:scale-110 group-hover:bg-amber-100 transition duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
        </a>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Últimas Asignaciones --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-middleby-600 inline-block"></span>
                    <h3 class="font-bold text-slate-800">Últimas Asignaciones Activas</h3>
                </div>
                <a href="{{ route('assignments.index') }}" class="text-xs font-bold uppercase tracking-wider text-middleby-700 hover:text-middleby-900 transition-colors bg-middleby-50 px-3 py-1 rounded-lg border border-middleby-100">Ver todas &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($this->recentAssignments as $assignment)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50/80 transition-colors">
                        <div class="flex items-center gap-4">
                            @if($assignment->device->category->isComputer())
                                <div class="w-11 h-11 rounded-xl bg-middleby-50 border border-middleby-100 flex items-center justify-center text-middleby-700 flex-shrink-0 shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </div>
                            @else
                                <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0 shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $assignment->device->brand }} {{ $assignment->device->model }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">Asignado a: <span class="font-bold text-middleby-800 bg-middleby-50 px-2 py-0.5 rounded">{{ $assignment->employee->name }}</span></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-block text-xs font-semibold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-full">{{ $assignment->assigned_at->diffForHumans() }}</span>
                            <p class="text-[11px] text-slate-400 mt-1">Por: {{ $assignment->assignedBy->name }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <p class="text-slate-500 font-medium text-sm">No hay asignaciones recientes registradas.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Garantías por Vencer --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                        <h3 class="font-bold text-slate-800">Garantías por Vencer</h3>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-red-50 text-red-700 border border-red-200">
                        &lt; 30 días
                    </span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($this->expiringWarranties as $device)
                        <div class="px-6 py-4 hover:bg-slate-50/80 transition-colors">
                            <div class="flex items-center justify-between mb-1">
                                <a href="{{ route('devices.show', $device) }}" class="text-sm font-bold text-middleby-700 hover:text-middleby-900 transition-colors">
                                    {{ $device->serial_number }}
                                </a>
                                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                    Expira {{ $device->warranty_expires_at->format('d M y') }}
                                </span>
                            </div>
                            <p class="text-xs font-semibold text-slate-600">{{ $device->brand }} {{ $device->model }}</p>
                            @if($device->currentAssignment)
                                <p class="text-[11px] text-slate-400 mt-1">En uso por: <span class="text-slate-600">{{ $device->currentAssignment->employee->name }}</span></p>
                            @else
                                <span class="inline-block text-[10px] uppercase font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded mt-1">Disponible en stock</span>
                            @endif
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-3 text-emerald-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-slate-500 font-medium text-sm">Todas las garantías están al corriente.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- Sección de Mantenimientos Activos / En Taller --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🛠️</span>
                <div>
                    <h3 class="font-bold text-base">Equipos Actualmente en Taller y Mantenimiento Activo</h3>
                    <p class="text-xs text-slate-300 font-normal">Supervisa intervenciones en curso, reparaciones correctivas y mantenimientos programados.</p>
                </div>
            </div>
            <a href="{{ route('maintenances.index') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl uppercase tracking-wider transition shadow-sm active:scale-95">
                Ir a Mantenimientos &rarr;
            </a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($this->activeMaintenances as $mant)
                <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/80 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-700 font-bold flex-shrink-0">
                            {{ $mant->type->icon() }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded border {{ $mant->type->badgeClasses() }}">{{ $mant->type->label() }}</span>
                                <span class="px-2 py-0.5 text-[11px] font-extrabold rounded-full border {{ $mant->status->badgeClasses() }}">{{ $mant->status->label() }}</span>
                            </div>
                            <p class="text-sm font-extrabold text-slate-900 mt-1">
                                {{ $mant->device ? "{$mant->device->brand} {$mant->device->model} (SN: {$mant->device->serial_number})" : 'Equipo eliminado' }}
                            </p>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $mant->title }}</p>
                        </div>
                    </div>
                    <div class="sm:text-right flex items-center justify-between sm:flex-col gap-2">
                        <span class="text-xs font-bold text-slate-600">Apertura: {{ $mant->created_at->format('d/M/Y') }}</span>
                        <a href="{{ route('maintenances.show', $mant) }}" class="text-xs font-extrabold text-middleby-700 hover:text-middleby-900 underline">
                            Ver o concluir ticket &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="px-6 py-10 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mx-auto mb-3 text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-slate-700 font-bold text-sm">No hay equipos abiertos en taller o con mantenimientos pendientes.</p>
                    <p class="text-xs text-slate-400 mt-0.5">Puedes registrar una nueva rutina técnica o reparación programada en cualquier momento.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
