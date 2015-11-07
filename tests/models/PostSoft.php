<?php

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PostSoft
 *
 * A test model that uses the Sluggable package and uses Laravel's SoftDeleting trait.
 */
class PostSoft extends Post
{
    use SoftDeletes;
}
