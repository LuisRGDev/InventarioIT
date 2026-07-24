<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('employees.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agregar Empleado</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <p class="text-sm text-gray-500">Sigue los pasos para registrar y asignar equipos al nuevo empleado.</p>
                </div>

                <div class="p-0">
                    @livewire('employee-onboarding-wizard')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
