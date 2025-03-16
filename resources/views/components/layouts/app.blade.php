<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
    @stack('styles')
</head>
<body>
<div id="app" class="bg-zinc-50 dark:text-gray-400 text-gray-800 dark:bg-zinc-900 min-h-screen overflow-hidden">
    <flux:header container class="border-b border-zinc-200 dark:border-zinc-700">
        <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc." class="max-lg:hidden dark:hidden" />
        <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc." class="max-lg:!hidden hidden dark:flex" />

        @include('partials.navbar')
    </flux:header>

    <flux:main container>
        {{ $slot }}
    </flux:main>
</div>
@livewireScripts
@fluxScripts
@stack('scripts')
@persist('toast')
<flux:toast />
@endpersist
</body>

</html>
