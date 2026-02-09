<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register NELC Compliance Services
        $this->app->singleton(\App\Services\ActivityLogService::class);
        $this->app->singleton(\App\Services\Xapi\XapiService::class);
        $this->app->singleton(\App\Services\Xapi\XapiClient::class);
        $this->app->singleton(\App\Services\Xapi\XapiStatementBuilder::class);
        $this->app->singleton(\App\Services\ReportService::class);
        $this->app->singleton(\App\Services\FutureXSsoService::class);
        $this->app->singleton(\App\Services\FutureXService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
