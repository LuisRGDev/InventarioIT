<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ openMobileMenu: false }" 
     @open-mobile-menu.window="openMobileMenu = true"
     class="flex shrink-0">
     
    <!-- Overlay for mobile -->
    <div x-show="openMobileMenu" 
         @click="openMobileMenu = false"
         x-transition.opacity
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 sm:hidden"></div>

    <!-- Sidebar Container -->
    <div :class="openMobileMenu ? 'translate-x-0' : '-translate-x-full'"
         class="fixed inset-y-0 left-0 w-72 bg-gradient-to-b from-middleby-800 to-middleby-950 text-slate-200 border-r border-middleby-950 shadow-[4px_0_15px_-3px_rgba(0,0,0,0.5)] z-50 flex flex-col transition-transform duration-300 ease-in-out sm:relative sm:translate-x-0 overflow-hidden">
        
        <!-- Subtle Inner Highlight (Skeuomorphism) -->
        <div class="absolute inset-0 pointer-events-none shadow-[inset_1px_1px_2px_rgba(255,255,255,0.1),inset_-1px_-1px_3px_rgba(0,0,0,0.6)]"></div>

        <!-- Logo Area -->
        <div class="relative shrink-0 flex items-center justify-between h-20 px-6 border-b border-middleby-950 shadow-[0_2px_4px_rgba(0,0,0,0.2)] bg-middleby-800">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 transition-transform hover:scale-[1.02] duration-200">
                <div class="bg-white p-1.5 rounded-lg shadow-[inset_0_2px_4px_rgba(0,0,0,0.2)]">
                    <x-application-logo class="block h-8 w-auto fill-current text-middleby-800" />
                </div>
            </a>
            
            <button @click="openMobileMenu = false" class="sm:hidden p-2 text-slate-400 hover:text-white rounded-md shadow-[inset_0_1px_2px_rgba(255,255,255,0.1)] bg-middleby-900/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Scrollable Navigation Links -->
        <div class="relative flex-1 overflow-y-auto px-4 py-6 space-y-1 custom-scrollbar">
            
            <!-- Reusable CSS Classes for Nav Links -->
            @php
                $navItemClasses = "group flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-all duration-200 relative overflow-hidden ";
                
                $activeClasses = "text-white bg-middleby-900 shadow-[inset_0_3px_6px_rgba(0,0,0,0.4),inset_0_-1px_1px_rgba(255,255,255,0.05)] border-l-4 border-amber-500";
                
                $inactiveClasses = "text-slate-300 hover:text-white border-l-4 border-transparent hover:border-slate-500 hover:bg-middleby-700/50 hover:shadow-[0_2px_4px_rgba(0,0,0,0.2)] shadow-[inset_0_1px_1px_rgba(255,255,255,0.05),0_1px_2px_rgba(0,0,0,0.2)] bg-middleby-800/40";
            @endphp

            <a href="{{ route('dashboard') }}" wire:navigate class="{{ $navItemClasses }} {{ request()->routeIs('dashboard') ? $activeClasses : $inactiveClasses }}">
                <span class="w-5 h-5 flex items-center justify-center opacity-80">🏠</span>
                Inicio
            </a>

            <div class="pt-4 pb-1">
                <p class="px-3 text-xs font-bold text-middleby-300 uppercase tracking-wider mb-2 drop-shadow-md">Gestión</p>
                <div class="space-y-1">
                    <a href="{{ route('devices.index') }}" wire:navigate class="{{ $navItemClasses }} {{ request()->routeIs('devices.*') ? $activeClasses : $inactiveClasses }}">
                        <span class="w-5 h-5 flex items-center justify-center opacity-80">💻</span>
                        Equipos
                    </a>
                    
                    <a href="{{ route('employees.index') }}" wire:navigate class="{{ $navItemClasses }} {{ request()->routeIs('employees.*') ? $activeClasses : $inactiveClasses }}">
                        <span class="w-5 h-5 flex items-center justify-center opacity-80">👥</span>
                        Empleados
                    </a>
                    
                    <a href="{{ route('assignments.index') }}" wire:navigate class="{{ $navItemClasses }} {{ request()->routeIs('assignments.*') && !request()->routeIs('assignments.assign') && !request()->routeIs('assignments.return') && !request()->routeIs('assignments.replace') ? $activeClasses : $inactiveClasses }}">
                        <span class="w-5 h-5 flex items-center justify-center opacity-80">📋</span>
                        Asignaciones
                    </a>
                </div>
            </div>

            <!-- Acciones Rápidas Dropdown -->
            <div x-data="{ openAssign: {{ request()->routeIs('assignments.assign', 'assignments.return', 'assignments.replace') ? 'true' : 'false' }} }" class="space-y-1 mb-2">
                <button @click="openAssign = !openAssign" 
                        class="{{ $navItemClasses }} w-full justify-between {{ request()->routeIs('assignments.assign', 'assignments.return', 'assignments.replace') ? 'text-white bg-middleby-900/50' : $inactiveClasses }}">
                    <div class="flex items-center gap-3">
                        <span class="w-5 h-5 flex items-center justify-center text-amber-400">⚡</span>
                        <span>Acciones Rápidas</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180 text-amber-400': openAssign }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div x-show="openAssign" 
                     x-collapse 
                     class="pl-11 pr-3 py-1 space-y-1">
                     
                    @php
                        $subItemClasses = "flex items-center gap-2 px-3 py-2 rounded-md text-xs font-medium transition-all shadow-[inset_0_1px_1px_rgba(255,255,255,0.05),0_1px_2px_rgba(0,0,0,0.1)] ";
                    @endphp
                     
                    <a href="{{ route('assignments.assign') }}" wire:navigate
                       class="{{ $subItemClasses }} {{ request()->routeIs('assignments.assign') ? 'bg-middleby-600 text-white shadow-[inset_0_2px_4px_rgba(0,0,0,0.3)]' : 'bg-middleby-800/80 text-slate-300 hover:text-white hover:bg-middleby-700' }}">
                        <span class="text-blue-300 font-bold text-sm">+</span>
                        Asignar equipo
                    </a>
                    
                    <a href="{{ route('assignments.return') }}" wire:navigate
                       class="{{ $subItemClasses }} {{ request()->routeIs('assignments.return') ? 'bg-amber-700 text-white shadow-[inset_0_2px_4px_rgba(0,0,0,0.3)]' : 'bg-middleby-800/80 text-slate-300 hover:text-amber-400 hover:bg-middleby-700' }}">
                        <span class="text-amber-500 font-bold text-sm">↩</span>
                        Retornar equipo
                    </a>
                    
                    <a href="{{ route('assignments.replace') }}" wire:navigate
                       class="{{ $subItemClasses }} {{ request()->routeIs('assignments.replace') ? 'bg-purple-700 text-white shadow-[inset_0_2px_4px_rgba(0,0,0,0.3)]' : 'bg-middleby-800/80 text-slate-300 hover:text-purple-400 hover:bg-middleby-700' }}">
                        <span class="text-purple-400 font-bold text-sm">⇄</span>
                        Reemplazar equipo
                    </a>
                </div>
            </div>

            <div class="pt-4 pb-1">
                <p class="px-3 text-xs font-bold text-middleby-300 uppercase tracking-wider mb-2 drop-shadow-md">Telefonía</p>
                <div class="space-y-1">
                    <a href="{{ route('phone-lines.index') }}" wire:navigate class="{{ $navItemClasses }} {{ request()->routeIs('phone-lines.*') ? $activeClasses : $inactiveClasses }}">
                        <span class="w-5 h-5 flex items-center justify-center opacity-80">📱</span>
                        Líneas Telefónicas
                    </a>
                    
                    <a href="{{ route('office-extensions.index') }}" wire:navigate class="{{ $navItemClasses }} {{ request()->routeIs('office-extensions.*') ? $activeClasses : $inactiveClasses }}">
                        <span class="w-5 h-5 flex items-center justify-center opacity-80">☎️</span>
                        Extensiones
                    </a>
                </div>
            </div>

            <div class="pt-4 pb-1">
                <p class="px-3 text-xs font-bold text-middleby-300 uppercase tracking-wider mb-2 drop-shadow-md">Configuración</p>
                <div class="space-y-1">
                    <a href="{{ route('maintenances.index') }}" wire:navigate class="{{ $navItemClasses }} {{ request()->routeIs('maintenances.*') ? $activeClasses : $inactiveClasses }}">
                        <span class="w-5 h-5 flex items-center justify-center opacity-80">🔧</span>
                        Mantenimientos
                    </a>
                    
                    <a href="{{ route('device-models.index') }}" wire:navigate class="{{ $navItemClasses }} {{ request()->routeIs('device-models.*') ? $activeClasses : $inactiveClasses }}">
                        <span class="w-5 h-5 flex items-center justify-center opacity-80">🏷️</span>
                        Modelos IT
                    </a>
                    
                    <a href="{{ route('job-positions.index') }}" wire:navigate class="{{ $navItemClasses }} {{ request()->routeIs('job-positions.*') ? $activeClasses : $inactiveClasses }}">
                        <span class="w-5 h-5 flex items-center justify-center opacity-80">💼</span>
                        Puestos
                    </a>
                </div>
            </div>
            
        </div>

        <!-- User Profile Footer -->
        <div class="relative shrink-0 p-4 bg-middleby-950 border-t border-middleby-900 shadow-[inset_0_4px_6px_-2px_rgba(0,0,0,0.5)]">
            <div x-data="{ openProfile: false }" class="relative">
                <button @click="openProfile = !openProfile" 
                        class="flex items-center gap-3 w-full p-2 rounded-lg hover:bg-middleby-800 transition-colors shadow-[0_2px_4px_rgba(0,0,0,0.2),inset_0_1px_1px_rgba(255,255,255,0.05)] border border-middleby-800/50 bg-middleby-900 text-left">
                    <div class="w-9 h-9 rounded-md bg-gradient-to-br from-middleby-600 to-middleby-800 flex items-center justify-center text-white font-bold shadow-[inset_0_1px_2px_rgba(255,255,255,0.2)] border border-middleby-500">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></p>
                        <p class="text-xs text-middleby-300 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <svg class="w-4 h-4 text-middleby-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                </button>

                <!-- Profile Dropdown Menu -->
                <div x-show="openProfile" 
                     @click.outside="openProfile = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute bottom-full left-0 w-full mb-2 bg-middleby-800 rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.5),inset_0_1px_1px_rgba(255,255,255,0.1)] border border-middleby-700 py-1 overflow-hidden z-50">
                    
                    <a href="{{ route('profile') }}" wire:navigate class="block px-4 py-2.5 text-sm text-slate-200 hover:bg-middleby-700 hover:text-white transition-colors">
                        👤 Mi Perfil
                    </a>
                    <div class="border-t border-middleby-900/50 my-1"></div>
                    <button wire:click="logout" class="w-full text-left px-4 py-2.5 text-sm text-amber-400 hover:bg-middleby-700 hover:text-amber-300 transition-colors font-medium">
                        🚪 Cerrar Sesión
                    </button>
                </div>
            </div>
        </div>
    </div>


</nav>
