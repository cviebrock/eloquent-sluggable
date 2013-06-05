<?php namespace Cviebrock\EloquentSluggable;

use Illuminate\Support\ServiceProvider;

class SluggableServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

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

		$this->registerSluggable();
		$this->registerEvents();

	}

	public function registerEvents()
	{
		$app = $this->app;

		$app['events']->listen('eloquent.saving*', function($model) use ($app)
		{
			$app['sluggable']->make($model);
		});
	}


	public function registerSluggable()
	{
		$this->app['sluggable'] = $this->app->share(function($app)
		{

			$config = $app['config']->get('eloquent-sluggable::config');

			return new Sluggable($config);
		});
	}


	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('sluggable');
	}

}
