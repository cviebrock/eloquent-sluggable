<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostShortConfig.
 */
class PostShortConfig extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug',
        ];
    }
}
