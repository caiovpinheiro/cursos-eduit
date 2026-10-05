<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Models\Activity;
use App\Models\VideoActivity;
use App\Models\UserActivity;
use App\Models\UserCourse;
use App\Observers\ActivityObserver;
use App\Observers\VideoActivityObserver;
use App\Observers\UserActivityObserver;
use App\Observers\UserCourseObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $appUrl = rtrim((string) config('app.url'), '/');

        if ($appUrl !== '') {
            URL::forceRootUrl($appUrl);

            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }

        RateLimiter::for('web-login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));
            $key = $email.'|'.$request->ip();

            return [
                Limit::perMinute(5)->by($key),
                Limit::perHour(20)->by($key),
            ];
        });

        RateLimiter::for('web-password-recovery', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));
            $key = $email.'|'.$request->ip();

            return [
                Limit::perMinute(3)->by($key),
                Limit::perHour(10)->by($key),
            ];
        });

        Activity::observe(ActivityObserver::class);
        VideoActivity::observe(VideoActivityObserver::class);
        UserActivity::observe(UserActivityObserver::class);
        UserCourse::observe(UserCourseObserver::class);
    }
}
