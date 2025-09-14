<?php

namespace App\Shared\Infrastructure\Providers;

use App\Shared\Application\Port\TransactionManager;
use App\Shared\Infrastructure\Persistence\Eloquent\EloquentTransactionManager;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider for the shared feature.
 * Registers bindings from ports to adapters for common functionalities.
 */
final class SharedServiceProvider extends ServiceProvider
{
    /**
     * Register the container services.
     */
    public function register(): void
    {
        // Bind TransactionManager port to Eloquent adapter
        $this->app->bind(TransactionManager::class, EloquentTransactionManager::class);
    }

    /**
     * Bootstrap services after all providers have been registered.
     */
    public function boot(): void
    {
        // Here you can configure other shared services
    }
}
