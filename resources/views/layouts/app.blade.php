<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Traveloka') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 traveloka-gradient min-h-screen">
        @include('components.flash-message')
        <div class="min-h-screen bg-transparent">
            @include('layouts.navigation')

            <header class="bg-white/70 backdrop-blur border-b border-white/60 shadow-sm">
                <div class="max-w-7xl mx-auto py-6 px-6 lg:px-10">
                    <div class="flex items-center justify-between">
                        <div>
                            {{ $header }}
                        </div>
                        <div class="hidden md:flex items-center gap-3">
                            <span class="text-sm text-slate-500">
                                {{ now()->locale(app()->getLocale())->translatedFormat('d M Y') }}
                            </span>
                            <span class="inline-flex items-center gap-2 rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-600">
                                <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                                Live
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <main class="py-10 px-4 sm:px-6 lg:px-10">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
