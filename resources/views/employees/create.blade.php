<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('employees.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agregar Empleado</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <p class="text-sm text-gray-500">Registra la información del empleado para poder asignarle equipos de cómputo.</p>
                </div>

                <form method="POST" action="{{ route('employees.store') }}" class="p-6 space-y-8">
                    @csrf

                    {{-- Sección 1: Datos personales --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-600 rounded text-xs font-bold text-center leading-5">1</span>
                            Datos personales
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            {{-- Nombre --}}
                            <div class="sm:col-span-2">
                                <x-input-label for="name" value="Nombre completo *"/>
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                              value="{{ old('name') }}" placeholder="Ej. Juan García López"
                                              :class="$errors->has('name') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('name')" class="mt-1"/>
                            </div>

                            {{-- Código de empleado --}}
                            <div>
                                <x-input-label for="employee_code" value="Código de empleado"/>
                                <x-text-input id="employee_code" name="employee_code" type="text" class="mt-1 block w-full font-mono"
                                              value="{{ old('employee_code') }}" placeholder="EMP-001"
                                              :class="$errors->has('employee_code') ? 'border-red-400' : ''"/>
                                <p class="mt-1 text-xs text-gray-400">Identificador único opcional</p>
                                <x-input-error :messages="$errors->get('employee_code')" class="mt-1"/>
                            </div>

                            {{-- Usuario en dominio --}}
                            <div>
                                <x-input-label for="domain_account" value="Usuario en dominio (Active Directory)"/>
                                <x-text-input id="domain_account" name="domain_account" type="text" class="mt-1 block w-full font-mono"
                                              value="{{ old('domain_account') }}" placeholder="jgarcia"
                                              :class="$errors->has('domain_account') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('domain_account')" class="mt-1"/>
                            </div>

                            {{-- Estatus --}}
                            <div>
                                <x-input-label for="status" value="Estatus *"/>
                                <select id="status" name="status"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                               @error('status') border-red-400 @enderror">
                                    @foreach($statuses as $s)
                                        <option value="{{ $s->value }}" {{ old('status', 'activo') == $s->value ? 'selected' : '' }}>
                                            {{ $s->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-1"/>
                            </div>

                            {{-- Email --}}
                            <div>
                                <x-input-label for="email" value="Correo electrónico *"/>
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                              value="{{ old('email') }}" placeholder="juan.garcia@empresa.com"
                                              :class="$errors->has('email') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('email')" class="mt-1"/>
                            </div>

                            {{-- Teléfono --}}
                            <div>
                                <x-input-label for="phone" value="Teléfono"/>
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                                              value="{{ old('phone') }}" placeholder="+52 55 1234 5678"
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

                            {{-- Departamento --}}
                            <div>
                                <x-input-label for="department" value="Departamento *"/>
                                <x-text-input id="department" name="department" type="text" class="mt-1 block w-full"
                                              value="{{ old('department') }}" placeholder="Ej. Tecnología de la Información"
                                              :class="$errors->has('department') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('department')" class="mt-1"/>
                            </div>

                            {{-- Puesto --}}
                            <div>
                                <x-input-label for="position" value="Puesto / Cargo *"/>
                                <x-text-input id="position" name="position" type="text" class="mt-1 block w-full"
                                              value="{{ old('position') }}" placeholder="Ej. Desarrollador Backend"
                                              :class="$errors->has('position') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('position')" class="mt-1"/>
                            </div>
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
                                      class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                      placeholder="Información relevante del empleado…">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-1"/>
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('employees.index') }}"
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                            Registrar Empleado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
