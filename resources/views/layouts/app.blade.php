<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'School Management System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
        @php $user = auth()->user(); @endphp
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">
            @include('layouts.sidebar')

            <div class="lg:pl-64 flex flex-col min-h-screen">
                @include('layouts.navigation')

                <!-- Page Heading -->
                <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $title ?? 'Dashboard' }}</h1>
                            @isset($subtitle)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $subtitle }}</p>
                            @endisset
                        </div>
                        <div class="flex items-center gap-3">
                            {{ $actions ?? '' }}
                        </div>
                    </div>
                </header>

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                        <div class="rounded-md bg-green-50 dark:bg-green-900/30 p-4 border border-green-200 dark:border-green-800">
                            <div class="flex">
                                <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                        <div class="rounded-md bg-red-50 dark:bg-red-900/30 p-4 border border-red-200 dark:border-red-800">
                            <div class="flex">
                                <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                <main class="flex-1">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
