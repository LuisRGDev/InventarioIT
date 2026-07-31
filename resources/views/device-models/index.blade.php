<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-900 leading-tight tracking-tight flex items-center gap-2.5">
                    <span class="w-2.5 h-7 bg-gradient-to-b from-middleby-600 to-amber-500 rounded-full inline-block shadow-sm"></span>
                    {{ __('Catálogo de Modelos y Estándares de Hardware') }}
                </h2>
                <p class="text-xs sm:text-sm font-semibold text-slate-500 mt-1">Configura las plantillas oficiales y especificaciones corporativas para evitar errores al registrar equipos.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('device-models.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-middleby-800 to-middleby-700 hover:from-middleby-700 hover:to-middleby-600 text-white text-sm font-black rounded-2xl shadow-md hover:shadow-lg transition-all duration-200 inline-flex items-center gap-2 active:scale-95 group">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-white group-hover:rotate-90 transition-transform duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <span>Registrar Nuevo Estándar</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-2xl shadow-xs flex items-center justify-between animate-fade-in">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0 font-extrabold">✓</div>
                        <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-2xl shadow-xs flex items-center justify-between animate-fade-in">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0 font-extrabold">!</div>
                        <p class="text-sm font-bold text-rose-900">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Barra de Búsqueda y Filtros --}}
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <form method="GET" action="{{ route('device-models.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                    <div class="sm:col-span-6">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Buscar por Marca o Modelo</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ej. HP, ProBook 440, Core i7, iPhone..." 
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition"/>
                            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Filtrar por Categoría</label>
                        <select name="category" class="w-full py-2.5 px-3 bg-slate-50/70 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-middleby-500 transition">
                            <option value="">Todas las categorías...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button type="submit" class="w-full py-2.5 px-4 bg-middleby-700 hover:bg-middleby-800 text-white font-black text-sm rounded-xl transition shadow-sm hover:shadow-md active:scale-95 text-center">
                            Filtrar
                        </button>
                        @if(request()->hasAny(['search', 'category']))
                            <a href="{{ route('device-models.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-extrabold text-sm" title="Limpiar Filtros">
                                ✕
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla de Modelos y Variantes --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
                @if($models->isEmpty())
                    <div class="p-16 text-center">
                        <div class="w-20 h-20 mx-auto rounded-full bg-slate-50 border border-slate-200/60 flex items-center justify-center text-slate-400 mb-4 shadow-inner">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-800">No hay modelos ni estándares catalogados aún</h3>
                        <p class="text-sm text-slate-500 max-w-md mx-auto mt-2 font-medium">Crea tu primer modelo de computadora, laptop, celular o impresora. Una vez creados, podrán seleccionarse con 1 clic sin cometer errores ortográficos en el inventario.</p>
                        <div class="mt-6">
                            <a href="{{ route('device-models.create') }}" class="px-6 py-3 bg-gradient-to-r from-middleby-800 to-middleby-700 hover:from-middleby-700 hover:to-middleby-600 text-white font-extrabold rounded-2xl shadow-md transition inline-flex items-center gap-2">
                                <span>+ Dar de Alta mi Primer Estándar de Hardware</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-100 text-xs font-extrabold uppercase text-slate-500 tracking-wider">
                                    <th class="py-4 px-6">Categoría</th>
                                    <th class="py-4 px-6">Marca y Modelo</th>
                                    <th class="py-4 px-6">Variante / Configuración</th>
                                    <th class="py-4 px-6">Especificaciones por Defecto</th>
                                    <th class="py-4 px-6 text-center">Equipos en Inventario</th>
                                    <th class="py-4 px-6 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($models as $model)
                                    <tr class="hover:bg-slate-50/60 transition duration-150">
                                        {{-- Categoría --}}
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold {{ $model->category?->isComputer() ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $model->category?->isComputer() ? 'bg-indigo-500' : 'bg-amber-500' }}"></span>
                                                {{ $model->category?->name ?? 'Sin Categoría' }}
                                            </span>
                                        </td>

                                        {{-- Marca y Modelo --}}
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="font-extrabold text-slate-900 text-base">{{ $model->brand }}</div>
                                            <div class="text-xs font-bold text-slate-500 uppercase tracking-wide">{{ $model->model }}</div>
                                        </td>

                                        {{-- Variante / SKU --}}
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            @if($model->variant)
                                                <span class="px-3 py-1 bg-emerald-50/80 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-black shadow-2xs inline-block">
                                                    🏷️ {{ $model->variant }}
                                                </span>
                                            @else
                                                <span class="text-xs font-semibold text-slate-400 italic">Estándar único / Sin sub-variantes</span>
                                            @endif
                                        </td>

                                        {{-- Especificaciones --}}
                                        <td class="py-4 px-6">
                                            <div class="text-xs text-slate-600 font-semibold bg-slate-50 p-2.5 rounded-xl border border-slate-200/70 inline-block max-w-md">
                                                {{ $model->specs_summary }}
                                            </div>
                                            @if($model->notes)
                                                <p class="text-xs text-slate-400 mt-1 font-medium truncate max-w-xs" title="{{ $model->notes }}">💬 {{ $model->notes }}</p>
                                            @endif
                                        </td>

                                        {{-- Contador Equipos --}}
                                        <td class="py-4 px-6 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-2xl text-xs font-black {{ $model->devices_count > 0 ? 'bg-middleby-100 text-middleby-900 border border-middleby-200' : 'bg-slate-100 text-slate-500' }}">
                                                🖥️ {{ $model->devices_count }} {{ $model->devices_count == 1 ? 'equipo' : 'equipos' }}
                                            </span>
                                        </td>

                                        {{-- Acciones --}}
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('device-models.edit', $model) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition font-bold text-xs inline-flex items-center gap-1">
                                                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    <span>Editar</span>
                                                </a>
                                                <form action="{{ route('device-models.destroy', $model) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que deseas retirar este modelo y estándar de hardware de tu catálogo?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition text-xs font-extrabold inline-flex items-center gap-1">
                                                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        <span>Retirar</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($models->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                            {{ $models->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
