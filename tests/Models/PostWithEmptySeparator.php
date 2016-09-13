<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithEmptySeparator
 *
 * A test model that uses an empty separator.
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithEmptySeparator extends Post
{

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title',
                'separator' => '',
            ]
        ];
    }
}
