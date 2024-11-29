<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithNoSource.
 *
 * A test model with no source field defined.
 */
class PostWithNoSource extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => null,
            ],
        ];
    }
}
