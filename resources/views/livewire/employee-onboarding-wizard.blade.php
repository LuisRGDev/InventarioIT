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
                            <x-text-input wire:model="email" id="email" type="email" class="mt-1 block w-full" placeholder="larosales@middleby.com"/>
                            <x-input-error :messages="$errors->get('email')" class="mt-1"/>
                        </div>


                        <div>
                            <x-input-label for="employee_code" value="Número de Empleado (Nomina)"/>
                            <x-text-input wire:model="employee_code" id="employee_code" type="text" class="mt-1 block w-full font-mono" placeholder="000"/>
                            <x-input-error :messages="$errors->get('employee_code')" class="mt-1"/>
                        </div>

                        <div>
                            <x-input-label for="domain_account" value="Usuario de Dominio (Active Directory)"/>
                            <x-text-input wire:model="domain_account" id="domain_account" type="text" class="mt-1 block w-full font-mono"/>
                            <x-input-error :messages="$errors->get('domain_account')" class="mt-1"/>
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="job_position_id" value="Puesto, Área y Dirección *"/>
                            <select wire:model="job_position_id" id="job_position_id" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Selecciona un puesto del catálogo...</option>
                                @foreach($this->jobPositions as $jp)
                                    <option value="{{ $jp->id }}">{{ $jp->display_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('job_position_id')" class="mt-1"/>
                        </div>

                        <div class="md:col-span-2 pt-4 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <x-input-label for="assign_phone_line_id" value="Línea Telefónica Móvil (Opcional)"/>
                                <select wire:model="assign_phone_line_id" id="assign_phone_line_id" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">-- Ninguna --</option>
                                    @foreach($this->availablePhoneLines as $line)
                                        <option value="{{ $line->id }}">
                                            {{ $line->number }} - {{ $line->provider }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assign_phone_line_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <x-input-label for="assign_office_extension_id" value="Extensión de Oficina (Opcional)"/>
                                <select wire:model="assign_office_extension_id" id="assign_office_extension_id" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">-- Ninguna --</option>
                                    @foreach($this->availableExtensions as $ext)
                                        <option value="{{ $ext->id }}">
                                            Ext. {{ $ext->extension_number }} {{ $ext->direct_number ? '('.$ext->direct_number.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assign_office_extension_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
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
                        @php $selectedComputer = $this->availableComputers->firstWhere('id', $computer_id) ?? \App\Models\Device::find($computer_id); @endphp
                        <div class="bg-green-50 border border-green-200 p-4 rounded-lg flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-green-900">✓ Equipo seleccionado</h4>
                                @if($selectedComputer)
                                    <p class="text-sm font-bold text-green-800">{{ $selectedComputer->brand }} {{ $selectedComputer->model }}</p>
                                    <p class="text-xs text-green-600 font-mono">SN: {{ $selectedComputer->serial_number }}</p>
                                @else
                                    <p class="text-xs text-green-700">ID: {{ $computer_id }}</p>
                                @endif
                            </div>
                            <button wire:click="clearComputer" type="button" class="text-sm text-red-500 hover:text-red-700 hover:underline font-medium">Quitar</button>
                        </div>
                        
                        <div class="mt-4">
                            <x-input-label for="computer_condition" value="Condición de Entrega *"/>
                            <select wire:model.live="computer_condition" id="computer_condition" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
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
                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-64 overflow-y-auto p-1">
                                @foreach($this->availableComputers as $device)
                                    <div wire:key="computer-{{ $device->id }}" class="border border-gray-200 rounded-lg p-3 hover:border-indigo-300 hover:shadow-sm transition bg-white flex flex-col justify-between">
                                        <div>
                                            <div class="flex justify-between items-start mb-1">
                                                <p class="text-sm font-semibold text-gray-900 line-clamp-1">{{ $device->brand }} {{ $device->model }}</p>
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-800 whitespace-nowrap ml-2">{{ $device->category?->name }}</span>
                                            </div>
                                            <p class="text-xs text-gray-500 font-mono mb-3">SN: {{ $device->serial_number }}</p>
                                        </div>
                                        <button wire:click="selectComputer({{ $device->id }})" type="button" class="w-full py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-600 hover:text-white border border-indigo-100 rounded transition">
                                            Seleccionar
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mt-2">No se encontraron equipos disponibles.</p>
                        @endif
                    @endif

                    @if($this->errorMessage ?? false)
                        <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                            {{ $this->errorMessage }}
                        </div>
                    @endif

                    <div class="flex justify-between pt-4 border-t border-gray-100 mt-6">
                        <button wire:click="skipComputer" type="button" class="px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Saltar Cómputo
                        </button>
                        <button wire:click="assignComputer" type="button" @if($computer_id && empty($computer_condition)) disabled @endif class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                            Siguiente: Asignar Celular
                        </button>
                    </div>
                </div>
            @endif

            {{-- PASO 3: Celular --}}
            @if($step === 3)
                <div class="space-y-6">
                    @if($smartphone_id)
                        @php $selectedPhone = $this->availableSmartphones->firstWhere('id', $smartphone_id) ?? \App\Models\Device::find($smartphone_id); @endphp
                        <div class="bg-green-50 border border-green-200 p-4 rounded-lg flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-green-900">✓ Celular seleccionado</h4>
                                @if($selectedPhone)
                                    <p class="text-sm font-bold text-green-800">{{ $selectedPhone->brand }} {{ $selectedPhone->model }}</p>
                                    <p class="text-xs text-green-600 font-mono">SN: {{ $selectedPhone->serial_number }}</p>
                                @else
                                    <p class="text-xs text-green-700">ID: {{ $smartphone_id }}</p>
                                @endif
                            </div>
                            <button wire:click="clearSmartphone" type="button" class="text-sm text-red-500 hover:text-red-700 hover:underline font-medium">Quitar</button>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="smartphone_condition" value="Condición de Entrega *"/>
                            <select wire:model.live="smartphone_condition" id="smartphone_condition" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
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
                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-64 overflow-y-auto p-1">
                                @foreach($this->availableSmartphones as $device)
                                    <div wire:key="smartphone-{{ $device->id }}" class="border border-gray-200 rounded-lg p-3 hover:border-indigo-300 hover:shadow-sm transition bg-white flex flex-col justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 line-clamp-1 mb-1">{{ $device->brand }} {{ $device->model }}</p>
                                            <p class="text-xs text-gray-500 font-mono mb-3">SN: {{ $device->serial_number }}@if($device->imei)<br>IMEI: {{ $device->imei }}@endif</p>
                                        </div>
                                        <button wire:click="selectSmartphone({{ $device->id }})" type="button" class="w-full py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-600 hover:text-white border border-indigo-100 rounded transition">
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
                        <button wire:click="skipSmartphone" class="px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
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
