<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify your CommonGrove email</title>
</head>
<body style="margin:0;padding:0;background:#f0f2f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f2f5;padding:48px 20px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:#0D1117;padding:28px 40px 24px;border-radius:12px 12px 0 0;">
                            <p style="margin:0;font-size:17px;font-weight:700;color:#E6EDF3;letter-spacing:-0.01em;">CommonGrove</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="background:#ffffff;padding:36px 40px 4px;">
                            <h1 style="margin:0 0 20px;font-size:21px;font-weight:600;color:#111827;line-height:1.3;">Welcome to CommonGrove</h1>
                            <p style="margin:0 0 14px;font-size:15px;color:#4b5563;line-height:1.75;">Before you can chat or create rooms, please verify your email address.</p>
                            <p style="margin:0 0 28px;font-size:15px;color:#4b5563;line-height:1.75;">This helps keep CommonGrove safer from spam, fake accounts, and bots.</p>
                        </td>
                    </tr>

                    {{-- Button --}}
                    <tr>
                        <td style="background:#ffffff;padding:0 40px 28px;">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background:#1D9E75;border-radius:8px;">
                                        <a href="{{ $url }}"
                                           style="display:inline-block;padding:13px 28px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;letter-spacing:0.01em;">Verify my email</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Closing copy --}}
                    <tr>
                        <td style="background:#ffffff;padding:0 40px 28px;">
                            <p style="margin:0 0 14px;font-size:15px;color:#4b5563;line-height:1.75;">Take your time — we'll be here.</p>
                            <p style="margin:0;font-size:13px;color:#9ca3af;line-height:1.65;">If you didn't create a CommonGrove account, you can ignore this email.</p>
                        </td>
                    </tr>

                    {{-- URL fallback --}}
                    <tr>
                        <td style="background:#ffffff;padding:0 40px 36px;border-radius:0 0 12px 12px;">
                            <p style="margin:0 0 5px;font-size:12px;color:#9ca3af;">If the button doesn't work, paste this link into your browser:</p>
                            <p style="margin:0;font-size:11px;color:#1D9E75;word-break:break-all;">{{ $url }}</p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px 40px 0;">
                            <p style="margin:0;font-size:12px;color:#9ca3af;">&copy; {{ date('Y') }} CommonGrove &mdash; A quiet place to connect.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
