<?php namespace Cviebrock\EloquentSluggable\Facades;

use Illuminate\Support\Facades\Facade;

class Slugger extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'slugger';
	}

}
