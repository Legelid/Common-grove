<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

/**
 * One-click unsubscribe from marketing/win-back email — reached via a
 * permanent signed URL (see WinbackMail), no login required. The 'signed'
 * route middleware rejects any tampered or non-CommonGrove-issued link
 * before this ever runs.
 */
class EmailUnsubscribeController extends Controller
{
    public function __invoke(User $user): View
    {
        $user->update(['marketing_emails_opt_out' => true]);

        return view('auth.unsubscribed');
    }
}
