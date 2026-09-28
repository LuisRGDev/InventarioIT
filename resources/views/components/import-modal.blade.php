@props([
    'name',
    'title',
    'action',
    'importRoute' => null,
    'templateLabel' => 'Descargar Plantilla Excel de Ejemplo (.xlsx)',
])

<div x-data="{
        show{{ $name }}: false,
        file: null,
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)].filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
     }"
     @open-{{ $name }}-modal.window="show{{ $name }} = true; file = null; setTimeout(() => firstFocusable().focus(), 100)"
     @keydown.escape.window="show{{ $name }} = false"
     x-on:reset-file.window="file = null">

    <div x-show="show{{ $name }}" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true"
         x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
         x-on:keydown.shift.tab.prevent="prevFocusable().focus()">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">

            <div x-show="show{{ $name }}"
                 @click="show{{ $name }} = false"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="show{{ $name }}"
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-slate-100 z-10">
                
                <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white p-6 sm:p-8">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 flex items-center justify-center h-14 w-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            </div>
                            <div class="w-full">
                                <h3 class="text-xl font-black text-slate-900 leading-tight" id="modal-title">{{ $title }}</h3>
                                <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">{{ $slot }}</p>
                                
                                @if($importRoute)
                                <div class="mt-4 p-4 bg-emerald-50/70 rounded-2xl border border-emerald-200 text-xs text-emerald-900 font-medium space-y-2">
                                    <p>✨ <strong>¿Cómo funciona?</strong> Puedes modificar un excel previamente exportado o usar la plantilla oficial. El sistema reconcilia y actualiza automáticamente registros existentes por su número de serie sin duplicados.</p>
                                    <a href="{{ $importRoute }}" class="inline-flex items-center justify-center gap-2 font-bold text-emerald-800 bg-white hover:bg-emerald-100 px-3.5 py-2 rounded-xl transition border border-emerald-300 shadow-xs w-full mt-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <span>{{ $templateLabel }}</span>
                                    </a>
                                </div>
                                @endif
                                
                                <div class="mt-5">
                                    <label for="import-file-{{ $name }}" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Selecciona el archivo Excel de tu computadora</label>
                                    <div class="border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-5 text-center bg-slate-50/60 hover:bg-emerald-50/10 transition duration-200">
                                        <input type="file" id="import-file-{{ $name }}" name="file" accept=".xlsx,.xls,.csv" required class="block w-full text-sm text-slate-700 font-medium file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer transition"/>
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
                        <button type="button" @click="show{{ $name }} = false" class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl border border-slate-300 shadow-xs px-5 py-3 bg-white text-sm font-bold text-slate-700 hover:bg-slate-50 transition active:scale-95">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
