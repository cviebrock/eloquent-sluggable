<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

use Cocur\Slugify\Slugify;


/**
 * Class PostCustomEngine
 *
 * A test model that customizes the Slugify engine with custom rules.
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithCustomEngine extends Post
{

    /**
     * @param \Cocur\Slugify\Slugify $engine
     * @param string $attribute
     * @return \Cocur\Slugify\Slugify
     */
    public function customizeSlugEngine(Slugify $engine, string $attribute): Slugify
    {
        $engine->addRule('e', 'a');
        $engine->addRule('i', 'a');
        $engine->addRule('o', 'a');
        $engine->addRule('u', 'a');

        return $engine;
    }
}
