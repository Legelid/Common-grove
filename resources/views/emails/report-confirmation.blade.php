@extends('emails.layout')

@section('title', 'CommonGrove: Report received')
@section('preheader', 'Your report has been received. We take this seriously.')

@section('body')
    <h1 class="cg-h1" style="margin:0 0 8px;font-size:20px;font-weight:600;color:#111827;line-height:1.3;">Your report has been received.</h1>

    @if ($gamertag)
        <p class="cg-p" style="margin:0 0 16px;font-size:15px;color:#4b5563;line-height:1.75;">
            Hi {{ $gamertag }},
        </p>
    @endif

    <p class="cg-p" style="margin:0 0 16px;font-size:15px;color:#4b5563;line-height:1.75;">
        We review reports personally, not with an automated filter. We won't be able to share details of what action is taken, but every report is read and considered.
    </p>

    <p class="cg-p" style="margin:0 0 0;font-size:15px;color:#4b5563;line-height:1.75;">
        Thank you for helping keep CommonGrove safe.
    </p>
@endsection

@section('footer_extra')
    You received this because you submitted a report on CommonGrove.
@endsection
