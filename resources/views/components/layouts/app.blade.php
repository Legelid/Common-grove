@props(['title' => config('app.name')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full antialiased flex flex-col items-center justify-center px-4 py-12" style="background:#0D1117;color:#E6EDF3;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif;">

    <div class="mb-8 text-center">
        <a href="{{ route('home') }}" class="text-xl font-bold" style="color:#E6EDF3;">CommonGround</a>
        <span class="ml-2 text-xs px-1.5 py-0.5 rounded font-semibold align-middle" style="background:rgba(29,158,117,0.15);color:#1D9E75;">BETA</span>
    </div>

    <div class="w-full max-w-md rounded-xl border p-8 shadow-2xl" style="background:#161B22;border-color:#30363D;">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
