<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('page-title', 'Traveloka Admin')</title>

        @vite(['resources/css/admin.css', 'resources/js/admin.js'])
        @stack('styles')
    </head>
    <body>
        @php
            $isDashboard = request()->routeIs('admin.dashboard');
        @endphp
        <div class="admin-shell">
            @include('admin.partials.sidebar')

            <div class="admin-sidebar-backdrop" data-sidebar-toggle></div>

            <div class="admin-main">
                @include('admin.partials.header', [
                    'pageTitle' => trim($__env->yieldContent('page-title')) ?: __('common.admin_dashboard'),
                ])

                <main class="admin-content" @if($isDashboard) data-dashboard-stats="true" @endif>
                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>

