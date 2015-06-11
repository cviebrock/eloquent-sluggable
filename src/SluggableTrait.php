<?php namespace Cviebrock\EloquentSluggable;

use Illuminate\Support\Str;

trait SluggableTrait {


	protected function needsSlugging()
	{
		$config = $this->getSluggableConfig();
		$save_to = $config['save_to'];
		$on_update = $config['on_update'];

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
		$config = $this->getSluggableConfig();
		$from = $config['build_from'];

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
		$config = $this->getSluggableConfig();
		$separator  = $config['separator'];
		$method     = $config['method'];
		$max_length = $config['max_length'];

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
		$config = $this->getSluggableConfig();
		$reserved = $config['reserved'];

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
				return $slug . $config['separator'] . '1';
			}
			return $slug;
		}

		throw new \UnexpectedValueException("Sluggable reserved is not null, an array, or a closure that returns null/array.");

	}

	protected function makeSlugUnique($slug)
	{
		$config = $this->getSluggableConfig();
		if (!$config['unique']) return $slug;

		$separator  = $config['separator'];
		$use_cache  = $config['use_cache'];
		$save_to    = $config['save_to'];

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

		$suffix = $this->generateSuffix($slug, $list);

		return $slug . $separator . $suffix;

	}

	/**
	 * @param $slug
	 * @param $list
	 *
	 * @return mixed
	 */
	protected function generateSuffix($slug, $list)
	{
		$method = array_get($this->sluggable, 'uniqueMethod', null);

		if ($method === null) {
			$config = $this->getSluggableConfig();
			$separator  = $config['separator'];
			$len = strlen($slug . $separator);

			array_walk($list, function (&$value, $key) use ($len) {
				$value = intval(substr($value, $len));
			});

			// find the highest increment
			rsort($list);
			return reset($list) + 1;
		} elseif (is_callable([$this, $method])) {
			return call_user_func([$this, $method], $slug, $list);
		}

		throw new \UnexpectedValueException("Sluggable uniqueMethod is not callable or null.");
	}

	protected function getExistingSlugs($slug)
	{
		$config = $this->getSluggableConfig();
		$save_to         = $config['save_to'];
		$include_trashed = $config['include_trashed'];

		$instance = new static;

		$query = $instance->where( $save_to, 'LIKE', $slug.'%' );

		// include trashed models if required
		if ( $include_trashed && $this->usesSoftDeleting() )
		{
			$query = $query->withTrashed();
		}

		// get a list of all matching slugs
		$list = $query->lists($save_to, $this->getKeyName());

		return $list;
	}


	protected function usesSoftDeleting() {
		return method_exists($this,'BootSoftDeletes');
	}


	protected function setSlug($slug)
	{
		$config = $this->getSluggableConfig();
		$save_to = $config['save_to'];
		$this->setAttribute( $save_to, $slug );
	}


	public function getSlug()
	{
		$config = $this->getSluggableConfig();
		$save_to = $config['save_to'];
		return $this->getAttribute( $save_to );
	}


	public function sluggify($force=false)
	{
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

	public function scopeWhereSlug($scope,$slug)
	{
		$config = $this->getSluggableConfig();
		return $scope->where($config['save_to'],$slug);
	}

	public function scopeFindBySlug($scope,$slug)
	{
		return $scope->whereSlug($slug)->first();
	}

	public function scopeFindBySlugOrFail($scope,$slug)
	{
		return $scope->whereSlug($slug)->firstOrFail();
	}

	protected function getSluggableConfig()
	{
		$defaults = \App::make('config')->get('sluggable');
		if (property_exists($this, 'sluggable'))
		{
 			return array_merge($defaults, $this->sluggable);
		}
		return $defaults;
	}

	/**
	 * Simple find by Id if it's numeric or slug if not. Fail if not found.
	 *
	 * @return Model/Collection
	 */

	public function scopeFindBySlugOrIdOrFail($scope, $slug)
    {
        if (is_numeric($slug) && $slug > 0) {
            return $scope->findOrFail($slug);
        }
        return $scope->findBySlugOrFail($slug);
    }

	/**
	 * Simple find by Id if it's numeric or slug if not.
	 *
	 * @return Model/Collection
	 */

    public function scopeFindBySlugOrId($scope, $slug)
    {
        if (is_numeric($slug) && $slug > 0) {
            return $scope->find($slug);
        }
	    return $scope->findBySlug($slug);
    }
}
