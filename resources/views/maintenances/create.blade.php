<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-middleby-900 leading-tight tracking-tight flex items-center gap-2.5">
                <span class="w-2 h-6 bg-gradient-to-b from-amber-500 to-middleby-700 rounded-full inline-block shadow-sm"></span>
                {{ __('Abrir Registro de Mantenimiento IT') }}
            </h2>
            <a href="{{ route('maintenances.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver a Mantenimientos
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden" x-data="{ status: 'en_proceso', type: 'preventivo' }">
                
                {{-- Cabecera decorativa --}}
                <div class="bg-gradient-to-r from-slate-900 via-middleby-900 to-slate-800 p-6 sm:p-8 text-white">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-400/30 flex items-center justify-center text-amber-300 flex-shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Nueva Intervención o Taller</h3>
                            <p class="text-xs sm:text-sm text-slate-300 mt-1">Lleva el control técnico de checkups preventivos, diagnósticos y reparaciones correctivas.</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('maintenances.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
                    @csrf

                    @if ($errors->any())
                        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl text-rose-800 text-sm">
                            <p class="font-bold">Por favor corrige los siguientes errores:</p>
                            <ul class="list-disc pl-5 mt-1 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Selección de Equipo --}}
                    <div>
                        <label for="device_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Equipo Informático *
                        </label>
                        <select id="device_id" name="device_id" required class="w-full border-2 border-slate-200 rounded-xl p-3 text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 bg-slate-50/50 hover:bg-white transition">
                            <option value="">-- Selecciona el equipo o dispositivo --</option>
                            @foreach ($devices as $d)
                                @php
                                    $employeeInfo = $d->currentAssignment ? " (Asignado a: {$d->currentAssignment->employee->name})" : " [En Stock - {$d->status->label()}]";
                                @endphp
                                <option value="{{ $d->id }}" {{ (old('device_id', $selectedDeviceId) == $d->id) ? 'selected' : '' }}>
                                    {{ $d->brand }} {{ $d->model }} · SN: {{ $d->serial_number }} {{ $employeeInfo }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-1">Selecciona qué computadora o dispositivo recibirá el servicio técnico.</p>
                    </div>

                    {{-- Tipo de Mantenimiento --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                        <div>
                            <label for="type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Tipo de Servicio *
                            </label>
                            <select id="type" name="type" x-model="type" required class="w-full border-2 border-slate-200 rounded-xl p-3 text-sm text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition">
                                <option value="preventivo">🛡️ Mantenimiento Preventivo (Limpieza / Check-up)</option>
                                <option value="correctivo">🔧 Mantenimiento Correctivo (Reparación / Falla)</option>
                                <option value="upgrade">⚡ Actualización / Upgrade de Hardware</option>
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Estado Inicial del Servicio *
                            </label>
                            <select id="status" name="status" x-model="status" required class="w-full border-2 border-slate-200 rounded-xl p-3 text-sm text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition">
                                <option value="en_proceso">🚀 En Proceso (Ingresa a taller en este momento)</option>
                                <option value="programado">📅 Programar para Fecha Futura</option>
                            </select>
                        </div>
                    </div>

                    {{-- Fecha programada condicional --}}
                    <div x-show="status === 'programado'" x-transition class="bg-indigo-50/70 border border-indigo-200 p-4 rounded-2xl space-y-2">
                        <label for="scheduled_at" class="block text-xs font-bold text-indigo-900 uppercase tracking-wider">
                            Fecha Programada para el Servicio *
                        </label>
                        <input type="date" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}"
                               class="border border-indigo-300 rounded-xl p-2.5 text-sm w-full sm:w-1/2 focus:ring-2 focus:ring-indigo-500 bg-white">
                        <p class="text-xs text-indigo-700">El equipo no se marcará en taller hasta que llegue la fecha o se inicie el servicio.</p>
                    </div>

                    {{-- Título y Síntomas --}}
                    <div>
                        <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Título o Resumen de la Intervención *
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                               placeholder="Ej. Mantenimiento preventivo semestral, Reemplazo de teclado dañado, Ampliación de RAM a 32GB"
                               class="w-full border-2 border-slate-200 rounded-xl p-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition">
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Síntomas, Diagnóstico o Notas Iniciales (Opcional)
                        </label>
                        <textarea id="description" name="description" rows="3"
                                  placeholder="Describe detalles sobre la falla reportada por el usuario o las piezas a inspeccionar..."
                                  class="w-full border-2 border-slate-200 rounded-xl p-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition">{{ old('description') }}</textarea>
                    </div>

                    {{-- Próximo mantenimiento sugerido --}}
                    <div>
                        <label for="next_due_at" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Próxima Revisión Preventiva Sugerida (Opcional)
                        </label>
                        <input type="date" id="next_due_at" name="next_due_at" value="{{ old('next_due_at', now()->addMonths(6)->format('Y-m-d')) }}"
                               class="w-full sm:w-1/2 border-2 border-slate-200 rounded-xl p-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-middleby-500">
                        <p class="text-xs text-slate-400 mt-1">Por defecto sugerimos 6 meses a futuro para mantener una rutina saludable en tu parque vehicular IT.</p>
                    </div>

                    {{-- Checkbox inteligente de cambio de estatus --}}
                    <div x-show="status === 'en_proceso'" x-transition class="bg-amber-50/80 border-2 border-amber-200 p-4 rounded-2xl">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="update_device_status_repair" value="1" checked
                                   class="mt-1 rounded w-5 h-5 text-amber-600 focus:ring-amber-500 border-amber-300">
                            <div>
                                <span class="text-sm font-bold text-amber-900">¿Cambiar el estatus en el inventario a "En Reparación" ahora mismo?</span>
                                <p class="text-xs text-amber-700 mt-0.5">Al marcar esta opción, la computadora se reflejará con color amarillo ("En Reparación") en el Dashboard mientras esté abierta esta bitácora.</p>
                            </div>
                        </label>
                    </div>

                    {{-- Botones de acción --}}
                    <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('maintenances.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-middleby-800 to-middleby-700 text-white font-bold text-sm rounded-xl shadow-md hover:from-middleby-700 hover:to-middleby-600 transition active:scale-95 inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Guardar y Abrir Registro</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
