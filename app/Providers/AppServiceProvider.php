<?php

namespace App\Providers;
use App\Models\Company;
use App\Models\LoginLog;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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

        // Register Gates from permissions table
        try {
            $permissions = Permission::all();
            foreach ($permissions as $permission) {
                Gate::define($permission->slug, function (User $user) use ($permission) {
                    return $user->hasPermission($permission->slug);
                });
            }
        } catch (\Exception $e) {
            // Table may not exist yet during migrations
        }
    }
}