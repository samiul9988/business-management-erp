<?php

namespace App\Providers;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
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
        // The activation screen (and the assets/login flow it depends on)
        // must stay reachable even while the rest of the site is down, so
        // an admin can always turn maintenance mode back off.
        PreventRequestsDuringMaintenance::except([
            'login',
            'logout',
            'dashboard/activation',
            'dashboard/activation/toggle',
            'dashboard/activation/preview',
            'css/*',
            'images/*',
            'build/*',
            'vendor/*',
        ]);
    }
}
