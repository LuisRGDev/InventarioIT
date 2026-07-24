<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('devices.show', $device) }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Equipo — <span class="text-gray-500 font-normal">{{ $device->brand }} {{ $device->model }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Advertencia si tiene asignación activa --}}
            @if($device->currentAssignment)
                <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-medium text-sm">Este equipo tiene una asignación activa.</p>
                        <p class="text-xs mt-1">Está asignado a <strong>{{ $device->currentAssignment->employee?->name }}</strong>. Para regresar el equipo usa la función de Retorno.</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <p class="text-sm text-gray-500">Modifica la información del equipo. El número de serie no puede cambiarse si el equipo tiene historial de asignaciones.</p>
                </div>

                <form method="POST" action="{{ route('devices.update', $device) }}" class="p-6 space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- Sección: Identificación --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-600 rounded text-xs font-bold text-center leading-5">1</span>
                            Identificación del equipo
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            {{-- Categoría --}}
                            <div>
                                <x-input-label for="device_category_id" value="Categoría *"/>
                                <select id="device_category_id" name="device_category_id"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                               @error('device_category_id') border-red-400 @enderror">
                                    <option value="">Seleccionar…</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ old('device_category_id', $device->device_category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('device_category_id')" class="mt-1"/>
                            </div>

                            {{-- Estatus --}}
                            <div>
                                <x-input-label for="status" value="Estatus *"/>
                                <select id="status" name="status"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                               @error('status') border-red-400 @enderror"
                                        {{ $device->currentAssignment ? 'title=Este equipo tiene asignación activa' : '' }}>
                                    @foreach($statuses as $s)
                                        <option value="{{ $s->value }}"
                                            {{ old('status', $device->status->value) == $s->value ? 'selected' : '' }}>
                                            {{ $s->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-1"/>
                            </div>

                            {{-- Marca --}}
                            <div>
                                <x-input-label for="brand" value="Marca *"/>
                                <x-text-input id="brand" name="brand" type="text" class="mt-1 block w-full"
                                              value="{{ old('brand', $device->brand) }}"
                                              :class="$errors->has('brand') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('brand')" class="mt-1"/>
                            </div>

                            {{-- Modelo --}}
                            <div>
                                <x-input-label for="model" value="Modelo *"/>
                                <x-text-input id="model" name="model" type="text" class="mt-1 block w-full"
                                              value="{{ old('model', $device->model) }}"
                                              :class="$errors->has('model') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('model')" class="mt-1"/>
                            </div>

                            {{-- Serial --}}
                            <div>
                                <x-input-label for="serial_number" value="Número de serie *"/>
                                <x-text-input id="serial_number" name="serial_number" type="text" class="mt-1 block w-full font-mono"
                                              value="{{ old('serial_number', $device->serial_number) }}"
                                              :class="$errors->has('serial_number') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('serial_number')" class="mt-1"/>
                            </div>

                            {{-- Nombre de la PC --}}
                            <div>
                                <x-input-label for="computer_name" value="Nombre de la PC / Hostname"/>
                                <x-text-input id="computer_name" name="computer_name" type="text" class="mt-1 block w-full font-mono"
                                              value="{{ old('computer_name', $device->computer_name) }}"
                                              :class="$errors->has('computer_name') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('computer_name')" class="mt-1"/>
                            </div>

                            {{-- MAC Ethernet --}}
                            <div>
                                <x-input-label for="mac_address_ethernet" value="MAC Ethernet"/>
                                <x-text-input id="mac_address_ethernet" name="mac_address_ethernet" type="text" class="mt-1 block w-full font-mono"
                                              value="{{ old('mac_address_ethernet', $device->mac_address_ethernet) }}"
                                              placeholder="AA:BB:CC:DD:EE:FF"
                                              :class="$errors->has('mac_address_ethernet') ? 'border-red-400' : ''"/>
                                <p class="mt-1 text-xs text-gray-400">Interfaz de red cableada (LAN)</p>
                                <x-input-error :messages="$errors->get('mac_address_ethernet')" class="mt-1"/>
                            </div>

                            {{-- MAC WiFi --}}
                            <div>
                                <x-input-label for="mac_address_wifi" value="MAC WiFi"/>
                                <x-text-input id="mac_address_wifi" name="mac_address_wifi" type="text" class="mt-1 block w-full font-mono"
                                              value="{{ old('mac_address_wifi', $device->mac_address_wifi) }}"
                                              placeholder="AA:BB:CC:DD:EE:FF"
                                              :class="$errors->has('mac_address_wifi') ? 'border-red-400' : ''"/>
                                <p class="mt-1 text-xs text-gray-400">Interfaz inalámbrica (WLAN)</p>
                                <x-input-error :messages="$errors->get('mac_address_wifi')" class="mt-1"/>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Sección: Garantía y fechas --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-600 rounded text-xs font-bold text-center leading-5">2</span>
                            Fechas y Garantía
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <x-input-label for="purchase_date" value="Fecha de compra"/>
                                <x-text-input id="purchase_date" name="purchase_date" type="date" class="mt-1 block w-full"
                                              value="{{ old('purchase_date', $device->purchase_date?->format('Y-m-d')) }}"
                                              :class="$errors->has('purchase_date') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('purchase_date')" class="mt-1"/>
                            </div>
                            <div>
                                <x-input-label for="warranty_expires_at" value="Vencimiento de garantía"/>
                                <x-text-input id="warranty_expires_at" name="warranty_expires_at" type="date" class="mt-1 block w-full"
                                              value="{{ old('warranty_expires_at', $device->warranty_expires_at?->format('Y-m-d')) }}"
                                              :class="$errors->has('warranty_expires_at') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('warranty_expires_at')" class="mt-1"/>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Sección: Notas --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-600 rounded text-xs font-bold text-center leading-5">3</span>
                            Notas adicionales
                        </h3>
                        <div>
                            <x-input-label for="notes" value="Notas u observaciones"/>
                            <textarea id="notes" name="notes" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('notes', $device->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-1"/>
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('devices.show', $device) }}"
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
