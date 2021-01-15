<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

use Cocur\Slugify\Slugify;


/**
 * Class PostCustomEngine2
 *
 * A test model that customizes the Slugify engine with other custom rules.
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithCustomEngine2 extends Post
{

    /**
     * @param \Cocur\Slugify\Slugify $engine
     * @param string $attribute
     * @return \Cocur\Slugify\Slugify
     */
    public function customizeSlugEngine(Slugify $engine, string $attribute): Slugify
    {
        return new Slugify(['regexp'=>'|[^A-Za-z0-9/]+|']);
    }
}
