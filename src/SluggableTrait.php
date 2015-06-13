<?php namespace Cviebrock\EloquentSluggable;

use Cocur\Slugify\Slugify;
use Illuminate\Support\Collection;


/**
 * Class SluggableTrait
 *
 * @package Cviebrock\EloquentSluggable
 */
trait SluggableTrait {

	/**
	 * Determines whether the model needs slugging.
	 *
	 * @return bool
	 */
	protected function needsSlugging() {
		$config = $this->getSluggableConfig();
		$save_to = $config['save_to'];
		$on_update = $config['on_update'];

		if (empty($this->{$save_to})) {
			return true;
		}

		if ($this->isDirty($save_to)) {
			return false;
		}

		return (!$this->exists || $on_update);
	}

	/**
	 * Get the source string for the slug.
	 *
	 * @return string
	 */
	protected function getSlugSource() {
		$config = $this->getSluggableConfig();
		$from = $config['build_from'];

		if (is_null($from)) {
			return $this->__toString();
		}

		$source = array_map(
			function ($attribute) {
				return $this->{$attribute};
			},
			(array) $from
		);

		return join($source, ' ');
	}

	/**
	 * Generate a slug from the given source string.
	 *
	 * @param string $source
	 * @return string
	 * @throws \UnexpectedValueException
	 */
	protected function generateSlug($source) {
		$config = $this->getSluggableConfig();
		$separator = $config['separator'];
		$method = $config['method'];
		$max_length = $config['max_length'];

		if ($method === null) {
			$slug = (new Slugify)->slugify($source, $separator);
		} elseif (is_callable($method)) {
			$slug = call_user_func($method, $source, $separator);
		} else {
			throw new \UnexpectedValueException('Sluggable method is not callable or null.');
		}

		if (is_string($slug) && $max_length) {
			$slug = substr($slug, 0, $max_length);
		}

		return $slug;
	}

	/**
	 * Checks that the given slug is not a reserved word.
	 *
	 * @param string $slug
	 * @return string
	 * @throws \UnexpectedValueException
	 */
	protected function validateSlug($slug) {
		$config = $this->getSluggableConfig();
		$reserved = $config['reserved'];

		if ($reserved === null) {
			return $slug;
		}

		// check for reserved names
		if ($reserved instanceof \Closure) {
			$reserved = $reserved($this);
		}

		if (is_array($reserved)) {
			if (in_array($slug, $reserved)) {
				return $slug . $config['separator'] . '1';
			}

			return $slug;
		}

		throw new \UnexpectedValueException('Sluggable reserved is not null, an array, or a closure that returns null/array.');
	}

	/**
	 * Checks if the slug should be unique, and makes it so if needed.
	 *
	 * @param string $slug
	 * @return string
	 */
	protected function makeSlugUnique($slug) {
		$config = $this->getSluggableConfig();
		if (!$config['unique']) {
			return $slug;
		}

		$separator = $config['separator'];

		// find all models where the slug is like the current one
		$list = $this->getExistingSlugs($slug);

		// if ...
		// 	a) the list is empty
		// 	b) our slug isn't in the list
		// 	c) our slug is in the list and it's for our model
		// ... we are okay
		if (
			count($list) === 0 ||
			!in_array($slug, $list) ||
			(array_key_exists($this->getKey(), $list) && $list[$this->getKey()] === $slug)
		) {
			return $slug;
		}

		$suffix = $this->generateSuffix($slug, $list);

		return $slug . $separator . $suffix;
	}

	/**
	 * Generate a unique suffix for the given slug (and list of existing, "similar" slugs.
	 *
	 * @param string $slug
	 * @param array $list
	 *
	 * @return string
	 */
	protected function generateSuffix($slug, $list) {
		$config = $this->getSluggableConfig();
		$separator = $config['separator'];
		$len = strlen($slug . $separator);

		array_walk($list, function (&$value, $key) use ($len) {
			$value = intval(substr($value, $len));
		});

		// find the highest increment
		rsort($list);

		return reset($list) + 1;
	}

