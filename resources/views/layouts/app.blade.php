<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    {!! NoCaptcha::renderJs() !!}
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

   <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="{{ asset('css/app_reservas.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">

    <!-- Scripts (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen">
        {{-- navegación --}}
        @includeIf('layouts.navigation')

        {{-- Page Heading (opcional en vistas que definan $header) --}}
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        @php
            $isAdmin = auth()->check() && (method_exists(auth()->user(), 'isAdmin') ? auth()->user()->isAdmin() : (isset(auth()->user()->role) && auth()->user()->role === 'admin'));
        @endphp

        <main class="{{ $isAdmin ? 'admin-page' : (request()->is('profile') ? 'profile-page' : 'page-bg') }}">
            {{-- Compatibilidad: si la vista pasó $slot (component), lo mostramos; si no, usamos @yield('content') --}}
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>
    </div>
</body>
</html>
