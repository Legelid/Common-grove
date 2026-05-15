<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\NotificationPreference;
use App\Models\User;
use App\Services\BlockService;
use App\Services\CrisisDetectionService;
use App\Services\FriendshipService;
use App\Services\GamertagSuggestionService;
use App\Services\NotificationPreferenceService;
use App\Services\PasswordService;
use App\Services\ReportService;
use App\Services\WeeklyMatchService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PasswordService::class);
        $this->app->singleton(GamertagSuggestionService::class);
        $this->app->singleton(BlockService::class);
        $this->app->singleton(ReportService::class);
        $this->app->singleton(CrisisDetectionService::class);
        $this->app->singleton(FriendshipService::class);
        $this->app->singleton(NotificationPreferenceService::class);
        $this->app->singleton(WeeklyMatchService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Auto-create default notification preferences when a new user registers.
        User::created(function (User $user): void {
            NotificationPreference::firstOrCreate(['user_id' => $user->id]);
        });
    }
}
