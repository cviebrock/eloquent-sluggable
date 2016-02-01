<?php namespace Cviebrock\EloquentSluggable;

use Illuminate\Support\ServiceProvider;

/**
 * Class SluggableServiceProvider
 *
 * @package Cviebrock\EloquentSluggable
 */
class SluggableServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../resources/config/sluggable.php' => config_path('sluggable.php'),
        ], 'config');

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../resources/config/sluggable.php', 'sluggable');

        $this->app->singleton(SlugService::class);

    }

}
