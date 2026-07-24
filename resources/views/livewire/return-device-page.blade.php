<div class="max-w-4xl mx-auto py-8">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Registrar Devolución de Equipo</h2>
        <p class="text-gray-600 mt-1">Registra la recepción de un equipo devuelto por un empleado al almacén.</p>
    </div>

    @if ($successMessage)
        <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 flex items-center gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium">{{ $successMessage }}</span>
        </div>
    @endif

    @if ($errorMessage)
        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-200 flex items-center gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span class="font-medium">{{ $errorMessage }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 sm:p-8">
            @if (!$this->device)
                <div class="text-center py-10">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Equipo no encontrado</h3>
                    <p class="mt-1 text-sm text-gray-500">No se pudo localizar el equipo o no se especificó un ID válido.</p>
                    <div class="mt-6">
                        <a href="{{ route('assignments.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                            Volver a Asignaciones
                        </a>
                    </div>
                </div>
            @elseif (!$this->device->currentAssignment)
                <div class="text-center py-10">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Equipo no asignado</h3>
                    <p class="mt-1 text-sm text-gray-500">Este equipo ({{ $this->device->serial_number }}) actualmente se encuentra disponible en almacén, no tiene una asignación activa para devolver.</p>
                    <div class="mt-6">
                        <a href="{{ route('assignments.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                            Volver a Asignaciones
                        </a>
                    </div>
                </div>
            @else
                <form wire:submit.prevent="prepareConfirm" class="space-y-8">
                    {{-- Información Actual --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Datos del Equipo</h3>
                            <div class="flex items-start gap-4">
                                <div class="bg-indigo-100 p-3 rounded-lg text-indigo-700 mt-1">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $this->device->brand }} {{ $this->device->model }}</p>
                                    <p class="text-sm text-gray-500">SN: {{ $this->device->serial_number }}</p>
                                    <span class="inline-flex mt-2 items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $this->device->category->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Empleado Actual</h3>
                            <div class="flex items-start gap-4">
                                <div class="bg-emerald-100 p-3 rounded-lg text-emerald-700 mt-1">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $this->device->currentAssignment->employee->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $this->device->currentAssignment->employee->department }} - {{ $this->device->currentAssignment->employee->position }}</p>
                                    <p class="text-xs text-gray-400 mt-1">Asignado el: {{ $this->device->currentAssignment->assigned_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6"></div>

                    {{-- Formulario de Recepción --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            Evaluación de Retorno
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Condición --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Condición en la que se recibe</label>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach($this->conditions as $cond)
                                        <label class="relative flex items-center p-3 rounded-xl border cursor-pointer hover:bg-gray-50 transition-colors {{ $conditionOnReturn === $cond->value ? 'bg-indigo-50 border-indigo-500 ring-1 ring-indigo-500' : 'bg-white border-gray-200' }}">
                                            <input type="radio" wire:model.live="conditionOnReturn" value="{{ $cond->value }}" class="sr-only">
                                            <div class="flex items-center gap-3">
                                                <div class="w-4 h-4 rounded-full border flex items-center justify-center {{ $conditionOnReturn === $cond->value ? 'border-indigo-600' : 'border-gray-300' }}">
                                                    @if($conditionOnReturn === $cond->value)
                                                        <div class="w-2 h-2 rounded-full bg-indigo-600"></div>
                                                    @endif
                                                </div>
                                                <span class="text-sm font-medium {{ $conditionOnReturn === $cond->value ? 'text-indigo-900' : 'text-gray-700' }}">
                                                    {{ $cond->label() }}
                                                </span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('conditionOnReturn') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Nuevo Estatus --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nuevo Estatus del Equipo</label>
                                <select wire:model="newStatus" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @foreach($this->returnableStatuses as $status)
                                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-xs text-gray-500">
                                    @if($conditionOnReturn === 'daniado')
                                        <span class="text-amber-600 font-medium">Nota:</span> Como el equipo viene dañado, se recomienda enviarlo a reparación o darlo de baja.
                                    @else
                                        Se enviará automáticamente a este estatus en almacén.
                                    @endif
                                </p>
                                @error('newStatus') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Notas --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Notas de Devolución (Opcional)</label>
                                <textarea wire:model="notes" rows="3" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Escribe detalles sobre accesorios faltantes, rayones, o motivos de devolución..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-5 flex justify-end gap-3">
                        <a href="{{ route('assignments.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Confirmar Devolución
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- Modal de Confirmación --}}
    <div x-data="{ show: @entangle('showConfirm') }" x-show="show" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="show" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10 text-indigo-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">¿Confirmar Devolución?</h3>
                            <div class="mt-2 text-sm text-gray-500">
                                <p>Estás a punto de devolver el equipo al almacén. Se registrará la fecha y tu usuario como responsable de la recepción.</p>
                                @if($this->device)
                                    <ul class="mt-3 list-disc pl-5 space-y-1">
                                        <li><strong>Equipo:</strong> {{ $this->device->serial_number }}</li>
                                        <li><strong>Empleado:</strong> {{ $this->device->currentAssignment?->employee->name }}</li>
                                        <li><strong>Nuevo Estatus:</strong> {{ ucfirst($newStatus) }}</li>
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="returnDevice" type="button" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Sí, Devolver Equipo
                    </button>
                    <button wire:click="$set('showConfirm', false)" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
