<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithFirstUniqueSuffix.
 */
class PostWithFirstUniqueSuffix extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'            => 'title',
                'firstUniqueSuffix' => '42',
            ],
        ];
    }
}
