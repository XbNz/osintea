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
    @fluxStyles
    @stack('styles')
</head>
<body>

<div id="app" class="bg-zinc-50 dark:text-gray-400 text-gray-800 dark:bg-zinc-900 min-h-screen overflow-hidden">
    <div style="height: 30px; -webkit-app-region: drag;" class="absolute top-0 left-0 right-0"></div>
    <flux:main container class="mt-3">
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
