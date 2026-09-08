@extends('emails.layout')

@section('title', 'We miss you at CommonGrove')
@section('preheader', 'It has been a while; your grove is still here.')

@section('body')
    <h1 class="cg-h1" style="margin:0 0 8px;font-size:20px;font-weight:600;color:#111827;line-height:1.3;">{{-- PLACEHOLDER: headline --}}We miss you.</h1>
    <p class="cg-p" style="margin:0 0 24px;font-size:15px;color:#4b5563;line-height:1.75;">
        It's been a while since we last saw you at CommonGrove, and that's okay. Life gets busy. 
        We hope you've been well. 
    </p>
    <p class="cg-p" style="margin:0 0 24px;font-size:15px;color:#4b5563;line-height:1.75;">
        Since you last visited, we've spent a long time quietly rebuilding things. Not chasing trends. Just trying to make this feel more like the calm, unhurried place we always wanted it to be.
        A little softer, a little more thoughtful and built the way we'd want it if <strong>we</strong> were the ones looking for somewhere to belong.
    </p>
    <p class="cg-p" style="margin:0 0 24px;font-size:15px;color:#4b5563;line-height:1.75;">
        We just wanted you to know that your grove is still here, and that you're always welcome back.
    </p>

    <table cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:28px;">
        <tr>
            <td style="background:#1D9E75;border-radius:8px;">
                <a href="{{ route('feed') }}"
                   style="display:inline-block;padding:12px 26px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;letter-spacing:0.01em;">Step back inside.</a>
            </td>
        </tr>
    </table>

    <p class="cg-muted" style="margin:0;font-size:13px;color:#6b7280;line-height:1.7;">
        {{-- PLACEHOLDER: closing line --}}Take your time. We'll be here.
    </p>
@endsection

@section('footer_extra')
    <a href="{{ $unsubscribeUrl }}" style="color:#9ca3af;text-decoration:underline;">Unsubscribe from emails like this</a>
@endsection
