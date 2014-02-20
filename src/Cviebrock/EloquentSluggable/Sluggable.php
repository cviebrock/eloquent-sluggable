<?php namespace Cviebrock\EloquentSluggable;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sluggable {

	/**
	 * The configuration array
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Constructor
	 *
	 * @param array $config
	 */
	public function __construct( array $config )
	{
		$this->config = $config;
	}

	/**
	 * Make a slug for the model
	 *
	 * @param  Model     $model The model
	 * @param  boolean   $force Force generation of a slug
	 * @return boolean
	 */
	public function make( Model $model, $force = false )
	{
		// if the model isn't sluggable, then do nothing
		if ( !isset($model::$sluggable) )
		{
			return true;
		}

		// load the configuration and use nicer variables for readability
		$config = array_merge($this->config, $model::$sluggable);
		$build_from = $save_to = $method = $separator = $unique = $on_update = $include_trashed = $reserved = null;
		extract($config, EXTR_IF_EXISTS);

		// skip slug generation if the model exists or the slug field is already populated,
		// and on_update is false ... unless we are forcing things!
		if ( !$force )
		{
			if ( ( $model->exists || !empty($model->{$save_to}) ) && !$on_update )
			{
				return true;
			}
		}

		// build the slug string
		if ( is_string($build_from) )
		{
			$string = $model->{$build_from};
		}
		elseif ( is_array($build_from) )
		{
			$string = '';
			foreach( $build_from as $field )
			{
				$string .= $model->{$field} . ' ';
			}
		}
		else
		{
			$string = $model->__toString();
		}

		$string = trim($string);

		// build slug using given slug style
		if ( is_null($method) )
		{
			$slug = Str::slug($string, $separator);
		}
		else if ( $method instanceof Closure )
		{
			$slug = $method($string, $separator);
		}
		else if ( is_callable($method) )
		{
			$slug = call_user_func($method, $string, $separator);
		}
		else
		{
			throw new \UnexpectedValueException("Sluggable method is not a callable, closure or null.");
		}

		// save this for later tests against uniqueness
		$base_slug = $slug;

		// check for reserved names
		if ( $reserved instanceof Closure )
		{
			$reserved = $reserved($model);
		}

		if ( is_array($reserved) && !empty($reserved) )
		{
			// if the generated slug is a reserved word, then append "-1" to it to prevent
			// a collision (assumes there are no reserved slugs that end in "-1" ).
			if ( in_array($slug, $reserved) )
			{
				$slug .= $separator . '1';
			}
		}
		else if ( !is_null($reserved) )
		{
			throw new \UnexpectedValueException("Sluggable reserved is not null, an array, or a closure that returns null/array.");
		}


		// if our new slug is the same as the old one, and we aren't forcing, we can be done
		if ( !$force && $model->{$save_to} === $slug )
		{
			return;
		}


		// check for uniqueness?
		if ( $unique )
		{
			// find all models where the slug is similar to the generated slug
			$class = get_class($model);
			if ( $include_trashed )
			{
				$collection = $class::where($save_to, 'LIKE', $base_slug.'%')
					->withTrashed()
					->get();
			}
			else
			{
				$collection = $class::where($save_to, 'LIKE', $base_slug.'%')
					->get();
			}

			// if there are no matching models, then we're okay with the generated slug
			if ( $collection->isEmpty() )
			{
				$model->{$save_to} = $slug;
				return true;
			}

			// extract the slug fields
			$list = $collection->lists($save_to, $model->getKeyName());

			// if the current model exists in the list -- i.e. the existing slug is either
			// equal to or an incremented version of the new slug -- then the slug doesn't
			// need to change and we can just return (unless on_update is true, in which case
			// ignore this test and continue).
			if ( !$on_update && array_key_exists($model->getKey(), $list) )
			{
				return true;
			}

			// does the exact new slug exist, or did we create a new slug because of a reserved word?
			if ( $base_slug != $slug || in_array($slug, $list) )
			{

				// copy this
				$self = $this;

				// filter the collection to only include the base slug, or the base slug + separator + number
				$collection->filter( function($obj) use ($base_slug, $separator, $save_to, $self)
				{
					// keep the base slug
					if ( $obj->{$save_to} === $base_slug )
					{
						return true;
					}

					return $self->isIncremented( $obj->{$save_to}, $base_slug, $separator);

				});

				// resort the collection by stripping the base slug
				$collection->sortBy( function($obj) use ($base_slug, $separator, $save_to)
				{
					return intval(substr($obj->{$save_to}, strlen($base_slug.$separator)));
				});

				// find the "highest" numbered version of the slug and increment it.
				$idx = substr($collection->last()->{$save_to}, strlen($base_slug.$separator));
				$idx = intval($idx);
				$idx++;

				$slug = $base_slug . $separator . $idx;

			}

		}

		// update the slug field
		$model->{$save_to} = $slug;

		// done!
		return true;

	}

	/**
	 * Test if a given slug is an incremented version of the base slug.
	 *
	 * @param  string  $slug      The slug to test
	 * @param  string  $base_slug The base version of the slug
	 * @param  string  $separator The separator
	 * @return boolean
	 */
	public function isIncremented( $slug, $base_slug, $separator )
	{
		if ( strpos($slug, $base_slug.$separator) === 0 )
		{
			$remainder = substr($slug, strlen($base_slug.$separator));
			return preg_match( '/^\d+$/', $remainder );
		}
		return false;
	}

}
