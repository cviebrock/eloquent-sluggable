<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PostWithSoftDeleting
 *
 * A test model that uses the Sluggable package and uses Laravel's SoftDeleting trait.
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithSoftDeleting extends Post
{

    use SoftDeletes;
}
