@extends('emails.layout')

@section('title', 'CommonGrove: We\'ve received your message')
@section('preheader', 'We\'ll take a look.')

@section('body')
    <h1 class="cg-h1" style="margin:0 0 8px;font-size:20px;font-weight:600;color:#111827;line-height:1.3;">We've got it.</h1>

    @if ($gamertag)
        <p class="cg-p" style="margin:0 0 16px;font-size:15px;color:#4b5563;line-height:1.75;">
            Hi {{ $gamertag }},
        </p>
    @endif

    <p class="cg-p" style="margin:0 0 16px;font-size:15px;color:#4b5563;line-height:1.75;">
        Your <strong style="font-weight:600;color:#374151;">{{ $typeLabel }}</strong> has been received. We read everything ourselves: no ticket queues, no bots.
    </p>

    <p class="cg-p" style="margin:0;font-size:15px;color:#4b5563;line-height:1.75;">
        If we need more details we'll follow up. Otherwise you don't need to do anything else. Thanks for taking the time.
    </p>
@endsection

@section('footer_extra')
    You received this because you submitted a report on CommonGrove.
@endsection
