<?php

namespace Vaskiq\LaravelDataLayer\Providers;

use Illuminate\Support\ServiceProvider;
use Vaskiq\LaravelDataLayer\Contracts\DataFactoryInterface;
use Vaskiq\LaravelDataLayer\Factories\DataFactory;

class LaravelDataLayerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register any bindings, singletons, or other service configurations.
    }

    public function boot(): void
    {
        // Bootstrapping logic, such as publishing config files or migrations.
        $this->app->singleton(DataFactoryInterface::class, DataFactory::class);
    }
}
