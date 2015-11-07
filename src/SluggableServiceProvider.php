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
        $this->handleConfigs();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCreator();
        $this->registerEvents();
        $this->registerCommands();
    }

    /**
     * Register the configuration.
     */
    private function handleConfigs()
    {
        $configPath = __DIR__ . '/../config/sluggable.php';
        $this->publishes([$configPath => config_path('sluggable.php')]);
        $this->mergeConfigFrom($configPath, 'sluggable');
    }

    /**
     * Register the migration creator.
     *
     * @return void
     */
    protected function registerCreator()
    {
        $this->app->singleton('sluggable.creator', function ($app) {
            return new SluggableMigrationCreator($app['files']);
        });
    }

    /**
     * Register the listener events
     *
     * @return void
     */
    public function registerEvents()
    {
        $this->app['events']->listen('eloquent.saving*', function ($model) {
            if ($model instanceof SluggableInterface) {
                $model->sluggify();
            }
        });
    }

    /**
     * Register the artisan commands
     *
     * @return void
     */
    public function registerCommands()
    {
        $this->app['sluggable.table'] = $this->app->share(function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['sluggable.creator'];

            $composer = $app['composer'];

            return new SluggableTableCommand($creator, $composer);
        });

        $this->commands('sluggable.table');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sluggable.creator', 'sluggable.table'];
    }
}
