<?php namespace Cviebrock\EloquentSluggable;

/**
 * Class SluggableTrait
 *
 * @package Cviebrock\EloquentSluggable
 */
trait SluggableTrait
{

    public static function bootSluggable()
    {
        static::creating(function ($model) {
            (new SlugService($model)->slug());
        });

        static::updating(function ($model) {
            (new SlugService($model)->slug());
        });
    }
}
