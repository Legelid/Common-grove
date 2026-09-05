@extends('emails.layout')

@section('title', 'New CommonGrove support request')
@section('preheader', 'A problem report was submitted and needs review.')

@section('body')
    @php
        $typeLabel     = \App\Models\ProblemReport::TYPES[$problemReport->report_type] ?? $problemReport->report_type;
        $priorityColor = match ($problemReport->priority) {
            'urgent' => '#ef4444',
            'high'   => '#f97316',
            default  => '#9ca3af',
        };
    @endphp

    <p style="margin:0 0 4px;font-size:12px;font-weight:600;color:#4A9E6A;text-transform:uppercase;letter-spacing:0.06em;">{{ $typeLabel }}</p>
    <h1 class="cg-h1" style="margin:0 0 4px;font-size:20px;font-weight:600;color:#111827;line-height:1.3;">{{ $problemReport->subject }}</h1>
    <p class="cg-muted" style="margin:0 0 24px;font-size:12px;color:#9ca3af;">
        {{ $problemReport->created_at->format('D d M Y · H:i') }} UTC
        &nbsp;&middot;&nbsp;
        <span style="color:{{ $priorityColor }};font-weight:600;text-transform:uppercase;font-size:11px;letter-spacing:0.05em;">{{ $problemReport->priority }}</span>
    </p>

    {{-- Details table --}}
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-top:1px solid #f3f4f6;margin-bottom:24px;">

        <tr>
            <td class="cg-muted" style="padding:11px 0;border-bottom:1px solid #f9fafb;width:120px;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;vertical-align:top;">From</td>
            <td class="cg-p" style="padding:11px 0 11px 12px;border-bottom:1px solid #f9fafb;font-size:14px;color:#374151;">
                @if ($problemReport->user)
                    {{ $problemReport->user->gamertag }}
                    @if ($problemReport->user->email)
                        <span class="cg-muted" style="color:#9ca3af;">&nbsp;&lt;{{ $problemReport->user->email }}&gt;</span>
                    @endif
                @else
                    <span class="cg-muted" style="color:#9ca3af;">Guest</span>
                @endif
            </td>
        </tr>

        @if ($problemReport->contact_email)
        <tr>
            <td class="cg-muted" style="padding:11px 0;border-bottom:1px solid #f9fafb;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;vertical-align:top;">Reply to</td>
            <td class="cg-p" style="padding:11px 0 11px 12px;border-bottom:1px solid #f9fafb;font-size:14px;color:#374151;">{{ $problemReport->contact_email }}</td>
        </tr>
        @endif

        @if ($problemReport->related_user)
        <tr>
            <td class="cg-muted" style="padding:11px 0;border-bottom:1px solid #f9fafb;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;vertical-align:top;">User</td>
            <td class="cg-p" style="padding:11px 0 11px 12px;border-bottom:1px solid #f9fafb;font-size:14px;color:#374151;">{{ $problemReport->related_user }}</td>
        </tr>
        @endif

        @if ($problemReport->related_room)
        <tr>
            <td class="cg-muted" style="padding:11px 0;border-bottom:1px solid #f9fafb;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;vertical-align:top;">Room</td>
            <td class="cg-p" style="padding:11px 0 11px 12px;border-bottom:1px solid #f9fafb;font-size:14px;color:#374151;">{{ $problemReport->related_room }}</td>
        </tr>
        @endif

        @if ($problemReport->page_url)
        <tr>
            <td class="cg-muted" style="padding:11px 0;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;vertical-align:top;">Page</td>
            <td style="padding:11px 0 11px 12px;font-size:14px;"><a href="{{ $problemReport->page_url }}" style="color:#1D9E75;word-break:break-all;text-decoration:none;">{{ $problemReport->page_url }}</a></td>
        </tr>
        @endif

    </table>

    <p class="cg-muted" style="margin:0 0 6px;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;">Message</p>
    <div class="cg-pre" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:14px 16px;font-size:14px;color:#374151;line-height:1.7;white-space:pre-wrap;margin-bottom:28px;">{{ $problemReport->description }}</div>

    <table cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td style="background:#1D9E75;border-radius:8px;">
                <a href="{{ route('admin.problem-reports') }}"
                   style="display:inline-block;padding:11px 22px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;">View in admin panel →</a>
            </td>
        </tr>
    </table>
@endsection

@section('footer_extra')
    Admin-only notification. Only you receive this.
@endsection
