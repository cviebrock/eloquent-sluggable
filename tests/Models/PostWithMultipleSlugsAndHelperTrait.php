<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

/**
 * Class PostWithMultipleSlugsAndPrimary
 */
class PostWithMultipleSlugsAndHelperTrait extends PostWithMultipleSlugs
{

    use SluggableScopeHelpers;

}
