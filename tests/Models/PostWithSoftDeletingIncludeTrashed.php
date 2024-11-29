<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PostWithSoftDeletingIncludeTrashed.
 *
 * A test model that uses the Sluggable package and uses Laravel's SoftDeleting trait
 * but includes trashed models.
 */
class PostWithSoftDeletingIncludeTrashed extends Post
{
    use SoftDeletes;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'         => 'title',
                'includeTrashed' => true,
            ],
        ];
    }
}
