<?php

namespace App\Providers;

use App\Http\Services\AuthenticatableServiceInterface;
use App\Http\Services\Production\AuthenticatableService;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interface\BaseRepositoryInterface;
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
        $this->app->singleton(
            AuthenticatableServiceInterface::class,
            AuthenticatableService::class
        );

        $this->app->singleton(
            BaseRepositoryInterface::class,
            BaseRepository::class
        );
    }
}
