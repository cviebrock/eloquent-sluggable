<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithCustomSeparator.
 *
 * A test model that uses a custom suffix generation method.
 */
class PostWithCustomSeparator extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'    => 'title',
                'separator' => '.',
            ],
        ];
    }
}
