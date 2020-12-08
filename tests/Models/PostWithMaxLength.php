<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithMaxLength
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithMaxLength extends Post
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
                'source' => 'title',
                'maxLength' => 10,
            ]
        ];
    }
}
