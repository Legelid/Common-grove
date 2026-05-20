@extends('emails.layout')

@section('title', 'Reset your CommonGrove password')
@section('preheader', 'A password reset was requested for your account.')

@section('body')
    <h1 class="cg-h1" style="margin:0 0 8px;font-size:20px;font-weight:600;color:#111827;line-height:1.3;">Reset your password.</h1>
    <p class="cg-p" style="margin:0 0 24px;font-size:15px;color:#4b5563;line-height:1.75;">
        Someone (hopefully you) requested a password reset for this account. Click the button below to choose a new password.
    </p>

    <table cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:28px;">
        <tr>
            <td style="background:#1D9E75;border-radius:8px;">
                <a href="{{ $url }}"
                   style="display:inline-block;padding:12px 26px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;letter-spacing:0.01em;">Reset my password</a>
            </td>
        </tr>
    </table>

    <p class="cg-muted" style="margin:0 0 8px;font-size:13px;color:#6b7280;line-height:1.7;">
        This link expires in 60 minutes. If you didn't request a reset, your password hasn't changed and you don't need to do anything.
    </p>

    <div class="cg-divider" style="margin:24px 0;border-top:1px solid #f3f4f6;"></div>

    <p class="cg-muted" style="margin:0 0 4px;font-size:11px;color:#9ca3af;">If the button doesn't work, paste this link:</p>
    <p style="margin:0;font-size:11px;color:#1D9E75;word-break:break-all;">{{ $url }}</p>
@endsection
