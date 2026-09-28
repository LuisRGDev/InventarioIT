<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-bold text-xl text-middleby-900 leading-tight tracking-tight flex items-center gap-2.5">
                <span class="w-2 h-6 bg-gradient-to-b from-amber-500 to-middleby-700 rounded-full inline-block shadow-sm"></span>
                {{ __('Panel de Control') }}
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

    <div class="py-8">

        <x-import-modal name="general-import" title="Importar Inventario General"
                         :action="route('dashboard.import')"
                         :import-route="route('dashboard.import.template')">
            Actualización y carga masiva mediante archivo Excel de 3 pestañas (Equipos Asignados, Stock Almacén y Empleados).
        </x-import-modal>

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
                            {{ session('error') }}
                        </div>
                    </div>
                </div>
            @endif

            <livewire:dashboard-metrics />
        </div>
    </div>
</x-app-layout>
