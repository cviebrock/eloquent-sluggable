<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithMaxLength.
 */
class PostWithMaxLengthSplitWords extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'             => 'title',
                'maxLength'          => 10,
                'maxLengthKeepWords' => false,
            ],
        ];
    }
}
