<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Nueva Asignación de Línea Telefónica</h2>
                    <p class="text-sm text-gray-500">Selecciona un empleado y la línea que deseas asignarle.</p>
                </div>
            </div>

            <div class="p-6">
                {{-- Alertas --}}
                @if ($successMessage)
                    <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <h3 class="text-sm font-medium text-green-800">¡Éxito!</h3>
                            <p class="text-sm text-green-700 mt-1">{{ $successMessage }}</p>
                        </div>
                    </div>
                @endif

                @if ($errorMessage)
                    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Error</h3>
                            <p class="text-sm text-red-700 mt-1">{{ $errorMessage }}</p>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    {{-- Columna 1: Empleado --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-600 rounded text-xs font-bold text-center leading-5">1</span>
                            Empleado
                        </h3>

                        @if ($this->selectedEmployee)
                            <div class="p-4 rounded-lg border border-indigo-200 bg-indigo-50/50 flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-indigo-900">{{ $this->selectedEmployee->name }}</p>
                                    <p class="text-sm text-indigo-700">{{ $this->selectedEmployee->department }}</p>
                                </div>
                                <button type="button" wire:click="clearEmployee" class="text-indigo-600 hover:text-indigo-800 p-2 rounded-full hover:bg-indigo-100 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        @else
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.300ms="employeeSearch" placeholder="Buscar por nombre, correo o código..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 pl-10 text-sm">
                                <div class="absolute left-3 top-2.5 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                            </div>
                            @error('selectedEmployeeId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror

                            @if(strlen($employeeSearch) > 0)
                                <div class="mt-2 border border-gray-200 rounded-lg shadow-sm bg-white overflow-hidden max-h-60 overflow-y-auto">
                                    @forelse($this->employees as $emp)
                                        <button type="button" wire:click="selectEmployee({{ $emp->id }})" class="w-full text-left px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition">
                                            <p class="text-sm font-medium text-gray-900">{{ $emp->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $emp->email }} &bull; {{ $emp->department }}</p>
                                        </button>
                                    @empty
                                        <div class="px-4 py-3 text-sm text-gray-500 text-center">No se encontraron empleados.</div>
                                    @endforelse
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Columna 2: Línea --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-600 rounded text-xs font-bold text-center leading-5">2</span>
                            Línea Telefónica
                        </h3>

                        @if ($this->selectedPhoneLine)
                            <div class="p-4 rounded-lg border border-emerald-200 bg-emerald-50/50 flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-emerald-900">{{ $this->selectedPhoneLine->number }}</p>
                                    <p class="text-sm text-emerald-700 font-mono">Plan: {{ $this->selectedPhoneLine->data_plan }} - ${{ number_format($this->selectedPhoneLine->plan_cost, 2) }}</p>
                                </div>
                                <button type="button" wire:click="clearPhoneLine" class="text-emerald-600 hover:text-emerald-800 p-2 rounded-full hover:bg-emerald-100 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        @else
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.300ms="phoneSearch" placeholder="Buscar por número o plan..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 pl-10 text-sm">
                                <div class="absolute left-3 top-2.5 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                                </div>
                            </div>
                            @error('selectedPhoneId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror

                            @if(strlen($phoneSearch) > 0)
                                <div class="mt-2 border border-gray-200 rounded-lg shadow-sm bg-white overflow-hidden max-h-60 overflow-y-auto">
                                    @forelse($this->availablePhoneLines as $phone)
                                        <button type="button" wire:click="selectPhoneLine({{ $phone->id }})" class="w-full text-left px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition">
                                            <p class="text-sm font-medium text-gray-900">{{ $phone->number }}</p>
                                            <p class="text-xs text-gray-500 font-mono">Plan: {{ $phone->data_plan }} - ${{ number_format($phone->plan_cost, 2) }}</p>
                                        </button>
                                    @empty
                                        <div class="px-4 py-3 text-sm text-gray-500 text-center">No hay líneas disponibles que coincidan.</div>
                                    @endforelse
                                </div>
                            @endif
                        @endif
                    </div>

                </div>

                <div class="mt-8 border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-600 rounded text-xs font-bold text-center leading-5">3</span>
                        Detalles de Entrega
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-5">
                        <div>
                            <x-input-label for="notes" value="Notas u observaciones"/>
                            <textarea wire:model="notes" id="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Opcional..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Confirmación --}}
                @if($showConfirm)
                    <div class="mt-8 p-5 bg-orange-50 border border-orange-200 rounded-lg flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <h4 class="font-medium text-orange-900">¿Confirmar Asignación?</h4>
                            <p class="text-sm text-orange-700 mt-1">La línea pasará al estatus "En Uso".</p>
                        </div>
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <button type="button" wire:click="$set('showConfirm', false)" class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancelar</button>
                            <button type="button" wire:click="assign" class="w-full sm:w-auto px-5 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition shadow-sm">Confirmar</button>
                        </div>
                    </div>
                @else
                    <div class="mt-8 flex justify-end">
                        <button type="button" wire:click="prepareConfirm" class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm inline-flex items-center gap-2">
                            Continuar
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
