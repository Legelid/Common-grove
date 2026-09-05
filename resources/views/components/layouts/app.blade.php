@props(['title' => config('app.name')])

@php
    $isLowStim = auth()->check() && auth()->user()->low_stimulation_mode;
    $bodyBg    = $isLowStim
        ? 'background:var(--bg);'
        : 'background:radial-gradient(ellipse 70% 55% at 50% 110%, rgb(var(--accent-rgb) / 0.055) 0%, transparent 65%), var(--bg);';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @include('partials.fonts')
    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full overflow-hidden antialiased flex flex-col items-center justify-center px-4 py-12" style="{{ $bodyBg }}color:var(--text);font-family:var(--font-body);">

    <div class="mb-8 text-center">
        <a href="{{ route('home') }}" class="font-display text-xl font-bold" style="color:var(--text);">CommonGrove</a>
        <span class="ml-2 text-xs px-1.5 py-0.5 rounded font-semibold align-middle bg-accent/15 text-accent">BETA</span>
    </div>

    <x-card padding="p-8" class="w-full max-w-md" style="border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);">
        {{ $slot }}
    </x-card>

    @livewireScripts
</body>
</html>
