<div class="space-y-6">
    {{-- Tarjetas de Métricas (KPIs) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Total Equipos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Equipos</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $this->metrics['total'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        {{-- Equipos Asignados --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Asignados</p>
                <p class="text-3xl font-bold text-green-600 mt-2">{{ $this->metrics['assigned'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>

        {{-- Equipos Disponibles --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Disponibles en Stock</p>
                <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $this->metrics['available'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
        </div>

        {{-- Equipos En Reparación / Obsoletos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Reparación / Baja</p>
                <p class="text-3xl font-bold text-orange-600 mt-2">{{ $this->metrics['inRepair'] + $this->metrics['obsolete'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Últimas Asignaciones --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Últimas Asignaciones Activas</h3>
                <a href="{{ route('assignments.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Ver todas &rarr;</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->recentAssignments as $assignment)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            @if($assignment->device->category->isComputer())
                                <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $assignment->device->brand }} {{ $assignment->device->model }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">Asignado a: <span class="font-medium">{{ $assignment->employee->name }}</span></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-900">{{ $assignment->assigned_at->diffForHumans() }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Por: {{ $assignment->assignedBy->name }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 text-sm">
                        No hay asignaciones recientes.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Garantías por Vencer --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Garantías por vencer</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    &lt; 30 días
                </span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->expiringWarranties as $device)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between mb-1">
                            <a href="{{ route('devices.show', $device) }}" class="text-sm font-medium text-indigo-600 hover:underline">
                                {{ $device->serial_number }}
                            </a>
                            <span class="text-xs font-medium text-red-600">
                                Expira {{ $device->warranty_expires_at->format('d M y') }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500">{{ $device->brand }} {{ $device->model }}</p>
                        @if($device->currentAssignment)
                            <p class="text-xs text-gray-400 mt-1">En uso por: {{ $device->currentAssignment->employee->name }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Disponible en stock</p>
                        @endif
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 text-sm">
                        No hay equipos con garantía próxima a vencer.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