	/**
	 * Get all existing slugs that are similar to the given slug.
	 *
	 * @param string $slug
	 * @return array
	 */
	protected function getExistingSlugs($slug) {
		$config = $this->getSluggableConfig();
		$save_to = $config['save_to'];
		$include_trashed = $config['include_trashed'];

		$instance = new static;

		$query = $instance->where($save_to, 'LIKE', $slug . '%');

		// include trashed models if required
		if ($include_trashed && $this->usesSoftDeleting()) {
			$query = $query->withTrashed();
		}

		// get a list of all matching slugs
		$list = $query->lists($save_to, $this->getKeyName());

		// Laravel 5.0/5.1 check
		return $list instanceof Collection ? $list->all() : $list;
	}

	/**
	 * Does this model use softDeleting?
	 *
	 * @return bool
	 */
	protected function usesSoftDeleting() {
		return method_exists($this, 'BootSoftDeletes');
	}

	/**
	 * Set the slug manually.
	 *
	 * @param string $slug
	 */
	protected function setSlug($slug) {
		$config = $this->getSluggableConfig();
		$save_to = $config['save_to'];
		$this->setAttribute($save_to, $slug);
	}

	/**
	 * Get the current slug.
	 *
	 * @return mixed
	 */
	public function getSlug() {
		$config = $this->getSluggableConfig();
		$save_to = $config['save_to'];

		return $this->getAttribute($save_to);
	}

	/**
	 * Manually slug the current model.
	 *
	 * @param bool $force
	 * @return $this
	 */
	public function sluggify($force = false) {
		if ($force || $this->needsSlugging()) {
			$source = $this->getSlugSource();
			$slug = $this->generateSlug($source);

			$slug = $this->validateSlug($slug);
			$slug = $this->makeSlugUnique($slug);

			$this->setSlug($slug);
		}

		return $this;
	}

	/**
	 * Force slugging of current model.
	 *
	 * @return SluggableTrait
	 */
	public function resluggify() {
		return $this->sluggify(true);
	}

	/**
	 * Query scope for finding a model by its slug.
	 *
	 * @param $scope
	 * @param $slug
	 * @return mixed
	 */
	public function scopeWhereSlug($scope, $slug) {
		$config = $this->getSluggableConfig();

		return $scope->where($config['save_to'], $slug);
	}

	/**
	 * Find a model by slug.
	 *
	 * @param $slug
	 * @return Model|null.
	 */
	public static function findBySlug($slug) {
		return self::whereSlug($slug)->first();
	}

	/**
	 * Find a model by slug or fail.
	 *
	 * @param $slug
	 * @return Model
	 */
	public static function findBySlugOrFail($slug) {
		return self::whereSlug($slug)->firstOrFail();
	}

	/**
	 * Get the default configuration and merge in any model-specific overrides.
	 *
	 * @return array
	 */
	protected function getSluggableConfig() {
		$defaults = app('config')->get('sluggable');
		if (property_exists($this, 'sluggable')) {
			return array_merge($defaults, $this->sluggable);
		}

		return $defaults;
	}

	/**
	 * Simple find by Id if it's numeric or slug if not. Fail if not found.
	 *
	 * @param $slug
	 * @return Model|Collection
	 */
	public static function findBySlugOrIdOrFail($slug) {
		if (is_numeric($slug) && $slug > 0) {
			return self::findOrFail($slug);
		}

		return self::findBySlugOrFail($slug);
	}

	/**
	 * Simple find by Id if it's numeric or slug if not.
	 *
	 * @param $slug
	 * @return Model|Collection|null
	 */
	public static function findBySlugOrId($slug) {
		if (is_numeric($slug) && $slug > 0) {
			return self::find($slug);
		}

		return self::findBySlug($slug);
	}
}
