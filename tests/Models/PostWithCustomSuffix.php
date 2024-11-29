<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

use Illuminate\Support\Collection;

/**
 * Class PostWithCustomSuffix.
 *
 * A test model that uses a custom suffix generation method.
 */
class PostWithCustomSuffix extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'       => 'title',
                'uniqueSuffix' => function ($slug, $separator, Collection $list) {
                    $size = count($list);

                    return chr($size + 96);
                },
            ],
        ];
    }
}
