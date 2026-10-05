<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-black text-2xl text-slate-900 leading-tight tracking-tight flex items-center gap-2.5">
                <span class="w-2.5 h-7 bg-gradient-to-b from-middleby-600 to-amber-500 rounded-full inline-block shadow-sm"></span>
                {{ __('Categorías de Equipos') }}
            </h2>
            <p class="text-xs sm:text-sm font-semibold text-slate-500 mt-1">Categorías disponibles al registrar equipos y modelos.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-2xl shadow-xs">
                    <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-2xl shadow-xs">
                    <p class="text-sm font-bold text-red-900">{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-100 text-xs font-extrabold uppercase text-slate-500 tracking-wider">
                                <th class="py-4 px-6">Categoría</th>
                                <th class="py-4 px-6">Identificador</th>
                                <th class="py-4 px-6 text-right">Equipos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($categories as $category)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-4 px-6 text-sm font-bold text-slate-900">{{ $category->name }}</td>
                                    <td class="py-4 px-6 text-sm font-mono text-slate-500">{{ $category->slug }}</td>
                                    <td class="py-4 px-6 text-sm text-right text-slate-700">{{ $category->devices_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-10 px-6 text-center text-sm text-slate-500">No hay categorías registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $categories->links() }}
        </div>
    </div>
</x-app-layout>
