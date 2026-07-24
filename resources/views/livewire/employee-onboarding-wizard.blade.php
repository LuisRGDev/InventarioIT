<div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Stepper Header --}}
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Alta de Empleado</h2>
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold {{ $step >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">1</span>
                <div class="w-8 h-1 {{ $step >= 2 ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>
                <span class="w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold {{ $step >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">2</span>
                <div class="w-8 h-1 {{ $step >= 3 ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>
                <span class="w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold {{ $step >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">3</span>
            </div>
        </div>

        <div class="p-6">
            {{-- PASO 1: Datos del Empleado --}}
            @if($step === 1)
                <form wire:submit="saveEmployee" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <x-input-label for="name" value="Nombre Completo *"/>
                            <x-text-input wire:model="name" id="name" type="text" class="mt-1 block w-full" placeholder="Ej. Juan Pérez"/>
                            <x-input-error :messages="$errors->get('name')" class="mt-1"/>
                        </div>

                        <div>
                            <x-input-label for="email" value="Correo Electrónico *"/>
                            <x-text-input wire:model="email" id="email" type="email" class="mt-1 block w-full" placeholder="juan@empresa.com"/>
                            <x-input-error :messages="$errors->get('email')" class="mt-1"/>
                        </div>

                        <div>
                            <x-input-label for="phone" value="Teléfono Personal"/>
                            <x-text-input wire:model="phone" id="phone" type="text" class="mt-1 block w-full"/>
                            <x-input-error :messages="$errors->get('phone')" class="mt-1"/>
                        </div>

                        <div>
                            <x-input-label for="employee_code" value="Número de Empleado (Nomina)"/>
                            <x-text-input wire:model="employee_code" id="employee_code" type="text" class="mt-1 block w-full font-mono"/>
                            <x-input-error :messages="$errors->get('employee_code')" class="mt-1"/>
                        </div>

                        <div>
                            <x-input-label for="domain_account" value="Usuario de Dominio (Active Directory)"/>
                            <x-text-input wire:model="domain_account" id="domain_account" type="text" class="mt-1 block w-full font-mono"/>
                            <x-input-error :messages="$errors->get('domain_account')" class="mt-1"/>
                        </div>

                        <div>
                            <x-input-label for="department" value="Departamento *"/>
                            <x-text-input wire:model="department" id="department" type="text" class="mt-1 block w-full" placeholder="Ej. TI, Recursos Humanos"/>
                            <x-input-error :messages="$errors->get('department')" class="mt-1"/>
                        </div>

                        <div>
                            <x-input-label for="position" value="Puesto *"/>
                            <x-text-input wire:model="position" id="position" type="text" class="mt-1 block w-full" placeholder="Ej. Desarrollador Sr."/>
                            <x-input-error :messages="$errors->get('position')" class="mt-1"/>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                            Siguiente: Asignar Cómputo
                        </button>
                    </div>
                </form>
            @endif

            {{-- PASO 2: Cómputo --}}
            @if($step === 2)
                <div class="space-y-6">
                    <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-lg">
                        <p class="text-sm text-indigo-800">Empleado creado exitosamente. Ahora puedes asignarle un equipo de cómputo, o saltar este paso.</p>
                    </div>

                    @if($computer_id)
                        <div class="bg-green-50 border border-green-200 p-4 rounded-lg flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-green-900">Equipo seleccionado</h4>
                                <p class="text-xs text-green-700">Equipo ID: {{ $computer_id }}</p>
                            </div>
                            <button wire:click="clearComputer" class="text-sm text-red-600 hover:underline">Quitar</button>
                        </div>
                        
                        <div class="mt-4">
                            <x-input-label for="computer_condition" value="Condición de Entrega *"/>
                            <select wire:model="computer_condition" id="computer_condition" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">Seleccionar condición...</option>
                                @foreach($this->conditions as $cond)
                                    <option value="{{ $cond->value }}">{{ $cond->label() }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('computer_condition')" class="mt-1"/>
                        </div>
                    @else
                        <div>
                            <x-input-label for="computerSearch" value="Buscar equipo disponible (Laptop/Desktop)"/>
                            <x-text-input wire:model.live.debounce.300ms="computerSearch" id="computerSearch" type="text" class="mt-1 block w-full" placeholder="Buscar por serie, modelo o marca..."/>
                        </div>
                        
                        @if(count($this->availableComputers) > 0)
                            <div class="mt-2 border border-gray-200 rounded-lg overflow-hidden divide-y divide-gray-100">
                                @foreach($this->availableComputers as $device)
                                    <div class="p-3 hover:bg-gray-50 flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $device->brand }} {{ $device->model }}</p>
                                            <p class="text-xs text-gray-500 font-mono">{{ $device->serial_number }}</p>
                                        </div>
                                        <button wire:click="selectComputer({{ $device->id }})" class="px-3 py-1 text-xs bg-white border border-gray-300 rounded hover:bg-gray-50">
                                            Seleccionar
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mt-2">No se encontraron equipos disponibles.</p>
                        @endif
                    @endif

                    <div class="flex justify-between pt-4 border-t border-gray-100 mt-6">
                        <button wire:click="assignComputer" class="px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Saltar Cómputo
                        </button>
                        <button wire:click="assignComputer" @if($computer_id && empty($computer_condition)) disabled @endif class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                            Siguiente: Asignar Celular
                        </button>
                    </div>
                </div>
            @endif

            {{-- PASO 3: Celular --}}
            @if($step === 3)
                <div class="space-y-6">
                    @if($smartphone_id)
                        <div class="bg-green-50 border border-green-200 p-4 rounded-lg flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-green-900">Celular seleccionado</h4>
                                <p class="text-xs text-green-700">Equipo ID: {{ $smartphone_id }}</p>
                            </div>
                            <button wire:click="clearSmartphone" class="text-sm text-red-600 hover:underline">Quitar</button>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="smartphone_condition" value="Condición de Entrega *"/>
                            <select wire:model="smartphone_condition" id="smartphone_condition" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">Seleccionar condición...</option>
                                @foreach($this->conditions as $cond)
                                    <option value="{{ $cond->value }}">{{ $cond->label() }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('smartphone_condition')" class="mt-1"/>
                        </div>
                    @else
                        <div>
                            <x-input-label for="smartphoneSearch" value="Buscar celular disponible"/>
                            <x-text-input wire:model.live.debounce.300ms="smartphoneSearch" id="smartphoneSearch" type="text" class="mt-1 block w-full" placeholder="Buscar por IMEI, modelo o marca..."/>
                        </div>

                        @if(count($this->availableSmartphones) > 0)
                            <div class="mt-2 border border-gray-200 rounded-lg overflow-hidden divide-y divide-gray-100">
                                @foreach($this->availableSmartphones as $device)
                                    <div class="p-3 hover:bg-gray-50 flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $device->brand }} {{ $device->model }}</p>
                                            <p class="text-xs text-gray-500 font-mono">{{ $device->serial_number }}</p>
                                        </div>
                                        <button wire:click="selectSmartphone({{ $device->id }})" class="px-3 py-1 text-xs bg-white border border-gray-300 rounded hover:bg-gray-50">
                                            Seleccionar
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mt-2">No se encontraron celulares disponibles.</p>
                        @endif
                    @endif

                    <div class="flex justify-between pt-4 border-t border-gray-100 mt-6">
                        <button wire:click="assignSmartphone" class="px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Finalizar sin Celular
                        </button>
                        <button wire:click="assignSmartphone" @if($smartphone_id && empty($smartphone_condition)) disabled @endif class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                            Finalizar y Guardar
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
