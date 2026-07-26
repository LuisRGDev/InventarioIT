<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-middleby-900 leading-tight tracking-tight flex items-center gap-2.5">
                <span class="w-2 h-6 bg-gradient-to-b from-amber-500 to-middleby-700 rounded-full inline-block shadow-sm"></span>
                {{ __('Panel de Control · Inventario IT') }}
            </h2>
            <div class="text-xs font-semibold text-slate-600 bg-slate-100 px-3.5 py-1.5 rounded-full border border-slate-200/80 shadow-xs flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block shadow-sm animate-pulse"></span>
                <span>Sistema <span class="text-middleby-700 font-extrabold">IT Inventario</span></span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:dashboard-metrics />
        </div>
    </div>
</x-app-layout>

