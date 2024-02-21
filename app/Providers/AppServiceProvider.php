<?php

namespace App\Providers;

use App\Http\Services\AuthenticatableServiceInterface;
use App\Http\Services\Production\AuthenticatableService;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Eloquent\LoaiThongBaoRepository;
use App\Repositories\Eloquent\ThongBaoRepository;
use App\Repositories\Eloquent\ThongBaoUserRepository;
use App\Repositories\Interface\BaseRepositoryInterface;
use App\Repositories\Interface\LoaiThongBaoRepositoryInterface;
use App\Repositories\Interface\ThongBaoRepositoryInterface;
use App\Repositories\Interface\ThongBaoUserRepositoryInterface;
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

        $this->app->singleton(
            ThongBaoRepositoryInterface::class,
            ThongBaoRepository::class
        );

        $this->app->singleton(
            ThongBaoUserRepositoryInterface::class,
            ThongBaoUserRepository::class
        );

        $this->app->singleton(
            LoaiThongBaoRepositoryInterface::class,
            LoaiThongBaoRepository::class
        );
    }
}
