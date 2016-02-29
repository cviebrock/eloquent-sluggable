<?php namespace Cviebrock\EloquentSluggable;

use Cviebrock\EloquentSluggable\Services\SlugService;


/**
 * Class Sluggable
 *
 * @package Cviebrock\EloquentSluggable
 */
trait Sluggable
{

    /**
     * Hook into the Eloquent model events to create or
     * update the slug as required.
     */
    public static function bootSluggable()
    {
        static::creating(function ($model) {
            (new SlugService($model))->slug();
        });

        static::updating(function ($model) {
            (new SlugService($model))->slug();
        });
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    abstract public function sluggable();
}
