<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('employees.show', $employee) }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Empleado — <span class="text-gray-500 font-normal">{{ $employee->name }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Advertencia equipos activos --}}
            @if($employee->currentAssignments->count() > 0)
                <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-medium text-sm">Este empleado tiene {{ $employee->currentAssignments->count() }} equipo(s) asignado(s).</p>
                        <p class="text-xs mt-1">Para darlo de baja, primero retorna todos sus equipos desde la sección de Asignaciones.</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <p class="text-sm text-gray-500">Modifica la información del empleado. El correo electrónico debe ser único en el sistema.</p>
                </div>

                <form method="POST" action="{{ route('employees.update', $employee) }}" class="p-6 space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- Sección 1: Datos personales --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-600 rounded text-xs font-bold text-center leading-5">1</span>
                            Datos personales
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div class="sm:col-span-2">
                                <x-input-label for="name" value="Nombre completo *"/>
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                              value="{{ old('name', $employee->name) }}"
                                              :class="$errors->has('name') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('name')" class="mt-1"/>
                            </div>

                            <div>
                                <x-input-label for="employee_code" value="Código de empleado"/>
                                <x-text-input id="employee_code" name="employee_code" type="text" class="mt-1 block w-full font-mono"
                                              value="{{ old('employee_code', $employee->employee_code) }}"
                                              placeholder="EMP-001"
                                              :class="$errors->has('employee_code') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('employee_code')" class="mt-1"/>
                            </div>

                            <div>
                                <x-input-label for="domain_account" value="Usuario en dominio (Active Directory)"/>
                                <x-text-input id="domain_account" name="domain_account" type="text" class="mt-1 block w-full font-mono"
                                              value="{{ old('domain_account', $employee->domain_account) }}"
                                              placeholder="jgarcia"
                                              :class="$errors->has('domain_account') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('domain_account')" class="mt-1"/>
                            </div>

                            <div>
                                <x-input-label for="status" value="Estatus *"/>
                                <select id="status" name="status"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                               @error('status') border-red-400 @enderror">
                                    @foreach($statuses as $s)
                                        <option value="{{ $s->value }}"
                                            {{ old('status', $employee->status->value) == $s->value ? 'selected' : '' }}>
                                            {{ $s->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-1"/>
                            </div>

                            <div>
                                <x-input-label for="email" value="Correo electrónico *"/>
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                              value="{{ old('email', $employee->email) }}"
                                              :class="$errors->has('email') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('email')" class="mt-1"/>
                            </div>

                            <div>
                                <x-input-label for="phone" value="Teléfono"/>
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                                              value="{{ old('phone', $employee->phone) }}"
                                              placeholder="+52 55 1234 5678"
                                              :class="$errors->has('phone') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('phone')" class="mt-1"/>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Sección 2: Información laboral --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-600 rounded text-xs font-bold text-center leading-5">2</span>
                            Información laboral
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <x-input-label for="department" value="Departamento *"/>
                                <x-text-input id="department" name="department" type="text" class="mt-1 block w-full"
                                              value="{{ old('department', $employee->department) }}"
                                              :class="$errors->has('department') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('department')" class="mt-1"/>
                            </div>
                            <div>
                                <x-input-label for="position" value="Puesto / Cargo *"/>
                                <x-text-input id="position" name="position" type="text" class="mt-1 block w-full"
                                              value="{{ old('position', $employee->position) }}"
                                              :class="$errors->has('position') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('position')" class="mt-1"/>
                            </div>
                        </div>
                        <div>
                        <x-input-label for="assign_phone_line_id" value="Línea Telefónica Móvil Actual"/>
                        <select id="assign_phone_line_id" name="assign_phone_line_id" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="">-- Ninguna --</option>
                            
                            @if($currentPhoneLine)
                                <option value="{{ $currentPhoneLine->id }}" selected>
                                    {{ $currentPhoneLine->number }} - {{ $currentPhoneLine->provider }} (Actual)
                                </option>
                            @endif

                            @foreach($availablePhoneLines as $line)
                                <option value="{{ $line->id }}">
                                    {{ $line->number }} - {{ $line->provider }} (Disponible)
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('assign_phone_line_id')" class="mt-1"/>
                    </div>

                    <div>
                        <x-input-label for="assign_office_extension_id" value="Extensión de Oficina Actual"/>
                        <select id="assign_office_extension_id" name="assign_office_extension_id" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="">-- Ninguna --</option>
                            
                            @if($currentExtension)
                                <option value="{{ $currentExtension->id }}" selected>
                                    Ext. {{ $currentExtension->extension_number }} {{ $currentExtension->direct_number ? '('.$currentExtension->direct_number.')' : '' }} (Actual)
                                </option>
                            @endif

                            @foreach($availableExtensions as $ext)
                                <option value="{{ $ext->id }}">
                                    Ext. {{ $ext->extension_number }} {{ $ext->direct_number ? '('.$ext->direct_number.')' : '' }} (Disponible)
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('assign_office_extension_id')" class="mt-1"/>
                    </div>
                </div>

                    <hr class="border-gray-100">

                    {{-- Sección 3: Notas --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-600 rounded text-xs font-bold text-center leading-5">3</span>
                            Notas adicionales
                        </h3>
                        <div>
                            <x-input-label for="notes" value="Observaciones"/>
                            <textarea id="notes" name="notes" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('notes', $employee->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-1"/>
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('employees.show', $employee) }}"
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
