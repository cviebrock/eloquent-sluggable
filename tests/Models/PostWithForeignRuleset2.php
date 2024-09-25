<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithForeignRuleset
 *
 * A test model that customizes the Slugify engine with a foreign ruleset.
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithForeignRuleset2 extends Post
{

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'slugEngineOptions' => [
                    'ruleset' => 'finnish'
                ],
            ],
        ];
    }
}
