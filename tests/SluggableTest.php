<?php namespace Cviebrock\EloquentSluggable\Test;

use Orchestra\Testbench\TestCase;

class SluggableTest extends TestCase {

  /**
   * Setup the test environment.
   */
	public function setUp()
	{
		parent::setUp();

		// create an artisan object for calling migrations
		$artisan = $this->app->make('artisan');

		// call migrations specific to our tests, e.g. to seed the db
		$artisan->call('migrate', array(
			'--database' => 'testbench',
			'--path'     => '../tests/migrations',
		));

	}

  /**
   * Define environment setup.
   *
   * @param  Illuminate\Foundation\Application    $app
   * @return void
   */
	protected function getEnvironmentSetUp($app)
	{
			// reset base path to point to our package's src directory
			$app['path.base'] = __DIR__ . '/../src';

			$app['config']->set('database.default', 'testbench');
			$app['config']->set('database.connections.testbench', array(
					'driver'   => 'sqlite',
					'database' => ':memory:',
					'prefix'   => '',
			));
	}


  /**
   * Get Sluggable package providers.
   *
   * @return array
   */
	protected function getPackageProviders()
	{
		return array('Cviebrock\EloquentSluggable\SluggableServiceProvider');
	}


  /**
   * Get Sluggable package aliases.
   *
   * @return array
   */
	protected function getPackageAliases()
	{
		return array(
			'Sluggable' => 'Cviebrock\EloquentSluggable\Facades\Sluggable'
		);
	}


	protected function post($title)
	{
		$post = new Post(array('title'=>$title));
		return $post;
	}


	public function testSimpleSlug()
	{
		$post = $this->post('My first post');
		$post->save();
		$this->assertEquals($post->slug, 'my-first-post');
	}

}
