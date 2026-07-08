<?php

namespace App\Providers;
use App\Models\Company;
use App\Models\LoginLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Login Log
        Event::listen(Login::class, function ($event) {
            LoginLog::create([
                'user_id'    => $event->user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'login_at'   => now(),
            ]);

        });

        // Share Active Company with all Blade Views
        View::composer('*', function ($view) {

            $currentCompany = Company::where('status', 1)
                ->orderBy('id')
                ->first();

            $view->with('currentCompany', $currentCompany);

        });
    }
}