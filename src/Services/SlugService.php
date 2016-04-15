<?php namespace Cviebrock\EloquentSluggable\Services;

use Cocur\Slugify\Slugify;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


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
     * Slug the current model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param bool $force
     * @return bool
     */
    public function slug(Model $model, $force = false)
    {
        $this->setModel($model);

        $attributes = [];

        foreach ($this->model->sluggable() as $attribute => $config) {
            if (is_numeric($attribute)) {
                $attribute = $config;
                $config = $this->getConfiguration();
            } else {
                $config = $this->getConfiguration($config);
            }

            $slug = $this->buildSlug($attribute, $config, $force);

            $this->model->setAttribute($attribute, $slug);

            $attributes[] = $attribute;
        }

        return $this->model->isDirty($attributes);
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
        $slug = $this->model->getAttribute($attribute);

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

        return (!$this->model->exists);
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

        $sourceStrings = array_map(function($key) {
            return array_get($this->model, $key);
        }, (array)$from);

        return join($sourceStrings, ' ');
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
        static $slugEngines = [];

        $modelClass = get_class($this->model);

        if (!array_key_exists($modelClass, $slugEngines)) {
            $engine = new Slugify();
            if (method_exists($this->model, 'customizeSlugEngine')) {
                $engine = $this->model->customizeSlugEngine($engine);
            }

            $slugEngines[$modelClass] = $engine;
        }

        return $slugEngines[$modelClass];
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
          $list->count() === 0 ||
          $list->contains($slug) === false ||
          (
            $list->has($this->model->getKey()) &&
            $list->get($this->model->getKey()) === $slug
          )
        ) {
            return $slug;
        }

        $method = $config['uniqueSuffix'];
        if ($method !== null) {
            $suffix = $method($slug, $separator, $list);
        } else {
            $suffix = $this->generateSuffix($slug, $separator, $list);
        }

        return $slug . $separator . $suffix;
    }

    /**
     * Generate a unique suffix for the given slug (and list of existing, "similar" slugs.
     *
     * @param string $slug
     * @param string $separator
     * @param \Illuminate\Support\Collection $list
     * @return string
     */
    protected function generateSuffix($slug, $separator, Collection $list)
    {
        $len = strlen($slug . $separator);

        // If the slug already exists, but belongs to
        // our model, return the current suffix.
        if ($list->search($slug) === $this->model->getKey()) {
            $suffix = explode($separator, $slug);

            return end($suffix);
        }

        $list->transform(function ($value, $key) use ($len) {
            return intval(substr($value, $len));
        });

        // find the highest value and return one greater.
        return $list->max() + 1;
    }

    /**
     * Get all existing slugs that are similar to the given slug.
     *
     * @param string $slug
     * @param string $attribute
     * @param array $config
     * @return \Illuminate\Support\Collection
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
        if ($includeTrashed && $this->usesSoftDeleting()) {
            $query->withTrashed();
        }

        // get the list of all matching slugs
        return $query->pluck($attribute, $this->model->getKeyName());
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
     * @param \Illuminate\Database\Eloquent\Model|string $model
     * @param string $attribute
     * @param string $fromString
     * @return string
     */
    public static function createSlug($model, $attribute, $fromString)
    {
        if (is_string($model)) {
            $model = new $model;
        }
        $instance = (new self())->setModel($model);

        $config = array_get($model->sluggable(), $attribute);
        $config = $instance->getConfiguration($config);

        $slug = $instance->generateSlug($fromString, $config);
        $slug = $instance->validateSlug($slug, $config);
        if ($config['unique']) {
            $slug = $instance->makeSlugUnique($slug, $attribute, $config);
        }

        return $slug;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return $this
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
        return $this;
    }

}
