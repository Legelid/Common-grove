@props(['title' => config('app.name')])

@php
    $isLowStim = auth()->check() && auth()->user()->low_stimulation_mode;
    $bodyBg    = $isLowStim
        ? 'background:#0D1117;'
        : 'background:radial-gradient(ellipse 70% 55% at 50% 110%, rgba(29,158,117,0.055) 0%, transparent 65%), radial-gradient(ellipse 55% 40% at 80% 5%, rgba(8,32,58,0.20) 0%, transparent 55%), #0D1117;';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full overflow-hidden antialiased flex flex-col items-center justify-center px-4 py-12" style="{{ $bodyBg }}color:#E6EDF3;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif;">

    <div class="mb-8 text-center">
        <a href="{{ route('home') }}" class="text-xl font-bold" style="color:#E6EDF3;">CommonGrove</a>
        <span class="ml-2 text-xs px-1.5 py-0.5 rounded font-semibold align-middle" style="background:rgba(29,158,117,0.15);color:#1D9E75;">BETA</span>
    </div>

    <div class="w-full max-w-md rounded-xl border p-8 shadow-2xl" style="background:#161B22;border-color:#30363D;">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
