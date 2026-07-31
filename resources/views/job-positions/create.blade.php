<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <a href="{{ route('job-positions.index') }}" class="text-sm font-bold text-middleby-600 hover:text-middleby-800 mb-2 inline-flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver al Catálogo
                </a>
                <h2 class="font-black text-2xl text-slate-900 leading-tight tracking-tight flex items-center gap-2.5">
                    <span class="w-2.5 h-7 bg-gradient-to-b from-emerald-500 to-teal-500 rounded-full inline-block shadow-sm"></span>
                    {{ __('Registrar Nuevo Puesto') }}
                </h2>
                <p class="text-xs sm:text-sm font-semibold text-slate-500 mt-1">Define la Dirección, Área y Nombre del Puesto.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
                <form action="{{ route('job-positions.store') }}" method="POST" class="p-6 sm:p-10 space-y-8">
                    @csrf

                    <div>
                        <h3 class="text-lg font-black text-slate-900 border-b border-slate-100 pb-3 mb-6 flex items-center gap-2">
                            <span class="text-2xl">🏢</span> Información del Puesto
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <x-input-label for="direction" :value="__('Dirección (Ej. Finanzas, Operaciones)')" class="font-extrabold text-slate-700" />
                                <x-text-input id="direction" name="direction" type="text" class="mt-2 block w-full bg-slate-50/50 focus:bg-white" :value="old('direction')" required autofocus />
                                <x-input-error class="mt-2 font-bold" :messages="$errors->get('direction')" />
                            </div>

                            <div>
                                <x-input-label for="area" :value="__('Área (Ej. Recursos Humanos, IT)')" class="font-extrabold text-slate-700" />
                                <x-text-input id="area" name="area" type="text" class="mt-2 block w-full bg-slate-50/50 focus:bg-white" :value="old('area')" required />
                                <x-input-error class="mt-2 font-bold" :messages="$errors->get('area')" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="name" :value="__('Nombre del Puesto (Ej. Analista de RH)')" class="font-extrabold text-slate-700" />
                                <x-text-input id="name" name="name" type="text" class="mt-2 block w-full bg-slate-50/50 focus:bg-white text-lg font-bold" :value="old('name')" required />
                                <x-input-error class="mt-2 font-bold" :messages="$errors->get('name')" />
                            </div>

                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-black text-slate-900 border-b border-slate-100 pb-3 mb-6 flex items-center gap-2">
                            <span class="text-2xl">📝</span> Notas e Información Adicional
                        </h3>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="notes" :value="__('Notas Generales del Puesto (Opcional)')" class="font-extrabold text-slate-700" />
                                <textarea id="notes" name="notes" rows="3" class="mt-2 block w-full border-slate-200 focus:border-middleby-500 focus:ring-middleby-500 rounded-xl shadow-sm text-sm font-medium bg-slate-50/50 focus:bg-white transition">{{ old('notes') }}</textarea>
                                <p class="mt-2 text-xs font-semibold text-slate-400">Puedes agregar información relevante sobre las funciones, ubicaciones u otras observaciones.</p>
                                <x-input-error class="mt-2 font-bold" :messages="$errors->get('notes')" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-4">
                        <a href="{{ route('job-positions.index') }}" class="px-5 py-2.5 text-sm font-black text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-middleby-800 to-middleby-700 hover:from-middleby-700 hover:to-middleby-600 text-white font-black text-sm rounded-xl shadow-md hover:shadow-lg transition active:scale-95 flex items-center gap-2">
                            <span>💾</span> Registrar Puesto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
