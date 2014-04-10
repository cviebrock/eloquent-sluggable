<?php namespace Cviebrock\EloquentSluggable\Test;

use Orchestra\Testbench\TestCase;


class SluggableTest extends TestCase {

  /**
   * Setup the test environment.
   */
	public function setUp()
	{
		parent::setUp();

		// Create an artisan object for calling migrations
		$artisan = $this->app->make('artisan');

		// Call migrations specific to our tests, e.g. to seed the db
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
			'Slugger' => 'Cviebrock\EloquentSluggable\Facades\Laravel\Slugger'
		);
	}


	/**
	 * Test basic slugging functionality.
	 *
	 * @test
	 */
	public function testSimpleSlug()
	{
		$post = $this->post('My First Post');
		$this->assertEquals($post->slug, 'my-first-post');
	}
}
