@php
    $isLowStim = auth()->check() && auth()->user()->low_stimulation_mode;
    $bodyBg    = $isLowStim
        ? 'background:var(--bg);'
        : 'background:radial-gradient(ellipse 70% 55% at 50% 110%, rgb(var(--accent-rgb) / 0.055) 0%, transparent 65%), var(--bg);';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Getting started | CommonGrove' }}</title>
    @include('partials.fonts')
    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen antialiased" style="{{ $bodyBg }}color:var(--text);font-family:var(--font-body);">

    <div class="flex flex-col items-center justify-start px-4 py-10 sm:py-16">

        <div class="mb-8 flex items-center justify-center gap-2">
            <span class="tracking-tight font-display" style="color:var(--text);font-size:1.5rem;font-weight:400;">Common<span style="font-weight:700;-webkit-text-stroke:0.6px var(--text);">Grove</span></span>
            <img src="{{ asset('images/logo-icon.png') }}" alt="" class="h-9 w-auto -ml-6">
            <span class="text-xs px-1.5 py-0.5 rounded font-semibold -ml-4 bg-accent/15 text-accent">BETA</span>
        </div>

        <div class="w-full max-w-xl">
            {{ $slot }}
        </div>

    </div>

    @livewireScripts
</body>
</html>
