@extends('emails.layout')

@section('title', 'You are invited to the CommonGrove founding beta')
@section('preheader', 'A personal invite to join CommonGrove, and earn a permanent founding badge.')

@section('body')
    <h1 class="cg-h1" style="margin:0 0 8px;font-size:20px;font-weight:600;color:#111827;line-height:1.3;">You're invited.</h1>
    <p class="cg-p" style="margin:0 0 20px;font-size:15px;color:#4b5563;line-height:1.75;">
        Hey, Andrew here, founder of CommonGrove. I wanted to personally invite you to join during our founding beta.
    </p>

    <p class="cg-p" style="margin:0 0 20px;font-size:15px;color:#4b5563;line-height:1.75;">
        CommonGrove is a quiet, judgment-free space for introverts and gamers to find real connection. No algorithmic feeds designed to keep you anxious, no ads, no bots, no fake activity, just a small, thoughtful community built for people who find most social platforms exhausting.
    </p>

    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:20px 24px;margin-bottom:24px;">
        <p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#111827;letter-spacing:0.04em;text-transform:uppercase;">What is FirstRoots?</p>
        <p style="margin:0 0 12px;font-size:14px;color:#4b5563;line-height:1.7;">
            Everyone who joins during the beta period earns the <strong>FirstRoots</strong> founding badge, permanently. It appears on your profile and as a soft teal glow around your avatar everywhere on the platform.
        </p>
        <p style="margin:0;font-size:14px;color:#4b5563;line-height:1.7;">
            It's a small, permanent mark that says: <em>you were here before it was anything.</em> It can never be bought or earned later.
        </p>
    </div>

    <table cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:28px;">
        <tr>
            <td style="background:#1D9E75;border-radius:8px;">
                <a href="{{ $claimUrl }}"
                   style="display:inline-block;padding:13px 30px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;letter-spacing:0.01em;">Claim your FirstRoots badge →</a>
            </td>
        </tr>
    </table>

    <div class="cg-divider" style="margin:24px 0;border-top:1px solid #f3f4f6;"></div>

    <p class="cg-muted" style="margin:0 0 4px;font-size:11px;color:#9ca3af;">If the button doesn't work, paste this link into your browser:</p>
    <p style="margin:0 0 20px;font-size:11px;color:#1D9E75;word-break:break-all;">{{ $claimUrl }}</p>

    <p class="cg-muted" style="margin:0;font-size:12px;color:#9ca3af;line-height:1.7;">
        This invite was sent by Andrew Collins, founder of CommonGrove. If you did not expect this email you can safely ignore it.
    </p>
@endsection
