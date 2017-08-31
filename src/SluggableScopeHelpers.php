<?php namespace Cviebrock\EloquentSluggable;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class SluggableScopeHelpers
 *
 * Helper trait for defining the primary slug of a model
 * and providing useful scopes and query methods.
 *
 * @package Cviebrock\EloquentSluggable
 */
trait SluggableScopeHelpers
{

    /**
     * Primary slug column of this model.
     *
     * @return string
     */
    public function getSlugKeyName(): string
    {
        if (property_exists($this, 'slugKeyName')) {
            return $this->slugKeyName;
        }

        $config = $this->sluggable();
        $name = reset($config);
        $key = key($config);

        // check for short configuration
        if ($key === 0) {
            return $name;
        }

        return $key;
    }

    /**
     * Primary slug value of this model.
     *
     * @return string
     */
    public function getSlugKey(): string
    {
        return $this->getAttribute($this->getSlugKeyName());
    }

    /**
     * Query scope for finding a model by its primary slug.
     *
     * @param \Illuminate\Database\Eloquent\Builder $scope
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereSlug(Builder $scope, string $slug): Builder
    {
        return $scope->where($this->getSlugKeyName(), $slug);
    }

    /**
     * Find a model by its primary slug.
     *
     * @param string $slug
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static[]|static|null
     */
    public static function findBySlug(string $slug, array $columns = ['*'])
    {
        return static::whereSlug($slug)->first($columns);
    }

    /**
     * Find a model by its primary slug or throw an exception.
     *
     * @param string $slug
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findBySlugOrFail(string $slug, array $columns = ['*'])
    {
        return static::whereSlug($slug)->firstOrFail($columns);
    }
}
