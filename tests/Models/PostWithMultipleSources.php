<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithMultipleSources.
 */
class PostWithMultipleSources extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['title', 'subtitle'],
            ],
        ];
    }
}
