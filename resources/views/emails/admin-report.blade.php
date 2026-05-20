@extends('emails.layout')

@section('title', 'New CommonGrove report')
@section('preheader', 'A user report was submitted and needs review.')

@section('body')
    @php
        $typeMap = [
            'App\\Models\\Message'     => 'Message',
            'App\\Models\\HangoutPost' => 'Hangout post',
            'App\\Models\\User'        => 'User profile',
        ];
        $reportableLabel = $typeMap[$report->reportable_type] ?? ($report->reportable_type ?? '—');
        $reasonLabel     = ucwords(str_replace('_', ' ', $report->reason ?? '—'));
        $reporter        = $report->reporter;
        $reported        = $report->reportedUser;
    @endphp

    <p style="margin:0 0 4px;font-size:12px;font-weight:600;color:#4A9E6A;text-transform:uppercase;letter-spacing:0.06em;">New report</p>
    <h1 class="cg-h1" style="margin:0 0 4px;font-size:20px;font-weight:600;color:#111827;">{{ $reasonLabel }}</h1>
    <p class="cg-muted" style="margin:0 0 24px;font-size:12px;color:#9ca3af;">{{ $report->created_at->format('D d M Y · H:i') }} UTC</p>

    {{-- Details table --}}
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-top:1px solid #f3f4f6;margin-bottom:24px;">

        <tr>
            <td class="cg-muted" style="padding:11px 0;border-bottom:1px solid #f9fafb;width:120px;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;vertical-align:top;">Reporter</td>
            <td class="cg-p" style="padding:11px 0 11px 12px;border-bottom:1px solid #f9fafb;font-size:14px;color:#374151;">
                {{ $reporter?->gamertag ?? '—' }}
                @if ($reporter?->email)
                    <span class="cg-muted" style="color:#9ca3af;">&nbsp;&lt;{{ $reporter->email }}&gt;</span>
                @endif
            </td>
        </tr>

        <tr>
            <td class="cg-muted" style="padding:11px 0;border-bottom:1px solid #f9fafb;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;vertical-align:top;">Reported</td>
            <td class="cg-p" style="padding:11px 0 11px 12px;border-bottom:1px solid #f9fafb;font-size:14px;color:#374151;">{{ $reported?->gamertag ?? '—' }}</td>
        </tr>

        <tr>
            <td class="cg-muted" style="padding:11px 0;border-bottom:1px solid #f9fafb;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;vertical-align:top;">Context</td>
            <td class="cg-p" style="padding:11px 0 11px 12px;border-bottom:1px solid #f9fafb;font-size:14px;color:#374151;">{{ $reportableLabel }}</td>
        </tr>

        <tr>
            <td class="cg-muted" style="padding:11px 0;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;vertical-align:top;">Reason</td>
            <td class="cg-p" style="padding:11px 0 11px 12px;font-size:14px;color:#374151;">{{ $reasonLabel }}</td>
        </tr>

    </table>

    @if ($report->detail)
        <p class="cg-muted" style="margin:0 0 6px;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;">Details</p>
        <div class="cg-pre" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:14px 16px;font-size:14px;color:#374151;line-height:1.7;white-space:pre-wrap;margin-bottom:28px;">{{ $report->detail }}</div>
    @endif

    <table cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td style="background:#1D9E75;border-radius:8px;">
                <a href="{{ route('admin.reports') }}"
                   style="display:inline-block;padding:11px 22px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;">View in admin panel →</a>
            </td>
        </tr>
    </table>
@endsection

@section('footer_extra')
    Admin-only notification. Only you receive this.
@endsection
