<?php namespace Cviebrock\EloquentSluggable\Services;

use Cocur\Slugify\Slugify;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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
     *
     * @return bool
     */
    public function slug(Model $model, bool $force = false): bool
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

            if ($slug !== null) {
                $this->model->setAttribute($attribute, $slug);
                $attributes[] = $attribute;
            }
        }

        return $this->model->isDirty($attributes);
    }

    /**
     * Get the sluggable configuration for the current model,
     * including default values where not specified.
     *
     * @param array $overrides
     *
     * @return array
     */
    public function getConfiguration(array $overrides = []): array
    {
        $defaultConfig = config('sluggable', []);

        return array_merge($defaultConfig, $overrides);
    }

    /**
     * Build the slug for the given attribute of the current model.
     *
     * @param string $attribute
     * @param array $config
     * @param bool $force
     *
     * @return null|string
     */
    public function buildSlug(string $attribute, array $config, bool $force = null)
    {
        $slug = $this->model->getAttribute($attribute);

        if ($force || $this->needsSlugging($attribute, $config)) {
            $source = $this->getSlugSource($config['source']);

            if ($source || is_numeric($source)) {
                $slug = $this->generateSlug($source, $config, $attribute);
                $slug = $this->validateSlug($slug, $config, $attribute);
                $slug = $this->makeSlugUnique($slug, $config, $attribute);
            }
        }

        return $slug;
    }

    /**
     * Determines whether the model needs slugging.
     *
     * @param string $attribute
     * @param array $config
     *
     * @return bool
     */
    protected function needsSlugging(string $attribute, array $config): bool
    {
        if (
            $config['onUpdate'] === true ||
            empty($this->model->getAttributeValue($attribute))
        ) {
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
     *
     * @return string
     */
    protected function getSlugSource($from): string
    {
        if (is_null($from)) {
            return $this->model->__toString();
        }

        $sourceStrings = array_map(function($key) {
            $value = data_get($this->model, $key);
            if (is_bool($value)) {
                $value = (int) $value;
            }

            return $value;
        }, (array) $from);

        return implode($sourceStrings, ' ');
    }

    /**
     * Generate a slug from the given source string.
     *
     * @param string $source
     * @param array $config
     * @param string $attribute
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    protected function generateSlug(string $source, array $config, string $attribute): string
    {
        $separator = $config['separator'];
        $method = $config['method'];
        $maxLength = $config['maxLength'];
        $maxLengthKeepWords = $config['maxLengthKeepWords'];

        if ($method === null) {
            $slugEngine = $this->getSlugEngine($attribute);
            $slug = $slugEngine->slugify($source, $separator);
        } elseif (is_callable($method)) {
            $slug = call_user_func($method, $source, $separator);
        } else {
            throw new \UnexpectedValueException('Sluggable "method" for ' . get_class($this->model) . ':' . $attribute . ' is not callable nor null.');
        }

        $len = mb_strlen($slug);
        if (is_string($slug) && $maxLength && $len > $maxLength) {
            $reverseOffset = $maxLength - $len;
            $lastSeparatorPos = mb_strrpos($slug, $separator, $reverseOffset);
            if ($maxLengthKeepWords && $lastSeparatorPos !== false) {
                $slug = mb_substr($slug, 0, $lastSeparatorPos);
            } else {
                $slug = trim(mb_substr($slug, 0, $maxLength), $separator);
            }
        }

        return $slug;
    }

    /**
     * Return a class that has a `slugify()` method, used to convert
     * strings into slugs.
     *
     * @param string $attribute
     *
     * @return \Cocur\Slugify\Slugify
     */
    protected function getSlugEngine(string $attribute): Slugify
    {
        static $slugEngines = [];

        $key = get_class($this->model) . '.' . $attribute;

        if (!array_key_exists($key, $slugEngines)) {
            $engine = new Slugify();
            if (method_exists($this->model, 'customizeSlugEngine')) {
                $engine = $this->model->customizeSlugEngine($engine, $attribute);
            }

            $slugEngines[$key] = $engine;
        }

        return $slugEngines[$key];
    }

    /**
     * Checks that the given slug is not a reserved word.
     *
     * @param string $slug
     * @param array $config
     * @param string $attribute
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    protected function validateSlug(string $slug, array $config, string $attribute): string
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
                $method = $config['uniqueSuffix'];
                if ($method === null) {
                    $suffix = $this->generateSuffix($slug, $separator, collect($reserved));
                } elseif (is_callable($method)) {
                    $suffix = $method($slug, $separator, collect($reserved));
                } else {
                    throw new \UnexpectedValueException('Sluggable "uniqueSuffix" for ' . get_class($this->model) . ':' . $attribute . ' is not null, or a closure.');
                }

                return $slug . $separator . $suffix;
            }

            return $slug;
        }

        throw new \UnexpectedValueException('Sluggable "reserved" for ' . get_class($this->model) . ':' . $attribute . ' is not null, an array, or a closure that returns null/array.');
    }

    /**
     * Checks if the slug should be unique, and makes it so if needed.
     *
     * @param string $slug
     * @param array $config
     * @param string $attribute
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    protected function makeSlugUnique(string $slug, array $config, string $attribute): string
    {
        if (!$config['unique']) {
            return $slug;
        }

        $separator = $config['separator'];

        // find all models where the slug is like the current one
        $list = $this->getExistingSlugs($slug, $attribute, $config);

        // if ...
        // 	a) the list is empty, or
        // 	b) our slug isn't in the list
        // ... we are okay
        if (
            $list->count() === 0 ||
            $list->contains($slug) === false
        ) {
            return $slug;
        }

        // if our slug is in the list, but
        // 	a) it's for our model, or
        //  b) it looks like a suffixed version of our slug
        // ... we are also okay (use the current slug)
        if ($list->has($this->model->getKey())) {
            $currentSlug = $list->get($this->model->getKey());

            if (
                $currentSlug === $slug ||
                strpos($currentSlug, $slug) === 0
            ) {
                return $currentSlug;
            }
        }

        $method = $config['uniqueSuffix'];
        if ($method === null) {
            $suffix = $this->generateSuffix($slug, $separator, $list);
        } elseif (is_callable($method)) {
            $suffix = $method($slug, $separator, $list);
        } else {
            throw new \UnexpectedValueException('Sluggable "uniqueSuffix" for ' . get_class($this->model) . ':' . $attribute . ' is not null, or a closure.');
        }

        return $slug . $separator . $suffix;
    }

    /**
     * Generate a unique suffix for the given slug (and list of existing, "similar" slugs.
     *
     * @param string $slug
     * @param string $separator
     * @param \Illuminate\Support\Collection $list
     *
     * @return string
     */
    protected function generateSuffix(string $slug, string $separator, Collection $list): string
    {
        $len = strlen($slug . $separator);

        // If the slug already exists, but belongs to
        // our model, return the current suffix.
        if ($list->search($slug) === $this->model->getKey()) {
            $suffix = explode($separator, $slug);

            return end($suffix);
        }

        $list->transform(function($value, $key) use ($len) {
            return (int) substr($value, $len);
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
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getExistingSlugs(string $slug, string $attribute, array $config): Collection
    {
        $includeTrashed = $config['includeTrashed'];

        $query = $this->model->newQuery()
            ->findSimilarSlugs($attribute, $config, $slug);

        // use the model scope to find similar slugs
        if (method_exists($this->model, 'scopeWithUniqueSlugConstraints')) {
            $query->withUniqueSlugConstraints($this->model, $attribute, $config, $slug);
        }

        // include trashed models if required
        if ($includeTrashed && $this->usesSoftDeleting()) {
            $query->withTrashed();
        }

        // get the list of all matching slugs
        $results = $query->select([$attribute, $this->model->getQualifiedKeyName()])
            ->get()
            ->toBase();

        // key the results and return
        return $results->pluck($attribute, $this->model->getKeyName());
    }

    /**
     * Does this model use softDeleting?
     *
     * @return bool
     */
    protected function usesSoftDeleting(): bool
    {
        return method_exists($this->model, 'bootSoftDeletes');
    }

    /**
     * Generate a unique slug for a given string.
     *
     * @param \Illuminate\Database\Eloquent\Model|string $model
     * @param string $attribute
     * @param string $fromString
     * @param array|null $config
     *
     * @return string
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public static function createSlug($model, string $attribute, string $fromString, array $config = null): string
    {
        if (is_string($model)) {
            $model = new $model;
        }
        /** @var static $instance */
        $instance = (new static())->setModel($model);

        if ($config === null) {
            $config = Arr::get($model->sluggable(), $attribute);
            if ($config === null) {
                $modelClass = get_class($model);
                throw new \InvalidArgumentException("Argument 2 passed to SlugService::createSlug ['{$attribute}'] is not a valid slug attribute for model {$modelClass}.");
            }
        } elseif (!is_array($config)) {
            throw new \UnexpectedValueException('SlugService::createSlug expects an array or null as the fourth argument; ' . gettype($config) . ' given.');
        }

        $config = $instance->getConfiguration($config);

        $slug = $instance->generateSlug($fromString, $config, $attribute);
        $slug = $instance->validateSlug($slug, $config, $attribute);
        $slug = $instance->makeSlugUnique($slug, $config, $attribute);

        return $slug;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return $this
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }
}
