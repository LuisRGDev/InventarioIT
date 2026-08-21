<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('phone-lines.show', $phoneLine) }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Historial de Asignaciones:') }} {{ $phoneLine->number }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-700">Bitácora de Movimientos</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        {{ $assignments->count() }} registros
                    </span>
                </div>

                <div class="p-6">
                    @if($assignments->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Sin historial</h3>
                            <p class="mt-1 text-sm text-gray-500">Esta línea nunca ha sido asignada a ningún colaborador.</p>
                        </div>
                    @else
                        <div class="relative border-l-2 border-indigo-100 ml-4 space-y-8 pb-4">
                            @foreach($assignments as $index => $assignment)
                                <div class="relative pl-8">
                                    {{-- Círculo del timeline --}}
                                    <div class="absolute -left-[9px] top-1 h-4 w-4 rounded-full border-2 border-white {{ $assignment->returned_at ? 'bg-gray-300' : 'bg-green-500 shadow-[0_0_0_4px_rgba(34,197,94,0.2)]' }}"></div>

                                    <div class="bg-white border {{ $assignment->returned_at ? 'border-gray-200' : 'border-green-200 ring-1 ring-green-50' }} rounded-xl p-5 shadow-sm hover:shadow-md transition">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                                            <div class="flex items-start gap-3">
                                                <div class="w-10 h-10 rounded-full {{ $assignment->returned_at ? 'bg-gray-100' : 'bg-green-100' }} flex items-center justify-center flex-shrink-0">
                                                    <span class="{{ $assignment->returned_at ? 'text-gray-600' : 'text-green-700' }} font-bold text-sm">
                                                        {{ strtoupper(substr($assignment->employee->name, 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-gray-900">
                                                        {{ $assignment->employee->name }}
                                                        @if(!$assignment->returned_at)
                                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-wider">
                                                                Actual
                                                            </span>
                                                        @endif
                                                    </h4>
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        {{ $assignment->employee->department }} • {{ $assignment->employee->position }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex flex-row sm:flex-col items-center sm:items-end gap-2 sm:gap-1 text-sm">
                                                <div class="flex items-center text-gray-500">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    Asignado: <span class="font-medium text-gray-900 ml-1">{{ $assignment->assigned_at->translatedFormat('d M, Y') }}</span>
                                                </div>
                                                @if($assignment->returned_at)
                                                    <div class="flex items-center text-gray-500">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Devuelto: <span class="font-medium text-gray-900 ml-1">{{ $assignment->returned_at->translatedFormat('d M, Y') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @if($assignment->notes)
                                                <div class="md:col-span-2">
                                                    <h5 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-1">Notas de Asignación</h5>
                                                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100">{{ $assignment->notes }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
