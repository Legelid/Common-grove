@extends('emails.layout')

@section('title', $subjectLine)
@section('preheader', Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', $bodyText)), 100))

@section('body')
    <div class="cg-p" style="margin:0;font-size:15px;color:#4b5563;line-height:1.75;white-space:pre-line;">{{ $bodyText }}</div>
@endsection

@section('footer_extra')
    <a href="{{ $unsubscribeUrl }}" style="color:#9ca3af;text-decoration:underline;">Unsubscribe from emails like this</a>
@endsection
