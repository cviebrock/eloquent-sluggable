<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithMultipleSlugs.
 */
class PostWithMultipleSlugs extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
            'dummy' => [
                'source'    => 'subtitle',
                'separator' => '.',
            ],
        ];
    }
}
