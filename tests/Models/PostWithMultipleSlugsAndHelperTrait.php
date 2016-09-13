<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

/**
 * Class PostWithMultipleSlugsAndPrimary
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithMultipleSlugsAndHelperTrait extends PostWithMultipleSlugs
{

    use SluggableScopeHelpers;

}
