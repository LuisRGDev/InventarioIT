<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('phone-lines.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Registrar Nueva Línea Telefónica') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <form action="{{ route('phone-lines.store') }}" method="POST">
                    @csrf
                    
                    <div class="p-6 sm:p-8 space-y-8">
                        {{-- Información de la línea --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Información de la Línea</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <x-input-label for="number" value="Número Telefónico *" />
                                    <x-text-input id="number" name="number" type="text" class="mt-1 block w-full font-mono"
                                                  value="{{ old('number') }}" required placeholder="Ej. 55 1234 5678" />
                                    <x-input-error :messages="$errors->get('number')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="status" value="Estatus Inicial *" />
                                    <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        @foreach(App\Enums\PhoneLineStatus::cases() as $status)
                                            @if($status->value !== 'asignada')
                                                <option value="{{ $status->value }}" {{ old('status', 'disponible') == $status->value ? 'selected' : '' }}>
                                                    {{ $status->label() }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="data_plan" value="Plan de Datos" />
                                    <x-text-input id="data_plan" name="data_plan" type="text" class="mt-1 block w-full"
                                                  value="{{ old('data_plan') }}" placeholder="Ej. Plan Telcel Max 1000" />
                                    <x-input-error :messages="$errors->get('data_plan')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="plan_cost" value="Costo del Plan" />
                                    <x-text-input id="plan_cost" name="plan_cost" type="number" step="0.01" class="mt-1 block w-full"
                                                  value="{{ old('plan_cost') }}" placeholder="Ej. 499.00" />
                                    <x-input-error :messages="$errors->get('plan_cost')" class="mt-1" />
                                </div>
                            </div>
                        </div>

                        {{-- Notas --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Notas (Opcional)</h3>
                            <div>
                                <textarea id="notes" name="notes" rows="3"
                                          class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                          placeholder="Añade observaciones generales sobre la línea...">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 flex items-center justify-end border-t border-gray-100">
                        <a href="{{ route('phone-lines.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Registrar Línea
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
