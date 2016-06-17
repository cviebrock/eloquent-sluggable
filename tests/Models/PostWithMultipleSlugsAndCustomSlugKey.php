<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithMultipleSlugsAndCustomSlugKey
 */
class PostWithMultipleSlugsAndCustomSlugKey extends PostWithMultipleSlugsAndHelperTrait
{

    protected $slugKeyName = 'dummy';
    
}
