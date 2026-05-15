<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Getting started — CommonGround' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full antialiased flex flex-col" style="background:#0D1117;color:#E6EDF3;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif;">

    <div class="flex-1 flex flex-col items-center justify-start px-4 py-10 sm:py-16">

        <div class="mb-8 text-center">
            <span class="text-lg font-bold" style="color:#E6EDF3;">CommonGround</span>
            <span class="ml-2 text-xs px-1.5 py-0.5 rounded font-semibold align-middle" style="background:rgba(29,158,117,0.15);color:#1D9E75;">BETA</span>
        </div>

        <div class="w-full max-w-xl">
            {{ $slot }}
        </div>

    </div>

    @livewireScripts
</body>
</html>
