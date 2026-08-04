<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Extensiones Telefónicas
            </h2>
            <div class="flex gap-3">
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

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                                        <a href="{{ route('office-extensions.edit', $ext) }}" class="inline-flex items-center justify-center p-2 text-gray-400 hover:text-indigo-600 bg-white rounded-lg hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition shadow-sm opacity-0 group-hover:opacity-100 focus:opacity-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
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
