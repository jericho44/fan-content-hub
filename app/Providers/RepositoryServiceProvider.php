<?php

namespace App\Providers;

use App\Interfaces;
use App\Repositories;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->bind(Interfaces\AppVersionInterface::class, Repositories\AppVersionRepository::class);
        // $this->app->bind(Interfaces\UserInterface::class, Repositories\UserRepository::class);
        // $this->app->bind(Interfaces\RoleInterface::class, Repositories\RoleRepository::class);
        // $this->app->bind(Interfaces\UserFirebaseInterface::class, Repositories\UserFirebaseRepository::class);
        // $this->app->bind(Interfaces\ProvinceInterface::class, Repositories\ProvinceRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
