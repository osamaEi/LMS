<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Repository Interfaces to Implementations
        $this->app->bind(
            \App\Repositories\Contracts\TrackRepositoryInterface::class,
            \App\Repositories\Eloquent\TrackRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\TermRepositoryInterface::class,
            \App\Repositories\Eloquent\TermRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\SubjectRepositoryInterface::class,
            \App\Repositories\Eloquent\SubjectRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\EnrollmentRepositoryInterface::class,
            \App\Repositories\Eloquent\EnrollmentRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\SessionRepositoryInterface::class,
            \App\Repositories\Eloquent\SessionRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\AttendanceRepositoryInterface::class,
            \App\Repositories\Eloquent\AttendanceRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\EvaluationRepositoryInterface::class,
            \App\Repositories\Eloquent\EvaluationRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
