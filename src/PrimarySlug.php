<?php

namespace Cviebrock\EloquentSluggable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class PrimarySlug
 * Helper trait for defining the primary slug of a model.
 *
 * @package Cviebrock\EloquentSluggable
 */
trait PrimarySlug
{

    /**
     * Primary slug column of this model.
     *
     * @return string
     */
    public function getSlugKeyName()
    {
        if (property_exists($this, 'slugKeyName')) {
            return $this->slugKeyName;
        }

        return array_first(array_keys($this->sluggable()));
    }

    /**
     * Primary slug value of this model.
     *
     * @return string
     */
    public function getSlugKey()
    {
        return $this->getAttribute($this->getSlugKeyName());
    }

    /**
     * Query scope for finding a model by primary slug.
     *
     * @param Builder $scope
     * @param string $slug
     * @return Builder
     */
    public function scopeWhereSlug($scope, $slug)
    {
        return $scope->where($this->getSlugKeyName(), $slug);
    }

    /**
     * Find Model by primary slug. Fallback to find by id. Fail if not found.
     *
     * @param string $slug
     * @return Model|Collection|null
     */
    public static function findBySlug($slug)
    {
        return static::whereSlug($slug)->first();
    }

    /**
     * Find by primary slug. Fail if not found.
     *
     * @param string $slug
     * @return Model
     */
    public static function findBySlugOrFail($slug)
    {
        return static::whereSlug($slug)->firstOrFail();
    }

}
