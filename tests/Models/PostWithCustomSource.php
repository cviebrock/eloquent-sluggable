<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithCustomSource
 *
 * A test model that uses a custom suffix generation method.
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithCustomSource extends Post
{

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'subtitle'
            ]
        ];
    }
}
