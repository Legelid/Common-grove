<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hangout Room — CommonGround</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-950 flex items-center justify-center px-4">
    <div class="text-center space-y-4 max-w-sm">
        <h1 class="text-2xl font-bold text-white">You're in!</h1>
        <p class="text-gray-400 text-sm">
            Messaging coming soon — Phase 6.
        </p>
        <a
            href="{{ route('feed') }}"
            class="inline-block mt-4 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg transition text-sm"
        >
            ← Back to feed
        </a>
    </div>
</body>
</html>
