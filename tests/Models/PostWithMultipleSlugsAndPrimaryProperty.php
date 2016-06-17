<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

use Cviebrock\EloquentSluggable\PrimarySlug;
use Illuminate\Support\Str;


/**
 * Class PostWithCustomMethod
 */
class PostWithMultipleSlugsAndPrimaryProperty extends PostWithMultipleSlugs
{
    use PrimarySlug;

    protected $slugKeyName = 'dummy';
}
