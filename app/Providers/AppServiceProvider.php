<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
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
        Paginator::useBootstrapFive();

        Gate::before(function ($user, $ability) {
            // Profile viewing and editing is universally allowed for all authenticated users
            if ($user && in_array($ability, ['profile.view', 'profile.edit'], true)) {
                return true;
            }

            return method_exists($user, 'hasRole') && $user->hasRole('super_admin') ? true : null;
        });
    }
}