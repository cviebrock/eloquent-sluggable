<?php

namespace Cviebrock\EloquentSluggable\Tests;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class TestServiceProvider.
 */
class TestServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(
            __DIR__ . '/database/migrations'
        );
    }
}
