<?php

declare(strict_types=1);

use App\Services\WeeklyMatchService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Expire hangout posts every 15 minutes
Schedule::command('hangout:expire')->everyFifteenMinutes();

// Group 2: Expire user statuses every 30 minutes
Schedule::command('status:expire')->everyThirtyMinutes();

// Group 11: Generate weekly match suggestions every Monday at 09:00
Schedule::call(fn () => app(WeeklyMatchService::class)->generateMatches())
    ->weeklyOn(1, '09:00')
    ->name('weekly-matches')
    ->withoutOverlapping();

// Group 12: Check friendship milestones daily
Schedule::command('friendships:milestones')->daily();

// Prune persistent room messages older than 14 days (runs at 3 AM daily)
Schedule::command('rooms:prune-messages')->dailyAt('03:00');
