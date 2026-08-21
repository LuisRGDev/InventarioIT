<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-middleby-900 leading-tight tracking-tight flex items-center gap-2.5">
                <span class="w-2 h-6 bg-gradient-to-b from-amber-500 to-middleby-700 rounded-full inline-block shadow-sm"></span>
                {{ __('Detalle de Bitácora y Taller') }} · #MANT-{{ $maintenance->id }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('maintenances.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver a Bitácora
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
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

            {{-- Banner superior de estado --}}
            <div class="rounded-2xl p-6 border shadow-sm {{ $maintenance->status === \App\Enums\MaintenanceStatus::EnProceso ? 'bg-gradient-to-r from-amber-50 to-orange-50 border-amber-200 text-amber-900' : ($maintenance->status === \App\Enums\MaintenanceStatus::Completado ? 'bg-gradient-to-r from-emerald-50 to-teal-50 border-emerald-200 text-emerald-900' : 'bg-slate-100 border-slate-200 text-slate-700') }}">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="text-4xl">
                            @if($maintenance->status === \App\Enums\MaintenanceStatus::EnProceso) 🔧
                            @elseif($maintenance->status === \App\Enums\MaintenanceStatus::Completado) ✅
                            @else 📅 @endif
                        </div>
                        <div>
                            <span class="inline-block px-3 py-1 text-xs font-extrabold rounded-full border bg-white shadow-xs {{ $maintenance->status->badgeClasses() }}">
                                Estado: {{ $maintenance->status->label() }}
                            </span>
                            <h3 class="text-2xl font-black mt-1">{{ $maintenance->title }}</h3>
                            <p class="text-xs opacity-80 font-medium">Tipo: <strong>{{ $maintenance->type->label() }}</strong> · Apertura: {{ $maintenance->created_at->format('d/M/Y H:i') }}</p>
                        </div>
                    </div>
                    @if($maintenance->completed_at)
                        <div class="sm:text-right bg-white/80 p-3 rounded-xl border border-emerald-200/60 shadow-xs">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Fecha Conclusa</p>
                            <p class="text-base font-extrabold text-emerald-700">{{ $maintenance->completed_at->format('d de M, Y · H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Columna izquierda: Datos del Equipo --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-3">
                            🖥️ Información del Dispositivo
                        </h4>
                        @if($maintenance->device)
                            <div class="space-y-3">
                                <div>
                                    <span class="text-xs text-slate-400 block">Marca y Modelo</span>
                                    <span class="text-lg font-extrabold text-slate-800">{{ $maintenance->device->brand }} {{ $maintenance->device->model }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-xs text-slate-400 block">Número de Serie</span>
                                        <span class="font-mono font-bold text-middleby-800">{{ $maintenance->device->serial_number }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 block">Estatus Inventario</span>
                                        <span class="font-bold text-sm text-slate-700">{{ $maintenance->device->status->label() }}</span>
                                    </div>
                                </div>
                                @if($maintenance->device->computer_name)
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60">
                                        <span class="text-[11px] text-slate-400 uppercase font-bold block">Nombre de Equipo (Dominio)</span>
                                        <span class="text-sm font-bold text-slate-800">{{ $maintenance->device->computer_name }}</span>
                                    </div>
                                @endif

                                @if($maintenance->device->currentAssignment)
                                    <div class="p-3.5 bg-blue-50/70 rounded-xl border border-blue-100 text-blue-900">
                                        <span class="text-[11px] text-blue-600 font-bold uppercase block">👤 Asignado actualmente a:</span>
                                        <p class="font-extrabold text-sm mt-0.5">{{ $maintenance->device->currentAssignment->employee->name }}</p>
                                        <p class="text-xs text-blue-700">{{ $maintenance->device->currentAssignment->employee->department }} · {{ $maintenance->device->currentAssignment->employee->position }}</p>
                                    </div>
                                @else
                                    <div class="p-3 bg-slate-50 rounded-xl text-slate-600 text-xs font-semibold">
                                        📦 Equipo actualmente en almacén / stock sin asignar.
                                    </div>
                                @endif

                                <div class="pt-2">
                                    <a href="{{ route('devices.show', $maintenance->device) }}" class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition flex items-center justify-center gap-2 shadow-sm">
                                        <span>Ir al Perfil Completo del Equipo</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-slate-500 font-italic">Este equipo fue eliminado del inventario.</p>
                        @endif
                    </div>

                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-3">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-3">
                            👤 Técnico Responsable
                        </h4>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-middleby-100 flex items-center justify-center text-middleby-800 font-black text-base">
                                {{ substr($maintenance->user ? $maintenance->user->name : 'IT', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-extrabold text-slate-800">{{ $maintenance->user ? $maintenance->user->name : 'Usuario del sistema' }}</p>
                                <p class="text-xs text-slate-400">{{ $maintenance->user ? $maintenance->user->email : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Columna derecha: Detalles y Formulario de Cierre --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Síntomas Reportados / Diagnóstico Inicial</h4>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-sm font-medium text-slate-700 leading-relaxed whitespace-pre-line">
                                {{ $maintenance->description ?: 'No se registraron notas de diagnóstico inicial al abrir el ticket.' }}
                            </div>
                        </div>

                        @if($maintenance->status === \App\Enums\MaintenanceStatus::Completado)
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-2 flex items-center gap-1.5">
                                    <span>✓ Solución Aplicada / Resumen de Trabajo Cerrado</span>
                                </h4>
                                <div class="p-5 bg-emerald-50/60 rounded-2xl border border-emerald-200 text-sm font-bold text-emerald-950 leading-relaxed whitespace-pre-line shadow-xs">
                                    {{ $maintenance->resolution_notes }}
                                </div>
                            </div>
                            
                            @if($maintenance->next_due_at)
                                <div class="p-4 bg-indigo-50/70 rounded-2xl border border-indigo-100 flex items-center justify-between text-indigo-950">
                                    <div>
                                        <span class="text-xs font-bold text-indigo-700 block uppercase">Próximo mantenimiento preventivo programado para:</span>
                                        <span class="text-lg font-black text-indigo-900">{{ $maintenance->next_due_at->format('d de F, Y') }}</span>
                                    </div>
                                    <div class="text-2xl">🗓️</div>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Caja Interactiva de Conclusión y Cierre de Orden --}}
                    @if(in_array($maintenance->status, [\App\Enums\MaintenanceStatus::EnProceso, \App\Enums\MaintenanceStatus::Programado]))
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-amber-200 shadow-lg space-y-6">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                                <div>
                                    <h3 class="text-lg font-black text-slate-900">Concluir y Cerrar Mantenimiento</h3>
                                    <p class="text-xs text-slate-500">Registra las acciones correctivas aplicadas y actualiza el estado en tu inventario.</p>
                                </div>
                                <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-lg animate-pulse">
                                    Trabajo Activo
                                </span>
                            </div>

                            <form action="{{ route('maintenances.complete', $maintenance) }}" method="POST" class="space-y-5">
                                @csrf

                                <div>
                                    <label for="resolution_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                        Notas de Solución / Intervención Realizada *
                                    </label>
                                    <textarea id="resolution_notes" name="resolution_notes" rows="4" required
                                              placeholder="Ej. Se limpió disipador y cambió pasta térmica. Se actualizó BIOS a última versión. Todo funcional sin incidencias."
                                              class="w-full border-2 border-slate-200 rounded-2xl p-3.5 text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">{{ old('resolution_notes') }}</textarea>
                                    <p class="text-[11px] text-slate-400 mt-1">Esta bitácora quedará registrada permanentemente en el historial del equipo.</p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="new_device_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                            Estatus Final del Equipo en Inventario *
                                        </label>
                                        <select id="new_device_status" name="new_device_status" required class="w-full border-2 border-slate-200 rounded-xl p-3 text-sm text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                            <option value="mantener">Mantener estatus actual en inventario</option>
                                            <option value="disponible">📦 Cambiar a Disponible (Almacén IT)</option>
                                            @if($maintenance->device && $maintenance->device->currentAssignment)
                                                <option value="asignado" selected>👤 Confirmar como Asignado (Entregar a {{ $maintenance->device->currentAssignment->employee->name }})</option>
                                            @endif
                                            <option value="obsoleto">⚠️ Marcar como Obsoleto / Irreparable</option>
                                            <option value="baja">🚫 Dar de Baja del Inventario</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="next_due_at" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                            Agendar Próxima Revisión Preventiva
                                        </label>
                                        <input type="date" id="next_due_at" name="next_due_at" value="{{ old('next_due_at', $maintenance->next_due_at ? $maintenance->next_due_at->format('Y-m-d') : now()->addMonths(6)->format('Y-m-d')) }}"
                                               class="w-full border-2 border-slate-200 rounded-xl p-2.5 text-sm text-slate-800 font-medium focus:ring-2 focus:ring-emerald-500">
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                    <button type="submit" form="cancel-form" class="text-xs font-bold text-slate-400 hover:text-rose-600 transition underline">
                                        Cancelar esta orden sin finalizar
                                    </button>
                                    
                                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-sm rounded-2xl shadow-md hover:shadow-lg transition active:scale-95 inline-flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Concluir y Cerrar Mantenimiento</span>
                                    </button>
                                </div>
                            </form>
                            
                            <form id="cancel-form" action="{{ route('maintenances.cancel', $maintenance) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
