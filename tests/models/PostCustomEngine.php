<?php

use Cocur\Slugify\Slugify;

/**
 * Class PostCustomEngine
 *
 * A test model that loads the Slugify class with custom rules.
 */
class PostCustomEngine extends Post
{
    protected function getSlugEngine()
    {
        $engine = new Slugify();

        $engine->addRule('e', 'a');
        $engine->addRule('i', 'a');
        $engine->addRule('o', 'a');
        $engine->addRule('u', 'a');

        return $engine;
    }
}
