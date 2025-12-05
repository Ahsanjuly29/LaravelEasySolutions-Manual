<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contract\CustomerRepositoryInterface;
use App\Repositories\Eloquent\EloquentCustomerRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        // Bind repositories
        $this->app->bind(
            CustomerRepositoryInterface::class,
            EloquentCustomerRepository::class
        );
    }

    /**
     * Bootstrap application services.
     */
    public function boot(): void
    {
        //
    }
}
