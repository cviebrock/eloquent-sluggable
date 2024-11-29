<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithCustomEngineOptions.
 *
 * A test model that customizes the Slugify engine with custom options.
 */
class PostWithCustomEngineOptions extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'            => 'title',
                'slugEngineOptions' => [
                    'lowercase' => false,
                ],
            ],
        ];
    }
}
