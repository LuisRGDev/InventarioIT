<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Extensiones Telefónicas
            </h2>
            <div class="flex flex-wrap gap-3" x-data>
                <button type="button" @click.stop="$dispatch('open-extension-import-modal')" class="px-4 py-2 bg-white text-emerald-700 border border-emerald-200 text-sm font-semibold rounded-xl hover:bg-emerald-50 transition shadow-xs inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Importar
                </button>
                <a href="{{ route('office-extensions.export') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-50 transition shadow-sm hover:shadow-md inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Exportar Excel
                </a>
                <a href="{{ route('office-extensions.create') }}" class="px-4 py-2 bg-gradient-to-r from-middleby-800 to-middleby-700 text-white text-sm font-bold rounded-xl hover:from-middleby-700 hover:to-middleby-600 transition shadow-sm hover:shadow-md inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nueva Extensión
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showImportModal: false }" @open-extension-import-modal.window="showImportModal = true" @keydown.escape.window="showImportModal = false">
        
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
                    
                    <form action="{{ route('office-extensions.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white p-6 sm:p-8">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-600 shadow-xs">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="w-full">
                                    <h3 class="text-lg font-bold text-slate-900 leading-6" id="modal-title">Importar Extensiones</h3>
                                    <div class="mt-2 text-sm text-slate-500 space-y-3">
                                        <p>Para procesar las extensiones, el archivo debe tener las cabeceras: numero_de_extension, numero_directo, estatus, correo.</p>
                                        <a href="{{ route('office-extensions.import.template') }}" class="inline-flex items-center gap-2 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-xl transition w-full justify-center">
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

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-2xl shadow-xs flex items-center justify-between animate-fade-in">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0 font-extrabold">✓</div>
                        <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-2xl shadow-xs flex items-center justify-between animate-fade-in">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0 font-extrabold">!</div>
                        <p class="text-sm font-bold text-rose-900">{!! session('error') !!}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <form method="GET" action="{{ route('office-extensions.index') }}" class="relative w-full sm:max-w-xs">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por número o directo..." class="w-full pl-10 pr-4 py-2 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Extensión</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Número Directo</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Estatus</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Asignada A</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($extensions as $ext)
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">Ext. {{ $ext->extension_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $ext->direct_number ?: 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $ext->status->color() }}">
                                            {{ $ext->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        @if($ext->currentAssignment && $ext->currentAssignment->employee)
                                            <a href="{{ route('employees.show', $ext->currentAssignment->employee) }}" class="font-medium text-indigo-600 hover:text-indigo-900 hover:underline">
                                                {{ $ext->currentAssignment->employee->name }}
                                            </a>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $ext->currentAssignment->employee->department }}</div>
                                        @else
                                            <span class="text-gray-400 italic">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 focus-within:opacity-100 transition-opacity">
                                            <a href="{{ route('office-extensions.edit', $ext) }}" class="inline-flex items-center justify-center p-2 text-gray-400 hover:text-indigo-600 bg-white rounded-lg hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition shadow-sm" title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                            <form action="{{ route('office-extensions.destroy', $ext) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta extensión? Se borrará su historial de forma permanente.');" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center p-2 text-gray-400 hover:text-red-600 bg-white rounded-lg hover:bg-red-50 border border-transparent hover:border-red-100 transition shadow-sm" title="Eliminar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900">No hay extensiones</h3>
                                        <p class="text-sm text-gray-500 mt-1">Comienza agregando tu primera extensión al inventario.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($extensions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $extensions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
