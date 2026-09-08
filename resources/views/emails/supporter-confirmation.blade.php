@extends('emails.layout')

@section('title', 'Thank you for being a CommonGrove Supporter')
@section('preheader', 'Your subscription is active. Supporter features are now available.')

@section('body')
    <h1 class="cg-h1" style="margin:0 0 8px;font-size:20px;font-weight:600;color:#111827;line-height:1.3;">You're in. Thank you.</h1>

    @if ($gamertag)
        <p class="cg-p" style="margin:0 0 16px;font-size:15px;color:#4b5563;line-height:1.75;">
            Hi {{ $gamertag }},
        </p>
    @endif

    <p class="cg-p" style="margin:0 0 16px;font-size:15px;color:#4b5563;line-height:1.75;">
        Your CommonGrove Supporter subscription is now active. The extra features are available in your profile settings whenever you want them.
    </p>

    <p class="cg-p" style="margin:0 0 28px;font-size:15px;color:#4b5563;line-height:1.75;">
        Supporters are what keep CommonGrove ad-free and independent. That genuinely matters. Thank you.
    </p>

    <table cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:28px;">
        <tr>
            <td style="background:#1D9E75;border-radius:8px;">
                <a href="{{ config('app.url') }}"
                   style="display:inline-block;padding:12px 26px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;letter-spacing:0.01em;">Go to CommonGrove</a>
            </td>
        </tr>
    </table>

    <p class="cg-muted" style="margin:0;font-size:13px;color:#6b7280;line-height:1.7;">
        You can cancel any time through PayPal, no questions asked.
    </p>
@endsection

@section('footer_extra')
    You received this because you became a CommonGrove Supporter.
@endsection
