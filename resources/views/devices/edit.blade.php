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

                <form method="POST" action="{{ route('devices.update', $device) }}" class="p-6 space-y-8"
                      x-data="{
                          categoryId: '{{ old('device_category_id', $device->device_category_id) }}',
                          categories: {{ $categoriesJson }},
                          models: {{ $modelsJson ?? '{}' }},
                          selectedModelId: '{{ old('device_model_id', $device->device_model_id ?? '') }}',
                          brand: '{{ old('brand', $device->brand) }}',
                          model: '{{ old('model', $device->model) }}',
                          cpu: '{{ old('specs.cpu', $device->specs['cpu'] ?? '') }}',
                          ram: '{{ old('specs.ram', $device->specs['ram'] ?? '') }}',
                          storage: '{{ old('specs.storage', $device->specs['storage'] ?? '') }}',
                          os: '{{ old('specs.os', $device->specs['os'] ?? '') }}',

                          get isComputer() { return this.categories[this.categoryId]?.isComputer ?? false; },
                          get isSmartphone() { return this.categories[this.categoryId]?.isSmartphone ?? false; },
                          get filteredModels() {
                              return Object.values(this.models).filter(m => !this.categoryId || m.category_id == this.categoryId);
                          },
                          selectModel(id) {
                              if (!id || !this.models[id]) {
                                  return;
                              }
                              let m = this.models[id];
                              this.brand = m.brand;
                              this.model = m.model;
                              if (m.cpu) this.cpu = m.cpu;
                              if (m.ram) this.ram = m.ram;
                              if (m.storage) this.storage = m.storage;
                              if (m.os) this.os = m.os;
                          }
                      }">
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
                                <select id="device_category_id" name="device_category_id" x-model="categoryId"
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

                            {{-- Selector Inteligente de Estándar / SKU --}}
                            <div class="sm:col-span-2 bg-gradient-to-br from-indigo-50/80 via-slate-50 to-amber-50/60 p-5 rounded-2xl border-2 border-indigo-100 shadow-xs my-2">
                                <label for="device_model_id" class="block text-xs font-black uppercase tracking-wider text-indigo-950 flex items-center gap-1.5">
                                    <span>⚡ Estándar de Hardware / Plantilla Corporativa (SKU)</span>
                                </label>
                                <p class="text-xs text-indigo-800/90 mt-1 font-medium mb-2.5">
                                    Selecciona una configuración para <b>autocompletar y sellar</b> la Marca, Modelo y Especificaciones exactas del catálogo sin errores ortográficos:
                                </p>
                                <select id="device_model_id" name="device_model_id" x-model="selectedModelId" @change="selectModel($event.target.value)"
                                        class="block w-full border-2 border-indigo-200 bg-white rounded-xl shadow-xs focus:ring-indigo-500 focus:border-indigo-500 text-sm font-black text-slate-800 p-2.5 transition">
                                    <option value="">-- Entrada Libre Manual / Sin Plantilla Predefinida --</option>
                                    <template x-for="m in filteredModels" :key="m.id">
                                        <option :value="m.id" x-text="m.display_name" :selected="m.id == selectedModelId"></option>
                                    </template>
                                </select>
                                <div x-show="selectedModelId != ''" x-transition class="mt-2 text-xs font-extrabold text-emerald-700 flex items-center gap-1">
                                    <span>✓ Estándar corporativo aplicado. Puedes personalizar manualmente la RAM o disco abajo si este equipo físico es una excepción o tuvo upgrade.</span>
                                </div>
                            </div>

                            {{-- Marca --}}
                            <div>
                                <x-input-label for="brand" value="Marca *"/>
                                <x-text-input id="brand" name="brand" type="text" class="mt-1 block w-full transition"
                                              x-model="brand"
                                              x-bind:readonly="selectedModelId != ''"
                                              x-bind:class="selectedModelId != '' ? 'bg-slate-100 text-slate-600 border-slate-300 font-bold shadow-none cursor-not-allowed' : ''"
                                              value="{{ old('brand', $device->brand) }}"
                                              :class="$errors->has('brand') ? 'border-red-400' : ''"/>
                                <x-input-error :messages="$errors->get('brand')" class="mt-1"/>
                            </div>

                            {{-- Modelo --}}
                            <div>
                                <x-input-label for="model" value="Modelo *"/>
                                <x-text-input id="model" name="model" type="text" class="mt-1 block w-full transition"
                                              x-model="model"
                                              x-bind:readonly="selectedModelId != ''"
                                              x-bind:class="selectedModelId != '' ? 'bg-slate-100 text-slate-600 border-slate-300 font-bold shadow-none cursor-not-allowed' : ''"
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

                    <hr class="border-gray-100 mb-8">

                    {{-- Especificaciones Dinámicas --}}
                    <div x-show="isComputer || isSmartphone" style="display: none;">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-600 rounded text-xs font-bold text-center leading-5">3</span>
                            Especificaciones Técnicas
                        </h3>

                        {{-- Campos de Cómputo --}}
                        <div x-show="isComputer" class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <x-input-label for="specs_cpu" value="Procesador (CPU)"/>
                                <x-text-input id="specs_cpu" name="specs[cpu]" type="text" class="mt-1 block w-full font-medium"
                                              x-model="cpu"
                                              value="{{ old('specs.cpu', $device->specs['cpu'] ?? '') }}" placeholder="Ej. Intel Core i5-1240P"/>
                            </div>
                            <div>
                                <x-input-label for="specs_cores" value="Núcleos"/>
                                <x-text-input id="specs_cores" name="specs[cores]" type="number" class="mt-1 block w-full"
                                              value="{{ old('specs.cores', $device->specs['cores'] ?? '') }}" placeholder="Ej. 12"/>
                            </div>
                            <div>
                                <x-input-label for="specs_ram" value="Memoria RAM"/>
                                <x-text-input id="specs_ram" name="specs[ram]" type="text" class="mt-1 block w-full font-medium"
                                              x-model="ram"
                                              value="{{ old('specs.ram', $device->specs['ram'] ?? '') }}" placeholder="Ej. 16 GB DDR4"/>
                            </div>
                            <div>
                                <x-input-label for="specs_storage" value="Almacenamiento (Disco)"/>
                                <x-text-input id="specs_storage" name="specs[storage]" type="text" class="mt-1 block w-full font-medium"
                                              x-model="storage"
                                              value="{{ old('specs.storage', $device->specs['storage'] ?? '') }}" placeholder="Ej. 512 GB SSD NVMe"/>
                            </div>
                            <div>
                                <x-input-label for="specs_os" value="Sistema Operativo"/>
                                <x-text-input id="specs_os" name="specs[os]" type="text" class="mt-1 block w-full font-medium"
                                              x-model="os"
                                              value="{{ old('specs.os', $device->specs['os'] ?? '') }}" placeholder="Ej. Windows 11 Pro"/>
                            </div>
                        </div>

                        {{-- Campos de Celular --}}
                        <div x-show="isSmartphone" class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <x-input-label for="phone_number" value="Número de Teléfono"/>
                                <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full"
                                              value="{{ old('phone_number', $device->phone_number) }}" placeholder="Ej. 55 1234 5678"/>
                            </div>
                            <div>
                                <x-input-label for="imei" value="IMEI"/>
                                <x-text-input id="imei" name="imei" type="text" class="mt-1 block w-full font-mono"
                                              value="{{ old('imei', $device->imei) }}" placeholder="IMEI del dispositivo"/>
                            </div>
                            <div>
                                <x-input-label for="data_plan" value="Plan de Datos"/>
                                <x-text-input id="data_plan" name="data_plan" type="text" class="mt-1 block w-full"
                                              value="{{ old('data_plan', $device->data_plan) }}" placeholder="Ej. Plan Telcel Max Sin Límite"/>
                            </div>
                            <div>
                                <x-input-label for="specs_os_mobile" value="Sistema Operativo"/>
                                <x-text-input id="specs_os_mobile" name="specs[os]" type="text" class="mt-1 block w-full"
                                              value="{{ old('specs.os', $device->specs['os'] ?? '') }}" placeholder="Ej. iOS 17 / Android 14"/>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Sección: Notas --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-600 rounded text-xs font-bold text-center leading-5" x-text="(isComputer || isSmartphone) ? '4' : '3'">3</span>
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
