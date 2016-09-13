<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithCustomSeparator
 *
 * A test model that uses a custom suffix generation method.
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithCustomSeparator extends Post
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
                'separator' => '.'
            ]
        ];
    }
}
