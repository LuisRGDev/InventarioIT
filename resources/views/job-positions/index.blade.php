<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-900 leading-tight tracking-tight flex items-center gap-2.5">
                    <span class="w-2.5 h-7 bg-gradient-to-b from-middleby-600 to-amber-500 rounded-full inline-block shadow-sm"></span>
                    {{ __('Catálogo de Puestos y Áreas') }}
                </h2>
                <p class="text-xs sm:text-sm font-semibold text-slate-500 mt-1">Configura las plantillas de puestos (Dirección, Área, Puesto) para evitar errores al dar de alta empleados.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3" x-data>
                <button type="button" @click.stop="$dispatch('open-job-import-modal')" class="px-4 py-2 bg-white text-emerald-700 border border-emerald-200 text-sm font-semibold rounded-xl hover:bg-emerald-50 transition shadow-xs inline-flex items-center gap-2 h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Importar
                </button>
                <a href="{{ route('job-positions.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-middleby-800 to-middleby-700 hover:from-middleby-700 hover:to-middleby-600 text-white text-sm font-black rounded-2xl shadow-md hover:shadow-lg transition-all duration-200 inline-flex items-center gap-2 active:scale-95 group h-[44px]">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-white group-hover:rotate-90 transition-transform duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <span>Registrar Nuevo Puesto</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showImportModal: false }" @open-job-import-modal.window="showImportModal = true" @keydown.escape.window="showImportModal = false">
        
        {{-- Modal Importación --}}
        <div x-show="showImportModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
                <div x-show="showImportModal" @click="showImportModal = false" x-transition.opacity class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showImportModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-100 z-10">
                    
                    <form action="{{ route('job-positions.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white p-6 sm:p-8">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-600 shadow-xs">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="w-full">
                                    <h3 class="text-lg font-bold text-slate-900 leading-6" id="modal-title">Importar Puestos</h3>
                                    <div class="mt-2 text-sm text-slate-500 space-y-3">
                                        <p>Para que el sistema procese correctamente tus puestos, el archivo debe tener las cabeceras exactas (direccion, area, puesto, notas).</p>
                                        <a href="{{ route('job-positions.import.template') }}" class="inline-flex items-center gap-2 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-xl transition w-full justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            Descargar Plantilla de Ejemplo
                                        </a>
                                        
                                        <div class="mt-4 border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-xl p-4 text-center transition">
                                            <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required class="block w-full text-sm text-slate-600 font-medium file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-2 border-t border-slate-100">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl border border-transparent shadow-md px-5 py-2.5 bg-emerald-600 text-sm font-bold text-white hover:bg-emerald-700 transition active:scale-95">
                                Subir e Importar
                            </button>
                            <button type="button" @click="showImportModal = false" class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl border border-slate-300 shadow-xs px-4 py-2.5 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition active:scale-95">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

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
                        <p class="text-sm font-bold text-rose-900">{!! session('error') !!}</p>
                    </div>
                </div>
            @endif

            {{-- Barra de Búsqueda y Filtros --}}
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <form method="GET" action="{{ route('job-positions.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                    <div class="sm:col-span-10">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Buscar por Dirección, Área o Puesto</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ej. Finanzas, RH, Analista..." 
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-middleby-500 focus:border-middleby-500 transition"/>
                            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button type="submit" class="w-full py-2.5 px-4 bg-middleby-700 hover:bg-middleby-800 text-white font-black text-sm rounded-xl transition shadow-sm hover:shadow-md active:scale-95 text-center">
                            Buscar
                        </button>
                        @if(request()->has('search'))
                            <a href="{{ route('job-positions.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-extrabold text-sm" title="Limpiar Filtros">
                                ✕
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla de Puestos --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
                @if($positions->isEmpty())
                    <div class="p-16 text-center">
                        <div class="w-20 h-20 mx-auto rounded-full bg-slate-50 border border-slate-200/60 flex items-center justify-center text-slate-400 mb-4 shadow-inner">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-800">No hay puestos catalogados aún</h3>
                        <p class="text-sm text-slate-500 max-w-md mx-auto mt-2 font-medium">Crea tu primer puesto. Una vez creados, podrán seleccionarse con 1 clic sin cometer errores ortográficos al dar de alta empleados.</p>
                        <div class="mt-6">
                            <a href="{{ route('job-positions.create') }}" class="px-6 py-3 bg-gradient-to-r from-middleby-800 to-middleby-700 hover:from-middleby-700 hover:to-middleby-600 text-white font-extrabold rounded-2xl shadow-md transition inline-flex items-center gap-2">
                                <span>+ Dar de Alta mi Primer Puesto</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-100 text-xs font-extrabold uppercase text-slate-500 tracking-wider">
                                    <th class="py-4 px-6">Dirección / Área</th>
                                    <th class="py-4 px-6">Puesto</th>
                                    <th class="py-4 px-6">Notas</th>
                                    <th class="py-4 px-6 text-center">Empleados</th>
                                    <th class="py-4 px-6 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($positions as $position)
                                    <tr class="hover:bg-slate-50/60 transition duration-150">
                                        {{-- Dirección y Área --}}
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="font-extrabold text-slate-900 text-base">{{ $position->direction }}</div>
                                            <div class="text-xs font-bold text-slate-500 uppercase tracking-wide">{{ $position->area }}</div>
                                        </td>

                                        {{-- Puesto --}}
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                {{ $position->name }}
                                            </span>
                                        </td>

                                        {{-- Notas --}}
                                        <td class="py-4 px-6">
                                            @if($position->notes)
                                                <p class="text-xs text-slate-400 font-medium truncate max-w-xs" title="{{ $position->notes }}">💬 {{ $position->notes }}</p>
                                            @else
                                                <span class="text-xs font-semibold text-slate-400 italic">Sin notas</span>
                                            @endif
                                        </td>

                                        {{-- Contador Empleados --}}
                                        <td class="py-4 px-6 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-2xl text-xs font-black {{ $position->employees_count > 0 ? 'bg-middleby-100 text-middleby-900 border border-middleby-200' : 'bg-slate-100 text-slate-500' }}">
                                                👤 {{ $position->employees_count }} {{ $position->employees_count == 1 ? 'empleado' : 'empleados' }}
                                            </span>
                                        </td>

                                        {{-- Acciones --}}
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('job-positions.edit', $position) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition font-bold text-xs inline-flex items-center gap-1">
                                                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    <span>Editar</span>
                                                </a>
                                                <form action="{{ route('job-positions.destroy', $position) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que deseas retirar este puesto de tu catálogo?');">
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

                    @if($positions->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                            {{ $positions->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
