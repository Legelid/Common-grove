<!DOCTYPE html>
<html lang="en" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <!--[if mso]><noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript><![endif]-->
    <title>@yield('title', 'CommonGrove')</title>
    <style>
        @media (prefers-color-scheme: dark) {
            .cg-outer  { background:#111827 !important; }
            .cg-card   { background:#1C2333 !important; border-color:#30363D !important; }
            .cg-body   { background:#161B22 !important; }
            .cg-footer { background:#0D1117 !important; border-color:#21262D !important; }
            .cg-h1     { color:#E6EDF3 !important; }
            .cg-p      { color:#C9D1D9 !important; }
            .cg-muted  { color:#8B949E !important; }
            .cg-divider{ border-color:#30363D !important; }
            .cg-pre    { background:#0D1117 !important; border-color:#30363D !important; color:#C9D1D9 !important; }
        }
    </style>
</head>
<body class="cg-outer" style="margin:0;padding:0;background:#f0f2f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">

    {{-- Preheader: hidden preview text shown in inbox list --}}
    <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">@yield('preheader', 'A message from CommonGrove.')&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;</div>

    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f0f2f5;">
        <tr>
            <td align="center" style="padding:40px 16px 48px;">

                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:560px;">

                    {{-- ── Header ───────────────────────────────────────────────── --}}
                    <tr>
                        <td class="cg-card" style="background:#0D1117;padding:22px 32px 0;border-radius:12px 12px 0 0;">
                            <p style="margin:0;font-size:16px;font-weight:700;color:#E6EDF3;letter-spacing:-0.02em;">CommonGrove</p>
                            <div style="margin-top:16px;height:1px;background:#1D9E75;opacity:0.3;"></div>
                        </td>
                    </tr>

                    {{-- ── Body ─────────────────────────────────────────────────── --}}
                    <tr>
                        <td class="cg-body" style="background:#ffffff;padding:32px 32px 36px;">
                            @yield('body')
                        </td>
                    </tr>

                    {{-- ── Footer ───────────────────────────────────────────────── --}}
                    <tr>
                        <td class="cg-footer" style="background:#f9fafb;padding:16px 32px 20px;border-radius:0 0 12px 12px;border-top:1px solid #e5e7eb;">
                            <p class="cg-muted" style="margin:0;font-size:11px;color:#9ca3af;line-height:1.7;">
                                &copy; {{ date('Y') }} CommonGrove &mdash; A quiet place to connect.<br>
                                @yield('footer_extra')
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
