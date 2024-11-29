<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithReservedSlug.
 *
 * A test model that uses custom reserved slug names.
 */
class PostWithReservedSlug extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'   => 'title',
                'reserved' => ['add', 'add-1'],
            ],
        ];
    }
}
