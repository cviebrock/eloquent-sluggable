<?php namespace Cviebrock\EloquentSluggable;

use Cviebrock\EloquentSluggable\Events\Slugged;
use Cviebrock\EloquentSluggable\Events\Slugging;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Database\Eloquent\Model;


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
        static::saving(function (Model $model) {

            if ($model->fireModelEvent(Slugging::class) === false) {
                return false;
            }

            (new SlugService($model))->slug();

            $model->fireModelEvent(Slugged::class);
        });
    }

    /**
     * Clone the model into a new, non-existing instance.
     *
     * @param  array|null $except
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function replicate(array $except = null)
    {
        $instance = parent::replicate($except);
        (new SlugService($instance))->slug(true);

        return $instance;
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    abstract public function sluggable();
}
