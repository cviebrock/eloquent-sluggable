<?php

namespace Cviebrock\EloquentSluggable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class SluggableScopeHelpers.
 *
 * Helper trait for defining the primary slug of a model
 * and providing useful scopes and query methods.
 */
trait SluggableScopeHelpers
{
    /**
     * Primary slug column of this model.
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
     */
    public function getSlugKey(): string
    {
        return $this->getAttribute($this->getSlugKeyName());
    }

    /**
     * Query scope for finding a model by its primary slug.
     */
    public function scopeWhereSlug(Builder $scope, string $slug): Builder
    {
        return $scope->where($this->getSlugKeyName(), $slug);
    }

    /**
     * Find a model by its primary slug.
     *
     * @return Collection|Model|static|static[]|null
     */
    public static function findBySlug(string $slug, array $columns = ['*'])
    {
        return static::whereSlug($slug)->first($columns);
    }

    /**
     * Find a model by its primary slug or throw an exception.
     *
     * @return Model|static
     *
     * @throws ModelNotFoundException
     */
    public static function findBySlugOrFail(string $slug, array $columns = ['*'])
    {
        return static::whereSlug($slug)->firstOrFail($columns);
    }
}
