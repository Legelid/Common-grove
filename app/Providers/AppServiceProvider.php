<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\NotificationPreference;
use App\Models\User;
use App\Policies\UserProfilePolicy;
use App\Services\BlockService;
use App\Services\CrisisDetectionService;
use App\Services\FriendshipService;
use App\Services\GamertagSuggestionService;
use App\Services\NotificationPreferenceService;
use App\Services\PasswordService;
use App\Services\ReportService;
use App\Services\HolidayThemeService;
use App\Services\SupporterService;
use App\Services\TonePackService;
use App\Services\WeeklyMatchService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
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
        $this->app->singleton(SupporterService::class);
        $this->app->singleton(HolidayThemeService::class);
        $this->app->singleton(TonePackService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserProfilePolicy::class);

        // Auto-create default notification preferences when a new user registers.
        User::created(function (User $user): void {
            NotificationPreference::firstOrCreate(['user_id' => $user->id]);
        });

        // @supporter / @endsupporter — gate Blade blocks to supporter+ users.
        Blade::directive('supporter', function (): string {
            return "<?php if(auth()->check() && auth()->user()->isSupporter()): ?>";
        });
        Blade::directive('endsupporter', function (): string {
            return "<?php endif; ?>";
        });

        // @freemember / @endfreemember — target non-supporter blocks (e.g. upgrade prompts).
        Blade::directive('freemember', function (): string {
            return "<?php if(auth()->check() && !auth()->user()->isSupporter()): ?>";
        });
        Blade::directive('endfreemember', function (): string {
            return "<?php endif; ?>";
        });

        // @tone('key', 'fallback') — outputs a tone-pack-aware phrase for the current user.
        // Never use for safety, moderation, or legal copy.
        Blade::directive('tone', function (string $expression): string {
            return "<?php echo auth()->check() ? e(app(\\App\\Services\\TonePackService::class)->getPhrase(auth()->user(), {$expression})) : ''; ?>";
        });
    }
}
