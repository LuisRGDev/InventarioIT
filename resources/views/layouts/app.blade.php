<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 selection:bg-middleby-500 selection:text-white overflow-hidden">
        <div class="flex h-screen bg-gradient-to-br from-slate-100 via-sky-50/25 to-slate-100">
            <!-- Sidebar Navigation -->
            <livewire:layout.navigation />

            <!-- Main Content Wrapper -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white/80 backdrop-blur-md shadow-xs border-b border-slate-200/70 z-30">
                        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8 flex items-center justify-between w-full">
                            <div class="flex items-center gap-4 w-full">
                                <!-- Mobile menu button (visible only on mobile, managed by Alpine in navigation component) -->
                                <button @click="$dispatch('open-mobile-menu')" class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                <div class="flex-1 w-full">
                                    {{ $header }}
                                </div>
                            </div>
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
