<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithIncludeTrashed
 *
 * A test model that uses the Sluggable package and "includeTrashed",
 * but does not use Laravel's SoftDeleting trait.
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithIncludeTrashed extends Post
{

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'includeTrashed' => true
            ]
        ];
    }
}
