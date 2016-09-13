<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithMultipleSlugsAndCustomSlugKey
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithMultipleSlugsAndCustomSlugKey extends PostWithMultipleSlugsAndHelperTrait
{

    protected $slugKeyName = 'dummy';
    
}
