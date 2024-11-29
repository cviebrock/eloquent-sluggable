<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

use Illuminate\Support\Str;

/**
 * Class PostWithCustomCallableMethod.
 */
class PostWithCustomCallableMethod extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'method' => [static::class, 'makeSlug'],
            ],
        ];
    }

    public static function makeSlug($string, $separator)
    {
        return strrev(Str::slug($string, $separator));
    }
}
