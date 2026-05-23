@php
    $isLowStim = auth()->check() && auth()->user()->low_stimulation_mode;
    $bodyBg    = $isLowStim
        ? 'background:#0D1117;'
        : 'background:radial-gradient(ellipse 70% 55% at 50% 110%, rgba(29,158,117,0.055) 0%, transparent 65%), radial-gradient(ellipse 55% 40% at 80% 5%, rgba(8,32,58,0.20) 0%, transparent 55%), #0D1117;';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Getting started — CommonGrove' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen antialiased" style="{{ $bodyBg }}color:#E6EDF3;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif;">

    <div class="flex flex-col items-center justify-start px-4 py-10 sm:py-16">

        <div class="mb-8 flex items-center justify-center gap-2">
            <span class="tracking-tight" style="color:#E6EDF3;font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:400;">Common<span style="font-weight:700;-webkit-text-stroke:0.6px #E6EDF3;">Grove</span></span>
            <img src="{{ asset('images/logo-icon.png') }}" alt="" class="h-9 w-auto -ml-6">
            <span class="text-xs px-1.5 py-0.5 rounded font-semibold -ml-4" style="background:rgba(29,158,117,0.15);color:#1D9E75;">BETA</span>
        </div>

        <div class="w-full max-w-xl">
            {{ $slot }}
        </div>

    </div>

    @livewireScripts
</body>
</html>
