<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service — CommonGround</title>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0D1117] antialiased pt-16 min-h-full">

    <header class="fixed top-0 inset-x-0 z-50 h-16 bg-[#161B22] border-b border-[#30363D] flex items-center px-6">
        <div class="flex items-center flex-1">
            <a href="{{ route('home') }}" class="text-[#E6EDF3] font-bold text-lg tracking-tight hover:text-[#1D9E75] transition-colors">CommonGround</a>
            <span class="bg-[#D29922] text-black text-xs font-bold px-2 py-0.5 rounded-full ml-2">BETA</span>
        </div>
    </header>

    <main class="flex items-center justify-center py-20 px-6">
        <div class="w-full max-w-2xl bg-[#161B22] border border-[#30363D] rounded-2xl p-10">
            <h1 class="text-2xl font-bold text-[#E6EDF3] mb-4">Terms of Service</h1>
            <p class="text-[#8B949E] mb-8">Our terms of service are being finalised. Check back soon.</p>
            <a href="{{ route('home') }}" class="text-sm text-[#1D9E75] hover:text-[#22B88A] transition-colors">&larr; Back to home</a>
        </div>
    </main>

    @livewireScripts
</body>
</html>
