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

}
