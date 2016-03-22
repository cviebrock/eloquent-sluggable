<?php namespace Cviebrock\EloquentSluggable\Services;

use Cocur\Slugify\Slugify;
use Cviebrock\EloquentSluggable\Events\Slugged;
use Cviebrock\EloquentSluggable\Events\Slugging;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * Class SlugService
 *
 * @package Cviebrock\EloquentSluggable\Services
 */
class SlugService
{

    /**
     * @var \Illuminate\Database\Eloquent\Model;
     */
    protected $model;

    /**
     * SlugService constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Slug the current model.
     *
     * @param bool $force
     */
    public function slug($force = false)
    {
        if (event(new Slugging($this->model)) === false) {
            return;
        }

        foreach ($this->model->sluggable() as $attribute => $config) {
            if (is_numeric($attribute)) {
                $attribute = $config;
                $config = $this->getConfiguration();
            } else {
                $config = $this->getConfiguration($config);
            }

            $slug = $this->buildSlug($attribute, $config, $force);

            $this->model->setAttribute($attribute, $slug);
        }

        if ($this->saveModel()) {
            event(new Slugged($this->model));
        }
    }

    /**
     * Get the sluggable configuration for the current model,
     * including default values where not specified.
     *
     * @param array $overrides
     * @return array
     */
    public function getConfiguration(array $overrides = [])
    {
        static $defaultConfig = null;
        if ($defaultConfig === null) {
            $defaultConfig = app('config')->get('sluggable');
        }

        return array_merge($defaultConfig, $overrides);
    }

    /**
     * Build the slug for the given attribute of the current model.
     *
     * @param string $attribute
     * @param array $config
     * @param bool $force
     * @return null|string
     */
    public function buildSlug($attribute, array $config, $force = null)
    {
        $slug = null;

        if ($force || $this->needsSlugging($attribute, $config)) {
            $source = $this->getSlugSource($config['source']);

            if ($source) {
                $slug = $this->generateSlug($source, $config);

                $slug = $this->validateSlug($slug, $config);

                if ($config['unique']) {
                    $slug = $this->makeSlugUnique($slug, $attribute, $config);
                }
            }
        }

        return $slug;
    }

    /**
     * Save the model to the database.
     *
     * @return bool
     */
    protected function saveModel()
    {
        return $this->model->save();
    }

    /**
     * Determines whether the model needs slugging.
     *
     * @param string $attribute
     * @param array $config
     * @return bool
     */
    protected function needsSlugging($attribute, array $config)
    {
        if (empty($this->model->getAttributeValue($attribute))) {
            return true;
        }

        if ($this->model->isDirty($attribute)) {
            return false;
        }

        return (!$this->model->exists || $config['onUpdate']);
    }

    /**
     * Get the source string for the slug.
     *
     * @param mixed $from
     * @return string
     */
    protected function getSlugSource($from)
    {
        if (is_null($from)) {
            return $this->model->__toString();
        }

        $source = array_map([$this, 'generateSource'], (array)$from);

        return join($source, ' ');
    }

    /**
     * Iterate over the model properties to generate the source string.
     *
     * @param string $key
     * @return string|null
     */
    protected function generateSource($key)
    {
        $value = $this->model;

        if (isset($value->{$key})) {
            return $value->{$key};
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_object($value) || !$tmp = $value->{$segment}) {
                return null;
            }

            $value = $value->{$segment};
        }

