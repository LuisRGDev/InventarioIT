<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-bold text-xl text-middleby-900 leading-tight tracking-tight flex items-center gap-2.5">
                <span class="w-2 h-6 bg-gradient-to-b from-amber-500 to-middleby-700 rounded-full inline-block shadow-sm"></span>
                {{ __('Panel de Control · Inventario IT') }}
            </h2>
            <div class="flex flex-wrap items-center gap-3" x-data>
                <button type="button" @click.stop="$dispatch('open-general-import-modal')" class="px-4 py-2 bg-white text-emerald-700 border border-emerald-200 hover:bg-emerald-50 text-sm font-bold rounded-xl shadow-sm hover:shadow-md transition-all duration-200 inline-flex items-center gap-2 active:scale-95 group">
                    <div class="w-6 h-6 rounded-lg bg-emerald-100/80 flex items-center justify-center text-emerald-700 group-hover:-translate-y-0.5 transition-transform duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    </div>
                    <span>Importar Inventario</span>
                </button>

                <a href="{{ route('dashboard.export') }}" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white text-sm font-bold rounded-xl shadow-sm hover:shadow-md transition-all duration-200 inline-flex items-center gap-2.5 border border-emerald-500/40 active:scale-95 group">
                    <div class="w-6 h-6 rounded-lg bg-white/15 flex items-center justify-center text-emerald-100 group-hover:rotate-6 transition-transform duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span>Exportar Todo el Inventario</span>
                </a>

                <div class="hidden lg:flex text-xs font-semibold text-slate-600 bg-slate-100 px-3.5 py-2 rounded-xl border border-slate-200/80 shadow-xs items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block shadow-sm animate-pulse"></span>
                    <span>Sistema <span class="text-middleby-700 font-extrabold">IT Inventario</span></span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showImportModal: false }" @open-general-import-modal.window="showImportModal = true" @keydown.escape.window="showImportModal = false">
        
        {{-- Modal Importación General --}}
        <div x-show="showImportModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
                
                {{-- Fondo Oscuro Backdrop con evento de click para cerrar --}}
                <div x-show="showImportModal" 
                     @click="showImportModal = false" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Contenedor Principal de la Modal --}}
                <div x-show="showImportModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-slate-100 z-10">
                    
                    <form action="{{ route('dashboard.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white p-6 sm:p-8">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 flex items-center justify-center h-14 w-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                </div>
                                <div class="w-full">
                                    <h3 class="text-xl font-black text-slate-900 leading-tight" id="modal-title">Importar Inventario General</h3>
                                    <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">Actualización y carga masiva mediante archivo Excel de 3 pestañas (Equipos Asignados, Stock Almacén y Empleados).</p>
                                    
                                    <div class="mt-4 p-4 bg-emerald-50/70 rounded-2xl border border-emerald-200 text-xs text-emerald-900 font-medium space-y-2">
                                        <p>✨ <strong>¿Cómo funciona?</strong> Puedes modificar un excel previamente exportado o usar la plantilla oficial. El sistema reconcilia y actualiza automáticamente registros existentes por su número de serie sin duplicados.</p>
                                        <a href="{{ route('dashboard.import.template') }}" class="inline-flex items-center justify-center gap-2 font-bold text-emerald-800 bg-white hover:bg-emerald-100 px-3.5 py-2 rounded-xl transition border border-emerald-300 shadow-xs w-full mt-2">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <span>Descargar Plantilla Excel de Ejemplo (.xlsx)</span>
                                        </a>
                                    </div>
                                    
                                    <div class="mt-5">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Selecciona el archivo Excel de tu computadora</label>
                                        <div class="border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-5 text-center bg-slate-50/60 hover:bg-emerald-50/10 transition duration-200">
                                            <input type="file" name="file" id="general_file" accept=".xlsx,.xls,.csv" required class="block w-full text-sm text-slate-700 font-medium file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer transition"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-6 py-4 sm:px-8 flex flex-col sm:flex-row-reverse gap-3 border-t border-slate-100">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl border border-transparent shadow-md px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-700 text-sm font-black text-white hover:from-emerald-700 hover:to-teal-800 transition active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>Subir y Procesar Excel</span>
                            </button>
                            <button type="button" @click="showImportModal = false" class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl border border-slate-300 shadow-xs px-5 py-3 bg-white text-sm font-bold text-slate-700 hover:bg-slate-50 transition active:scale-95">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-xs flex items-center justify-between animate-fade-in">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-xs animate-fade-in">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                        <div class="text-sm text-rose-800 font-medium">
                            {!! session('error') !!}
                        </div>
                    </div>
                </div>
            @endif

            <livewire:dashboard-metrics />
        </div>
    </div>
</x-app-layout>
