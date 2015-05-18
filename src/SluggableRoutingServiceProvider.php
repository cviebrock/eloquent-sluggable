<?php namespace Cviebrock\EloquentSluggable;

use \Illuminate\Routing\RoutingServiceProvider;
use \Cviebrock\EloquentSluggable\SluggableRouter as Router;

class SluggableRoutingServiceProvider extends RoutingServiceProvider
{
    /**
     * Register the router instance.
     *
     * @return void
     */
    protected function registerRouter()
    {
        $this->app->rebinding(
            'router',
            $this->app->share(function($app) {
                return new Router($app['events'], $app);
            })
        );
    }
}