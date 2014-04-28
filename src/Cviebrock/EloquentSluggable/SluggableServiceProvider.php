<?php namespace Cviebrock\EloquentSluggable;


use Illuminate\Support\ServiceProvider;

class SluggableServiceProvider extends ServiceProvider {

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
		$this->package('cviebrock/eloquent-sluggable');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerEvents();
		$this->registerCommands();
	}

	/**
	 * Register the listener events
	 *
	 * @return void
	 */
	public function registerEvents()
	{
		$this->app['events']->listen('eloquent.saving*', function($model)
		{
			if ($model instanceof SluggableInterface)
			{
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
		$this->app['sluggable.table'] = $this->app->share(function($app)
		{
			return new SluggableTableCommand;
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
		return array();
	}

}
