<?php namespace Cviebrock\EloquentSluggable;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

/**
 * Class ServiceProvider
 *
 * @package Cviebrock\EloquentSluggable
 */
class ServiceProvider extends BaseServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->setUpConfig();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton(SluggableObserver::class, function($app) {
            return new SluggableObserver(new SlugService(), $app['events']);
        });
    }

    protected function setUpConfig()
    {
        $source = dirname(__DIR__) . '/resources/config/sluggable.php';

        if ($this->app instanceof LaravelApplication) {
            $this->publishes([$source => config_path('sluggable.php')], 'config');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('sluggable');
        }

        $this->mergeConfigFrom($source, 'sluggable');
    }
}
