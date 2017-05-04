<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

use Cviebrock\EloquentSluggable\Engines\IdeographicEngine;


/**
 * Class PostWithIdeographicEngine
 *
 * A test model that customizes the Slugify engine with custom rules.
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithIdeographicEngine extends Post
{

    /**
     * @return IdeographicEngine
     */
    public function customizeSlugEngine()
    {
        return new IdeographicEngine();
    }
}
