<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithOnUpdate.
 *
 * A test model that uses the onUpdate functionality.
 */
class PostWithOnUpdate extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'   => 'title',
                'onUpdate' => true,
            ],
        ];
    }
}
