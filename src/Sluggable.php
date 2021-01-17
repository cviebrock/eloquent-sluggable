<?php namespace Cviebrock\EloquentSluggable;

use Cocur\Slugify\Slugify;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Database\Eloquent\Builder;
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
    public static function bootSluggable(): void
    {
        static::observe(app(SluggableObserver::class));
    }

    /**
     * Register a slugging model event with the dispatcher.
     *
     * @param \Closure|string $callback
     */
    public static function slugging($callback): void
    {
        static::registerModelEvent('slugging', $callback);
    }

    /**
     * Register a slugged model event with the dispatcher.
     *
     * @param \Closure|string $callback
     */
    public static function slugged($callback): void
    {
        static::registerModelEvent('slugged', $callback);
    }

    /**
     * @inheritDoc
     */
    public function replicate(array $except = null)
    {
        $instance = parent::replicate($except);
        (new SlugService())->slug($instance, true);

        return $instance;
    }

    /**
     * Return the event name that should be listened to for generating slugs.
     *
     * Can be one of:
     * - SluggableObserver::SAVING (to generate the slug before the model is saved)
     * - SluggableObserver::SAVED (to generate the slug after the model is saved)
     *
     * The second option is required if the primary key is to be part of the slug
     * source, as it won't be set during the "saving" event.
     *
     * @return string
     */
    public function sluggableEvent(): string
    {
        return SluggableObserver::SAVING;
    }

    /**
     * Query scope for finding "similar" slugs, used to determine uniqueness.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $attribute
     * @param array $config
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindSimilarSlugs(Builder $query, string $attribute, array $config, string $slug): Builder
    {
        $separator = $config['separator'];

        return $query->where(function(Builder $q) use ($attribute, $slug, $separator) {
            $q->where($attribute, '=', $slug)
                ->orWhere($attribute, 'LIKE', $slug . $separator . '%');
        });
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    abstract public function sluggable(): array;


    /**
     * Optionally customize the cocur/slugify engine.
     *
     * @param \Cocur\Slugify\Slugify $engine
     * @param string $attribute
     * @return \Cocur\Slugify\Slugify
     */
    public function customizeSlugEngine(Slugify $engine, string $attribute): Slugify
    {
        return $engine;
    }

    /**
     * Optionally add constraints to the query that determines uniqueness.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $attribute
     * @param array $config
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithUniqueSlugConstraints(
        Builder $query,
        Model $model,
        string $attribute,
        array $config,
        string $slug
    ): Builder
    {
        return $query;
    }
}
