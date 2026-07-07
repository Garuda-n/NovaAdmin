<?php

namespace App\Providers;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Models\LoginLog;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\ActivityLogObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(Login::class, function ($event) {
            $loginLog = LoginLog::create([
                'user_id'    => $event->user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'login_at'   => now(),
            ]);
        });
    }
}