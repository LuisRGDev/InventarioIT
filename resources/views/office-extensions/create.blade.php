<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('office-extensions.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agregar Extensión Telefónica</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <form method="POST" action="{{ route('office-extensions.store') }}" class="p-6 space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <x-input-label for="extension_number" value="Número de Extensión *"/>
                            <x-text-input id="extension_number" name="extension_number" type="text" class="mt-1 block w-full"
                                          value="{{ old('extension_number') }}" required/>
                            <x-input-error :messages="$errors->get('extension_number')" class="mt-1"/>
                        </div>
                        <div>
                            <x-input-label for="direct_number" value="Número Directo (Opcional)"/>
                            <x-text-input id="direct_number" name="direct_number" type="text" class="mt-1 block w-full"
                                          value="{{ old('direct_number') }}"/>
                            <x-input-error :messages="$errors->get('direct_number')" class="mt-1"/>
                        </div>
                        <div>
                            <x-input-label for="status" value="Estatus *"/>
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                @foreach($statuses as $s)
                                    <option value="{{ $s->value }}" {{ old('status') == $s->value ? 'selected' : '' }}>
                                        {{ $s->label() }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-1"/>
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="notes" value="Notas / Observaciones"/>
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-1"/>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('office-extensions.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancelar</a>
                        <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
