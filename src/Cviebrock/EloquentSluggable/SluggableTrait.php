<?php namespace Cviebrock\EloquentSluggable;

use Illuminate\Support\Str;

trait SluggableTrait {


	protected function needsSlugging()
	{
		$save_to = $this->sluggable['save_to'];
		$on_update = $this->sluggable['on_update'];

		if (empty($this->{$save_to})) {
			return true;
		}

		if ($this->isDirty($save_to)) {
			return false;
		}

		return ( !$this->exists || $on_update );
	}


	protected function getSlugSource()
	{
		$from = $this->sluggable['build_from'];

		if ( is_null($from) )
		{
			return $this->__toString();
		}

		$source = array_map(
			function($attribute)
			{
				return $this->{$attribute};
			},
			(array) $from
		);

		return join($source, ' ');
	}



	protected function generateSlug($source)
	{
		$separator  = $this->sluggable['separator'];
		$method     = $this->sluggable['method'];
		$max_length = $this->sluggable['max_length'];

		if ( $method === null )
		{
			$slug = Str::slug($source, $separator);
		}
		elseif ( is_callable($method) )
		{
			$slug = call_user_func($method, $source, $separator);
		}
		else
		{
			throw new \UnexpectedValueException("Sluggable method is not callable or null.");
		}

		if (is_string($slug) && $max_length)
		{
			$slug = substr($slug, 0, $max_length);
		}

		return $slug;
	}


	protected function validateSlug($slug)
	{

		$reserved = $this->sluggable['reserved'];

		if ( $reserved === null ) return $slug;

		// check for reserved names
		if ( $reserved instanceof \Closure )
		{
			$reserved = $reserved($this);
		}

		if ( is_array($reserved) )
		{
			if ( in_array($slug, $reserved) )
			{
				return $slug . $this->sluggable['separator'] . '1';
			}
			return $slug;
		}

		throw new \UnexpectedValueException("Sluggable reserved is not null, an array, or a closure that returns null/array.");

	}

	protected function makeSlugUnique($slug)
	{
		if (!$this->sluggable['unique']) return $slug;

		$separator  = $this->sluggable['separator'];
		$use_cache  = $this->sluggable['use_cache'];
		$save_to    = $this->sluggable['save_to'];

		// if using the cache, check if we have an entry already instead
		// of querying the database
		if ( $use_cache )
		{
			$increment = \Cache::tags('sluggable')->get($slug);
			if ( $increment === null )
			{
				\Cache::tags('sluggable')->put($slug, 0, $use_cache);
			}
			else
			{
				\Cache::tags('sluggable')->put($slug, ++$increment, $use_cache);
				$slug .= $separator . $increment;
			}
			return $slug;
		}

		// no cache, so we need to check directly
		// find all models where the slug is like the current one
		$list = $this->getExistingSlugs($slug);

		// if ...
		// 	a) the list is empty
		// 	b) our slug isn't in the list
		// 	c) our slug is in the list and it's for our model
		// ... we are okay
		if (
			count($list)===0 ||
			!in_array($slug, $list) ||
			( array_key_exists($this->getKey(), $list) && $list[$this->getKey()]===$slug )
		)
		{
			return $slug;
		}


		// map our list to keep only the increments
		$len = strlen($slug.$separator);
		array_walk($list, function(&$value, $key) use ($len)
		{
			$value = intval(substr($value, $len));
		});

		// find the highest increment
		rsort($list);
		$increment = reset($list) + 1;

		return $slug . $separator . $increment;

	}


	protected function getExistingSlugs($slug)
	{
		$save_to         = $this->sluggable['save_to'];
		$include_trashed = $this->sluggable['include_trashed'];

		$instance = new static;

		$query = $instance->where( $save_to, 'LIKE', $slug.'%' );

		// include trashed models if required
		if ( $include_trashed && $instance->usesSoftDeleting() )
		{
			$query = $query->withTrashed();
		}

		// get a list of all matching slugs
		$list = $query->lists($save_to, $this->getKeyName());

		return $list;
	}

	protected function usesSoftDeleting() {
		if ( in_array('Illuminate\Database\Eloquent\SoftDeletingTrait', class_uses($this) ) ) {
			return true;
		}
		return ( property_exists($this,'softDelete') && $this->softDelete==true );
	}


	protected function setSlug($slug)
	{
		$save_to = $this->sluggable['save_to'];
		$this->setAttribute( $save_to, $slug );
	}


	public function getSlug()
	{
		$save_to = $this->sluggable['save_to'];
		return $this->getAttribute( $save_to );
	}


	public function sluggify($force=false)
	{
		$config = \App::make('config')->get('eloquent-sluggable::config');
		$this->sluggable = array_merge( $config, $this->sluggable );

		if ($force || $this->needsSlugging())
		{

			$source = $this->getSlugSource();
			$slug = $this->generateSlug($source);

			$slug = $this->validateSlug($slug);
			$slug = $this->makeSlugUnique($slug);

			$this->setSlug($slug);
		}

		return $this;
	}


	public function resluggify()
	{
		return $this->sluggify(true);
	}


	public static function getBySlug($slug)
	{

		$instance = new static;

		$config = \App::make('config')->get('eloquent-sluggable::config');
		$config = array_merge( $config, $instance->sluggable );

		return $instance->where( $config['save_to'], $slug )->get();
	}

	public static function findBySlug($slug)
	{

		return static::getBySlug($slug)->first();
	}

}
