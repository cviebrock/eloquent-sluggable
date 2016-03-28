<?php namespace Tests\Models;

use Cocur\Slugify\Slugify;


/**
 * Class PostCustomEngine
 *
 * A test model that customizes the Slugify engine with custom rules.
 */
class PostWithCustomEngine extends Post
{

    public function customizeSlugEngine(Slugify $engine)
    {
        $engine->addRule('e', 'a');
        $engine->addRule('i', 'a');
        $engine->addRule('o', 'a');
        $engine->addRule('u', 'a');

        return $engine;
    }
}