        return $value;
    }

    /**
     * Generate a slug from the given source string.
     *
     * @param string $source
     * @param array $config
     * @return string
     */
    protected function generateSlug($source, array $config)
    {
        $separator = $config['separator'];
        $method = $config['method'];
        $maxLength = $config['maxLength'];

        if ($method === null) {
            $slugEngine = $this->getSlugEngine();
            $slug = $slugEngine->slugify($source, $separator);
        } elseif (is_callable($method)) {
            $slug = call_user_func($method, $source, $separator);
        } else {
            throw new \UnexpectedValueException('Sluggable method is not callable or null.');
        }

        if (is_string($slug) && $maxLength) {
            $slug = mb_substr($slug, 0, $maxLength);
        }

        return $slug;
    }

    /**
     * Return a class that has a `slugify()` method, used to convert
     * strings into slugs.
     *
     * @return Slugify
     */
    protected function getSlugEngine()
    {
        static $slugEngine;
        if (!$slugEngine) {
            $slugEngine = new Slugify();
        }

        return $slugEngine;
    }

    /**
     * Checks that the given slug is not a reserved word.
     *
     * @param string $slug
     * @param array $config
     * @return string
     */
    protected function validateSlug($slug, array $config)
    {
        $separator = $config['separator'];
        $reserved = $config['reserved'];

        if ($reserved === null) {
            return $slug;
        }

        // check for reserved names
        if ($reserved instanceof \Closure) {
            $reserved = $reserved($this->model);
        }

        if (is_array($reserved)) {
            if (in_array($slug, $reserved)) {
                return $slug . $separator . '1';
            }

            return $slug;
        }

        throw new \UnexpectedValueException('Sluggable reserved is not null, an array, or a closure that returns null/array.');
    }

    /**
     * Checks if the slug should be unique, and makes it so if needed.
     *
     * @param string $slug
     * @param string $attribute
     * @param array $config
     * @return string
     */
    protected function makeSlugUnique($slug, $attribute, array $config)
    {
        $separator = $config['separator'];

        // find all models where the slug is like the current one
        $list = $this->getExistingSlugs($slug, $attribute, $config);

        // if ...
        // 	a) the list is empty
        // 	b) our slug isn't in the list
        // 	c) our slug is in the list and it's for our model
        // ... we are okay
        if (
          count($list) === 0 ||
          !in_array($slug, $list) ||
          (
            array_key_exists($this->model->getKey(), $list) &&
            $list[$this->model->getKey()] === $slug
          )
        ) {
            return $slug;
        }

        $suffix = $this->generateSuffix($slug, $separator, $list);

        return $slug . $separator . $suffix;
    }

    /**
     * Generate a unique suffix for the given slug (and list of existing, "similar" slugs.
     *
     * @param string $slug
     * @param string $separator
     * @param array $list
     * @return string
     */
    protected function generateSuffix($slug, $separator, array $list)
    {
        $len = strlen($slug . $separator);

        // If the slug already exists, but belongs to
        // our model, return the current suffix.
        if ($this->model->getKey() === array_search($slug, $list)) {
            $suffix = explode($separator, $slug);

            return end($suffix);
        }

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
     * @param string $attribute
     * @param array $config
     * @return array
     */
    protected function getExistingSlugs($slug, $attribute, array $config)
    {
        $separator = $config['separator'];
        $includeTrashed = $config['includeTrashed'];

        $query = $this->model->newQuery();

        //check for direct match or something that has a separator followed by a suffix
        $query->where(function (Builder $q) use ($attribute, $slug, $separator) {
            $q->where($attribute, $slug)
              ->orWhere($attribute, 'LIKE', $slug . $separator . '%');
        });

        // include trashed models if required
        if ($includeTrashed && $this->model->usesSoftDeleting()) {
            $query->withTrashed();
        }

        // get a list of all matching slugs
        $list = $query->pluck($attribute, $this->model->getKeyName());

        return $list->all();
    }

    /**
     * Does this model use softDeleting?
     *
     * @return bool
     */
    protected function usesSoftDeleting()
    {
        return method_exists($this->model, 'bootSoftDeletes');
    }

    /**
     * Generate a unique slug for a given string.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $attribute
     * @param string $fromString
     * @return string
     */
    public static function createSlug(Model $model, $attribute, $fromString)
    {
        $instance = new self($model);

        $config = array_get($model->sluggable(), $attribute);
        $config = $instance->getConfiguration($config);

        $slug = $instance->generateSlug($fromString, $config);
        $slug = $instance->validateSlug($slug, $config);
        if ($config['unique']) {
            $slug = $instance->makeSlugUnique($slug, $attribute, $config);
        }

        return $slug;
    }

}
