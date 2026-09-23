<?php

namespace App\Providers;

use Application\Jira\Policies\JiraNotePolicy;
use Domain\Models\JiraNote;
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
        Gate::policy(JiraNote::class, JiraNotePolicy::class);
    }
}
