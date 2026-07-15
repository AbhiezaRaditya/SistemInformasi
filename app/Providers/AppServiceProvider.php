<?php

namespace App\Providers;

use App\Listeners\UpdateLastLoginTimestamp;
use App\Listeners\UpdateLastLogoutTimestamp;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        Event::listen(Login::class, UpdateLastLoginTimestamp::class);
        Event::listen(Logout::class, UpdateLastLogoutTimestamp::class);
    }
}