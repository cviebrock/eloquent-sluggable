<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

use Cviebrock\EloquentSluggable\PrimarySlug;
use Illuminate\Support\Str;


/**
 * Class PostWithCustomMethod
 */
class PostWithMultipleSlugsAndPrimary extends PostWithMultipleSlugs
{
    use PrimarySlug;

}
