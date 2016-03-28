<?php namespace Tests\Models;

use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class PostWithSoftDeleting
 *
 * A test model that uses the Sluggable package and "includeTrashed",
 * but doesn't use Laravel's SoftDeleting trait.
 */
class PostWithIncludeTrashed extends Post
{

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title',
                'includeTrashed' => true
            ]
        ];
    }
}
