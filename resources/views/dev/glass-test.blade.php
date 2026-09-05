<!DOCTYPE html>
<html lang="en" data-glass-theme="forest-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Glass panel test (dev only)</title>
    @include('partials.fonts')
    @vite(['resources/css/app.css'])
</head>
<body style="margin:0;min-height:100vh;">

    {{-- Placeholder background — same forest photo used elsewhere on the site,
         so the panels are tested against a realistic busy image rather than a
         flat color, matching how they'll actually be used later. --}}
    <div style="
        position:fixed;
        inset:0;
        background-image:url('{{ asset('images/forest-path.jpg') }}');
        background-size:cover;
        background-position:center 25%;
        z-index:0;
    "></div>

    <div style="position:relative;z-index:1;padding:4rem 2rem;display:flex;flex-direction:column;align-items:center;gap:3rem;">

        <h1 style="color:#F5F0E8;font-family:'Source Sans 3',system-ui,sans-serif;">Glass panel — dev test (not linked from nav)</h1>

        <x-glass-panel tier="light" style="padding:2rem;border-radius:16px;max-width:32rem;">
            <p style="color:#F5F0E8;font-family:'Source Sans 3',system-ui,sans-serif;margin:0;">
                <strong>tier="light"</strong> — uses <code>--glass-blur-light</code> (8px). Meant for nav bars, hero cards, decorative elements.
            </p>
        </x-glass-panel>

        <x-glass-panel tier="heavy" style="padding:2rem;border-radius:16px;max-width:32rem;">
            <p style="color:#F5F0E8;font-family:'Source Sans 3',system-ui,sans-serif;margin:0;">
                <strong>tier="heavy"</strong> — uses <code>--glass-blur-heavy</code> (16px). Meant for dense text areas, lists.
            </p>
        </x-glass-panel>

        <x-glass-panel style="padding:2rem;border-radius:16px;max-width:32rem;">
            <p style="color:#F5F0E8;font-family:'Source Sans 3',system-ui,sans-serif;margin:0;">
                No <code>tier</code> passed — should default to <strong>light</strong>.
            </p>
        </x-glass-panel>

    </div>

</body>
</html>
