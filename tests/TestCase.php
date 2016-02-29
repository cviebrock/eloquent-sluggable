<?php namespace Tests;

use Orchestra\Testbench\TestCase as Orchestra;


/**
 * Class SluggableTest
 */
abstract class TestCase extends Orchestra
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        // Call migrations specific to our tests, e.g. to seed the db
        $this->artisan('migrate', [
          '--database' => 'testbench',
          '--path' => __DIR__ . '../resources/database/migrations',
        ]);
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        //        // reset base path to point to our package's src directory
        //        $app['path.base'] = __DIR__ . '/../src';
        //
        // set up database configuration
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
          'driver' => 'sqlite',
          'database' => ':memory:',
          'prefix' => '',
        ]);
    }

    /**
     * Get Sluggable package providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
          \Cviebrock\EloquentSluggable\ServiceProvider::class
        ];
    }
}
