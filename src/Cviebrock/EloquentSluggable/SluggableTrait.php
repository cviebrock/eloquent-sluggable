<?php namespace Cviebrock\EloquentSluggable;


trait SluggableTrait {


	protected function needsSlugging()
	{
		$save_to = $this->sluggable['save_to'];
		$on_update = $this->sluggable['on_update'];

		return ( !$this->exists || empty($this->{$save_to}) || $on_update );
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
		$separator = $this->sluggable['separator'];
		$method    = $this->sluggable['method'];

		if ( $method === null )
		{
			return \Str::slug($source, $separator);
		}

		if ( $method instanceof Closure )
		{
			return $method($source, $separator);
		}

		if ( is_callable($method) )
		{
			return call_user_func($method, $source, $separator);
		}

		throw new \UnexpectedValueException("Sluggable method is not a callable, closure or null.");

	}


	protected function isSlugValid($slug)
	{
		return !$this->isSlugReserved($slug) &&
			($this->sluggable['unique'] && $this->isSlugUnique($slug));
	}


	protected function isSlugReserved($slug)
	{
		$reserved = $this->sluggable['reserved'];

		if ( $reserved === null )
		{
			return false;
		}

		// check for reserved names
		if ( $reserved instanceof Closure )
		{
			$reserved = $reserved($this);
		}

		if ( is_array($reserved) )
		{
			return in_array($slug, $reserved);
		}

		throw new \UnexpectedValueException("Sluggable reserved is not null, an array, or a closure that returns null/array.");
	}


	protected function isSlugUnique($slug)
	{
		$instance = new static;
		$query = $instance->where( $this->sluggable['save_to'], $slug );
		if ( $this->sluggable['include_trashed'] )
		{
			$query = $query->withTrashed();
		}
		if ( $this->exists )
		{
			$query = $query->where( $this->getKeyName(), '!=', $this->getKey() );
		}
		return $query->count() == 0;
	}



	protected function incrementSlug($base, $slug)
	{

		$separator = $this->sluggable['separator'];

		if( strpos($slug, $base.$separator) === 0)
		{
			$remainder = substr($slug, strlen($base.$separator));

			// check that it's numeric
			if ( preg_match( '/^\d+$/', $remainder ) )
			{
				// increment and return
				$remainder = (int)$remainder + 1;
				return $base.$separator.$remainder;
			}
		}

		// otherwise, just add first increment

		return $base.$separator.'1';

	}


	protected function setSlug($slug)
	{
		$attribute = $this->sluggable['save_to'];
		$this->{$attribute} = $slug;
	}


	public function slug($force=false)
	{

		$config = \App::make('config')->get('eloquent-sluggable::config');
		$this->sluggable = array_merge( $config, $this->sluggable );

		if (!$force && !$this->needsSlugging()) return true;

		$source = $this->getSlugSource();
		$slug = $base = $this->generateSlug($source);
		while (!$this->isSlugValid($slug))
		{
			$slug = $this->incrementSlug($base, $slug);
		}
		$this->setSlug($slug);

		return true;
	}


	public static function findBySlug($slug)
	{

		$instance = new static;

		return $instance->where( $this->sluggable['save_to'], $slug )->get();
	}

}