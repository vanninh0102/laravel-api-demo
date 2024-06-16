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
        $this->app->bind(\App\Repositories\Interfaces\AuthInterface::class, \App\Repositories\SanctumAuthRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\StoreInterface::class, \App\Repositories\StoreRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\ProductInterface::class, \App\Repositories\ProductRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
