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
		$this->registerConfiguration();
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

	/**
	 * Register configuration files
	 *
	 * @return void
	 */

	protected function registerConfiguration()
	{
		$user_config_file = app()->configPath().'/packages/cviebrock/eloquent-sluggable/config.php';
		$package_config_file = __DIR__.'/../../config/config.php';
		$config = $this->app['files']->getRequire($package_config_file);

		if (file_exists($user_config_file)) {
			$userConfig = $this->app['files']->getRequire($user_config_file);
			$config = array_replace_recursive($config, $userConfig);
		}

		$this->app['config']->set('eloquent-sluggable::config', $config);
	}

}
