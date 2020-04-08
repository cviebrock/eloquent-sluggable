<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithCallableSource
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithCallableSource extends Post
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
                'source' => function (Post $post) {
                    return "{$post->title}-test-{$post->subtitle}";
                }
            ]
        ];
    }

}
