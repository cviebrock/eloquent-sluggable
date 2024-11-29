<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithIdSource.
 *
 * A test model that uses the model's ID in the slug source.
 */
class PostWithIdSource extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'   => ['title', 'id'],
                'onUpdate' => true,
            ],
        ];
    }
}
