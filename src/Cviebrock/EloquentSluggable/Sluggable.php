<?php namespace Cviebrock\EloquentSluggable;


use Closure;
use Illuminate\Database\Eloquent\Model;

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
	public function __construct( array $config ) {

		$this->config = $config;

	}

	/**
	 * Make a slug for the model
	 *
	 * @param  Model     $model The model
	 * @param  boolean   $force Force generation of a slug
	 * @return boolean
	 */
	public function make( Model $model, $force = false)
	{

		// if the model isn't sluggable, then do nothing

		if ( !isset( $model::$sluggable ) ) {
			return true;
		}


		// load the configuration

		$config = array_merge( $this->config, $model::$sluggable );


		// nicer variables for readability

		$build_from = $save_to = $method = $separator = $unique = $on_update = null;
		extract( $config, EXTR_IF_EXISTS );


		// skip slug generation if the model exists or the slug field is already populated,
		// and on_update is false ... unless we are forcing things!

		if (!$force) {
			if ( ( $model->exists || !empty($model->{$save_to}) ) && !$on_update ) {
				return true;
			}
		}


		// build the slug string

		if ( is_string($build_from) ) {

			$string = $model->{$build_from};

		} else if ( is_array( $build_from ) ) {

			$string = '';
			foreach( $build_from as $field ) {
				$string .= $model->{$field} . ' ';
			}

		} else {

			$string = $model->__toString();
		}

		$string = trim( $string );


		// build slug using given slug style

		if ( is_null($method) ) {

			$slug = \Str::slug( $string );

		} else if ( $method instanceof Closure ) {

			$slug = $method( $string, $separator );

		} else if ( is_callable( $method ) ) {

			$slug = call_user_func( $method, $string, $separator );

		} else {

			throw new \UnexpectedValueException("Sluggable method is not a callable, closure or null.");

		}


		// check for uniqueness?

		if ( $unique ) {

			// find all models where the slug is similar to the generated slug

			$class = get_class($model);

			$collection = $class::where( $save_to, 'LIKE', $slug.'%' )
				->orderBy( $save_to, 'DESC' )
				->get();


			// extract the slug fields

			$list = $collection->lists( $save_to, $model->getKeyName() );

			// if the current model exists in the list -- i.e. the existing slug is either
			// equal to or an incremented version of the new slug -- then the slug doesn't
			// need to change and we can just return

			if ( array_key_exists($model->getKey(), $list) ) {
				return true;
			}

			// does the exact new slug exist?

			if ( in_array($slug, $list) ) {

				// find the "highest" numbered version of the slug and increment it.

				$idx = substr( $collection->first()->{$save_to} , strlen($slug) );
				$idx = ltrim( $idx, $separator );
				$idx = intval( $idx );
				$idx++;

				$slug .= $separator . $idx;

			}

		}


		// update the slug field

		$model->{$save_to} = $slug;


		// done!

		return true;

	}


}
