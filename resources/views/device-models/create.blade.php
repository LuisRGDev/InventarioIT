<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-xl text-slate-900 leading-tight tracking-tight flex items-center gap-2.5">
                <span class="w-2.5 h-6 bg-gradient-to-b from-middleby-600 to-amber-500 rounded-full inline-block shadow-sm"></span>
                {{ __('Registrar Nuevo Estándar / Modelo de Hardware') }}
            </h2>
            <a href="{{ route('device-models.index') }}" class="text-sm font-bold text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver al Catálogo
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                
                {{-- Cabecera decorativa --}}
                <div class="bg-gradient-to-r from-slate-900 via-middleby-900 to-slate-800 p-6 sm:p-8 text-white">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center text-indigo-300 flex-shrink-0 shadow-inner">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black">Plantilla de Especificaciones Corporativas</h3>
                            <p class="text-xs sm:text-sm text-slate-300 mt-1 font-medium">Define marcas, modelos y variantes para que al dar de alta equipos, todos compartan la misma redacción sin errores de escritura.</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('device-models.store') }}" method="POST" class="p-6 sm:p-8 space-y-8"
                      x-data="{ 
                          categoryId: '{{ old('device_category_id') }}',
                          categories: {{ $categories->map(fn($c) => ['id' => $c->id, 'slug' => $c->slug])->toJson() }},
                          get isSmartphone() {
                              const cat = this.categories.find(c => c.id == this.categoryId);
                              return cat && cat.slug === 'smartphone';
                          }
                      }">
                    @csrf

                    @if ($errors->any())
                        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-2xl text-rose-800 text-sm animate-fade-in">
                            <p class="font-black">Por favor corrige los siguientes errores:</p>
                            <ul class="list-disc pl-5 mt-1 space-y-1 font-medium">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- 1. Identificación y Categoría --}}
                    <div>
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2 border-b border-slate-100 pb-2">
                            <span class="inline-block w-5 h-5 bg-middleby-100 text-middleby-800 rounded text-xs font-black text-center leading-5">1</span>
                            Información Principal del Modelo
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            {{-- Categoría --}}
                            <div>
                                <label for="device_category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Categoría del Dispositivo *
                                </label>
                                <select id="device_category_id" name="device_category_id" x-model="categoryId" required class="w-full border-2 border-slate-200 rounded-2xl p-3 text-sm text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 bg-slate-50/50 hover:bg-white transition">
                                    <option value="">-- Selecciona la categoría --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('device_category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Marca --}}
                            <div>
                                <label for="brand" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Marca / Fabricante *
                                </label>
                                <input type="text" id="brand" name="brand" value="{{ old('brand') }}" required placeholder="Ej. HP, Dell, Apple, Lenovo..."
                                       class="w-full border-2 border-slate-200 rounded-2xl p-3 text-sm text-slate-800 font-extrabold focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 bg-slate-50/50 hover:bg-white transition"/>
                            </div>

                            {{-- Modelo --}}
                            <div>
                                <label for="model" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Nombre o Número de Modelo *
                                </label>
                                <input type="text" id="model" name="model" value="{{ old('model') }}" required placeholder="Ej. ProBook 440 G9, OptiPlex 7000..."
                                       class="w-full border-2 border-slate-200 rounded-2xl p-3 text-sm text-slate-800 font-extrabold focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 bg-slate-50/50 hover:bg-white transition"/>
                            </div>
                        </div>

                        {{-- Variante / Edición --}}
                        <div class="mt-5 bg-amber-50/60 p-5 rounded-2xl border border-amber-200/80">
                            <label for="variant" class="block text-xs font-black text-amber-900 uppercase tracking-wider mb-1">
                                Variante, Edición o Sub-Configuración
                            </label>
                            <p class="text-xs text-amber-800 mb-3 font-medium">
                                💡 Útil cuando el mismo modelo físico se vende con distintas piezas de procesador, RAM o almacenamiento.
                            </p>
                            <input type="text" id="variant" name="variant" value="{{ old('variant') }}" placeholder="Ej. Edición Core i5 / 16GB RAM | Directiva Core i7 | 256GB Black"
                                   class="w-full border-2 border-amber-300 rounded-xl p-3 text-sm text-amber-950 font-black focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white transition shadow-2xs"/>
                        </div>
                    </div>

                    {{-- 2. Especificaciones Técnicas --}}
                    <div>
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2 border-b border-slate-100 pb-2">
                            <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-700 rounded text-xs font-black text-center leading-5">2</span>
                            Especificaciones Técnicas por Defecto
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- CPU --}}
                            <div x-show="!isSmartphone" x-transition>
                                <label for="cpu" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Procesador (CPU)
                                </label>
                                <input type="text" id="cpu" name="cpu" value="{{ old('cpu') }}" placeholder="Ej. Intel Core i5-1235U 1.3GHz, Apple M3 Pro..."
                                       class="w-full border-2 border-slate-200 rounded-2xl p-3 text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 bg-slate-50/50 hover:bg-white transition"/>
                            </div>

                            {{-- Núcleos (Cores) --}}
                            <div x-show="!isSmartphone" x-transition>
                                <label for="cores" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Núcleos (Cores)
                                </label>
                                <input type="text" id="cores" name="cores" value="{{ old('cores') }}" placeholder="Ej. 10 Núcleos, Octa-core..."
                                       class="w-full border-2 border-slate-200 rounded-2xl p-3 text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 bg-slate-50/50 hover:bg-white transition"/>
                            </div>

                            {{-- RAM --}}
                            <div>
                                <label for="ram" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Memoria RAM
                                </label>
                                <input type="text" id="ram" name="ram" value="{{ old('ram') }}" placeholder="Ej. 16 GB LPDDR5, 32 GB DDR4..."
                                       class="w-full border-2 border-slate-200 rounded-2xl p-3 text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 bg-slate-50/50 hover:bg-white transition"/>
                            </div>

                            {{-- Almacenamiento --}}
                            <div>
                                <label for="storage" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    <span x-text="isSmartphone ? 'Almacenamiento (Capacidad)' : 'Almacenamiento (Disco)'">Almacenamiento (Disco)</span>
                                </label>
                                <input type="text" id="storage" name="storage" value="{{ old('storage') }}" placeholder="Ej. 512 GB SSD NVMe M.2, 1 TB SSD..."
                                       class="w-full border-2 border-slate-200 rounded-2xl p-3 text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 bg-slate-50/50 hover:bg-white transition"/>
                            </div>

                            {{-- Sistema Operativo --}}
                            <div>
                                <label for="os" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    <span x-text="isSmartphone ? 'Sistema Operativo Móvil' : 'Sistema Operativo por Defecto'">Sistema Operativo por Defecto</span>
                                </label>
                                <input type="text" id="os" name="os" value="{{ old('os') }}" placeholder="Ej. Windows 11 Pro 64-bit, macOS Sonoma, iOS 17..."
                                       class="w-full border-2 border-slate-200 rounded-2xl p-3 text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 bg-slate-50/50 hover:bg-white transition"/>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Notas Adicionales --}}
                    <div>
                        <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Notas o Descripción Interna
                        </label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Ej. Modelo estándar aprobado para directores y desarrolladores senior. Garantía 3 años NBD..."
                                  class="w-full border-2 border-slate-200 rounded-2xl p-3 text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 bg-slate-50/50 hover:bg-white transition">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Botones de Acción --}}
                    <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-4">
                        <a href="{{ route('device-models.index') }}" class="px-5 py-3 rounded-2xl border-2 border-slate-200 hover:bg-slate-50 text-slate-700 font-extrabold text-sm transition active:scale-95">
                            Cancelar
                        </a>
                        <button type="submit" class="px-7 py-3 bg-gradient-to-r from-middleby-800 to-middleby-700 hover:from-middleby-700 hover:to-middleby-600 text-white font-black text-sm rounded-2xl shadow-lg hover:shadow-xl transition active:scale-95 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Guardar y Publicar Estándar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
