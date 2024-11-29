<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithEmptySeparator.
 *
 * A test model that uses an empty separator.
 */
class PostWithEmptySeparator extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'    => 'title',
                'separator' => '',
            ],
        ];
    }
}
