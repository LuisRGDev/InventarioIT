<div class="max-w-5xl mx-auto py-8">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Reemplazar Equipo</h2>
        <p class="text-gray-600 mt-1">Devuelve el equipo actual de un empleado y entrégale uno nuevo en un solo paso.</p>
    </div>

    @if ($successMessage)
        <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-medium">{{ $successMessage }}</span>
            </div>
            <a href="{{ route('assignments.index') }}" class="text-sm font-medium underline text-emerald-700 hover:text-emerald-900">Ir a Asignaciones</a>
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
            <form wire:submit.prevent="prepareConfirm" class="space-y-10">
                
                {{-- 1. Empleado y Equipo Viejo --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900 flex items-center gap-2 mb-6">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-sm font-bold">1</span>
                        Empleado y Equipo a Devolver
                    </h3>

                    @if (!$employeeId)
                        <div class="relative max-w-lg">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input wire:model.live.debounce.300ms="employeeSearch" type="text" class="block w-full pl-10 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Buscar empleado por nombre o correo...">
                            
                            @if(strlen($employeeSearch) > 0 && count($this->employees) > 0)
                                <div class="absolute z-10 w-full mt-1 bg-white shadow-lg rounded-xl border border-gray-100 max-h-60 overflow-auto">
                                    <ul class="py-1 divide-y divide-gray-100">
                                        @foreach($this->employees as $emp)
                                            <li>
                                                <button type="button" wire:click="selectEmployee({{ $emp->id }})" class="w-full text-left px-4 py-3 hover:bg-indigo-50 flex flex-col transition-colors">
                                                    <span class="font-medium text-gray-900">{{ $emp->name }}</span>
                                                    <span class="text-xs text-gray-500">{{ $emp->email }} • {{ $emp->department }}</span>
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @elseif(strlen($employeeSearch) > 0)
                                <div class="absolute z-10 w-full mt-1 bg-white shadow-lg rounded-xl border border-gray-100 p-4 text-center text-sm text-gray-500">
                                    No se encontraron empleados con equipos activos.
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-5 flex items-start justify-between">
                            <div class="flex gap-4">
                                <div class="bg-white p-3 rounded-lg text-indigo-600 shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $this->selectedEmployee->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $this->selectedEmployee->email }} • {{ $this->selectedEmployee->department }}</p>
                                </div>
                            </div>
                            <button type="button" wire:click="selectEmployee(null)" class="text-sm text-indigo-600 font-medium hover:text-indigo-800">Cambiar Empleado</button>
                        </div>

                        {{-- Selección del equipo viejo --}}
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Selecciona el equipo que va a entregar:</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($this->selectedEmployee->currentAssignments as $assignment)
                                    <label class="relative flex p-4 cursor-pointer rounded-xl border bg-white shadow-sm hover:border-indigo-400 hover:ring-1 hover:ring-indigo-400 transition-all {{ $oldDeviceId == $assignment->device_id ? 'border-indigo-600 ring-2 ring-indigo-600 bg-indigo-50/50' : 'border-gray-200' }}">
                                        <input type="radio" wire:model.live="oldDeviceId" value="{{ $assignment->device_id }}" class="sr-only">
                                        <div class="flex-1">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <span class="block text-sm font-bold text-gray-900">{{ $assignment->device->brand }} {{ $assignment->device->model }}</span>
                                                    <span class="block text-xs text-gray-500 mt-0.5">SN: {{ $assignment->device->serial_number }}</span>
                                                </div>
                                                @if($oldDeviceId == $assignment->device_id)
                                                    <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('oldDeviceId') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Condición de devolución --}}
                        @if($oldDeviceId)
                            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Condición en la que entrega el viejo</label>
                                    <select wire:model="conditionOnReturn" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @foreach($this->conditions as $cond)
                                            <option value="{{ $cond->value }}">{{ $cond->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Estatus al que pasará (Almacén)</label>
                                    <select wire:model="oldDeviceNewStatus" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="disponible">Disponible</option>
                                        <option value="en_reparacion">En Reparación</option>
                                        <option value="obsoleto">Obsoleto / Baja</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <hr class="border-gray-200">

                {{-- 2. Nuevo Equipo --}}
                <div class="{{ !$oldDeviceId ? 'opacity-50 pointer-events-none transition-opacity' : '' }}">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center gap-2 mb-6">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-sm font-bold">2</span>
                        Equipo Nuevo a Asignar
                    </h3>

                    @if (!$newDeviceId)
                        <div class="relative max-w-lg">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input wire:model.live.debounce.300ms="deviceSearch" type="text" class="block w-full pl-10 rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Buscar equipo disponible por número de serie, marca...">
                            
                            @if(strlen($deviceSearch) > 0 && count($this->availableDevices) > 0)
                                <div class="absolute z-10 w-full mt-1 bg-white shadow-lg rounded-xl border border-gray-100 max-h-60 overflow-auto">
                                    <ul class="py-1 divide-y divide-gray-100">
                                        @foreach($this->availableDevices as $dev)
                                            <li>
                                                <button type="button" wire:click="selectNewDevice({{ $dev->id }})" class="w-full text-left px-4 py-3 hover:bg-emerald-50 flex flex-col transition-colors">
                                                    <span class="font-medium text-gray-900">{{ $dev->brand }} {{ $dev->model }}</span>
                                                    <span class="text-xs text-gray-500">SN: {{ $dev->serial_number }} • {{ $dev->category->name }}</span>
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @elseif(strlen($deviceSearch) > 0)
                                <div class="absolute z-10 w-full mt-1 bg-white shadow-lg rounded-xl border border-gray-100 p-4 text-center text-sm text-gray-500">
                                    No se encontraron equipos disponibles.
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-5 flex items-start justify-between">
                            <div class="flex gap-4">
                                <div class="bg-white p-3 rounded-lg text-emerald-600 shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $this->selectedNewDevice->brand }} {{ $this->selectedNewDevice->model }}</h4>
                                    <p class="text-sm text-gray-600">SN: {{ $this->selectedNewDevice->serial_number }} • {{ $this->selectedNewDevice->category->name }}</p>
                                </div>
                            </div>
                            <button type="button" wire:click="$set('newDeviceId', null)" class="text-sm text-emerald-600 font-medium hover:text-emerald-800">Cambiar Equipo</button>
                        </div>

                        {{-- Condición de entrega --}}
                        <div class="mt-5 max-w-sm">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Condición en la que se entrega</label>
                            <select wire:model="conditionOnDelivery" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                @foreach($this->conditions as $cond)
                                    <option value="{{ $cond->value }}">{{ $cond->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    @error('newDeviceId') <span class="text-xs text-red-500 mt-2 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-5 flex justify-end gap-3 border-t border-gray-100 mt-8">
                    <button type="submit" class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors inline-flex items-center gap-2 disabled:opacity-50" {{ (!$oldDeviceId || !$newDeviceId) ? 'disabled' : '' }}>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Procesar Reemplazo
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Confirmación --}}
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
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Confirmar Reemplazo</h3>
                            <div class="mt-4 bg-gray-50 p-4 rounded-lg text-sm text-gray-600">
                                <p class="font-medium text-gray-900 border-b border-gray-200 pb-2 mb-2">Resumen de la operación:</p>
                                <p><strong>Empleado:</strong> {{ $this->selectedEmployee?->name }}</p>
                                <p class="mt-2 text-red-600">
                                    <strong>Devuelve:</strong> 
                                    @if($oldDeviceId && ($oldDev = $this->selectedEmployee?->currentAssignments->firstWhere('device_id', (int) $oldDeviceId)?->device))
                                        {{ $oldDev->brand }} {{ $oldDev->model }} (SN: {{ $oldDev->serial_number }})
                                    @else
                                        Equipo ID #{{ $oldDeviceId }}
                                    @endif
                                </p>
                                <p class="mt-1 text-emerald-600">
                                    <strong>Recibe:</strong> 
                                    @if($this->selectedNewDevice)
                                        {{ $this->selectedNewDevice->brand }} {{ $this->selectedNewDevice->model }} (SN: {{ $this->selectedNewDevice->serial_number }})
                                    @else
                                        Equipo ID #{{ $newDeviceId }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="replace" type="button" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirmar Reemplazo
                    </button>
                    <button wire:click="$set('showConfirm', false)" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
