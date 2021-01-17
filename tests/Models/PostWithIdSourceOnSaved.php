<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

use Cviebrock\EloquentSluggable\SluggableObserver;

/**
 * Class PostWithIdSourceOnSaved
 *
 * A test model that uses the model's ID in the slug source
 * and the SluggableObserver::SAVED event listener.
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithIdSourceOnSaved extends Post
{

    /**
     * @inheritDoc
     */
    public function sluggableEvent(): string
    {
        return SluggableObserver::SAVED;
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['title','id'],
                'onUpdate' => true,
            ],
        ];
    }
}
