<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\PayPal\SubscriptionController;
use App\Http\Controllers\PayPal\WebhookController as PayPalWebhookController;
use App\Livewire\Account\SupporterSettings;
use App\Livewire\Admin\CrisisLog;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\PlatformStats;
use App\Livewire\Admin\ProblemReports as AdminProblemReports;
use App\Livewire\Admin\ReportsQueue;
use App\Livewire\Admin\TagModeration;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Account\CollectDateOfBirth;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Reports\ProblemReportForm;
use App\Livewire\Feed\CreateHangoutPost;
use App\Livewire\Feed\HangoutFeed;
use App\Livewire\Friends\FriendsList;
use App\Livewire\Messaging\ConversationList;
use App\Livewire\Messaging\DirectMessage;
use App\Livewire\Messaging\Room;
use App\Livewire\Profile\EditProfileCustomization;
use App\Livewire\Profile\ProfileSettings;
use App\Livewire\Profile\PublicProfile;
use App\Livewire\Support\ComparePage;
use App\Livewire\Support\SupportPage;
use App\Livewire\Onboarding\OnboardingFlow;
use App\Livewire\Tags\TagSelector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Local-only debug routes — REMOVE BEFORE PRODUCTION
|--------------------------------------------------------------------------
*/

Route::get('/dev-login', function () {
    if (!app()->isLocal()) abort(404);
    $user = App\Models\User::where('is_admin', true)->first();
    if (!$user) return 'No admin user found';
    Auth::login($user);
    session()->regenerate();
    return redirect()->route('feed');
})->name('dev.login');

/*
|--------------------------------------------------------------------------
| Public routes — accessible without authentication
|--------------------------------------------------------------------------
*/

Route::view('/', 'landing')->name('home');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');
Route::view('/guidelines', 'guidelines')->name('guidelines');
Route::get('/report', ProblemReportForm::class)->name('report');
Route::get('/support', SupportPage::class)->name('support');
Route::get('/support/compare', ComparePage::class)->name('support.compare');

// PayPal webhook — no CSRF (exempted in bootstrap/app.php)
Route::post('/paypal/webhook', PayPalWebhookController::class)->name('paypal.webhook');

// Registration
Route::get('/register', Register::class)->name('register');

// Login
Route::get('/login', Login::class)->name('login');

// Password reset — request link
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function (\Illuminate\Http\Request $request) {
    $request->validate(['email' => ['required', 'email']]);

    $status = \Illuminate\Support\Facades\Password::sendResetLink(
        $request->only('email')
    );

    return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
        ? back()->with('status', __($status))
        : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
})->middleware('throttle:5,1')->name('password.email');

// Password reset — set new password
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('password.store');

/*
|--------------------------------------------------------------------------
| Auth only — email verified not required
| (used for the email verification notice page itself)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Date of birth collection (for existing users prompted after login)
    Route::get('/account/birthday', CollectDateOfBirth::class)->name('account.birthday');

    // Email verification notice (Livewire — handles resend with rate limiting)
    Route::get('/email/verify', VerifyEmail::class)->name('verification.notice');

    // Resend verification email — fallback POST route (used by Laravel internals; UI uses Livewire)
    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:3,10')->name('verification.send');

    // Handle the verification link click (signed URL, expires after 60 minutes)
    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('onboarding');
    })->middleware(['signed', 'throttle:10,1'])->name('verification.verify');

    // Logout
    Route::post('/logout', LogoutController::class)->name('logout');

});

/*
|--------------------------------------------------------------------------
| Protected routes — auth + verified required
| Add all internal platform routes inside this group.
|--------------------------------------------------------------------------
*/

// Onboarding — auth + verified, but NOT behind onboarded middleware (to avoid redirect loop)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/onboarding', OnboardingFlow::class)->name('onboarding');
});

// Browseable without email verification — read-only views
Route::middleware(['auth', 'onboarded'])->group(function () {

    // Interest tag selection
    Route::get('/tags', TagSelector::class)->name('tags.select');

    // Hangout Feed (browseable; posting requires verified below)
    Route::get('/feed', HangoutFeed::class)->name('feed');

    // Hangout room — redirect to room if one exists, otherwise placeholder
    Route::get('/hangout/{id}', function (string $id) {
        $room = \App\Models\Conversation::where('hangout_post_id', $id)->first();

        if ($room) {
            return redirect()->route('room.show', $room->id);
        }

        return view('hangout.placeholder', ['id' => $id]);
    })->name('hangout.show');

    // Messaging
    Route::get('/messages', ConversationList::class)->name('messages.index');
    Route::get('/messages/{conversationId}', DirectMessage::class)->name('messages.show');
    Route::get('/room/{conversationId}', Room::class)->name('room.show');

    // Friends
    Route::get('/friends', FriendsList::class)->name('friends.index');

    // Profile
    Route::get('/profile/settings', ProfileSettings::class)->name('profile.settings');
    Route::get('/profile/edit', EditProfileCustomization::class)->name('profile.edit');
    Route::get('/profile/{gamertag}', PublicProfile::class)->name('profile.show');

});

// Requires verified email — write/create actions
Route::middleware(['auth', 'verified', 'onboarded'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Creating a hangout post requires verified
    Route::get('/feed/post', CreateHangoutPost::class)->name('feed.post');

    // Supporter settings
    Route::get('/settings/supporter', SupporterSettings::class)->name('settings.supporter');

    // PayPal subscription flow
    Route::get('/support/subscribe', [SubscriptionController::class, 'redirect'])->name('support.subscribe');
    Route::get('/support/subscribe/return', [SubscriptionController::class, 'return'])->name('support.subscribe.return');
    Route::get('/support/subscribe/cancel', [SubscriptionController::class, 'cancel'])->name('support.subscribe.cancel');

});

/*
|--------------------------------------------------------------------------
| Admin routes — auth + verified + admin middleware
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {

    Route::get('/', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/reports', ReportsQueue::class)->name('admin.reports');
    Route::get('/problem-reports', AdminProblemReports::class)->name('admin.problem-reports');
    Route::get('/users', UserManagement::class)->name('admin.users');
    Route::get('/tags', TagModeration::class)->name('admin.tags');
    Route::get('/stats', PlatformStats::class)->name('admin.stats');
    Route::get('/crisis', CrisisLog::class)->name('admin.crisis');

    Route::get('/problem-reports/{id}/screenshot', function (string $id) {
        $report = \App\Models\ProblemReport::findOrFail($id);
        abort_if(! $report->screenshot_path, 404);

        $path = storage_path('app/' . $report->screenshot_path);
        abort_if(! file_exists($path), 404);

        return response()->file($path);
    })->name('admin.problem-reports.screenshot');

});
