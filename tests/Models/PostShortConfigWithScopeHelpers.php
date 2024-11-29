<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

/**
 * Class PostShortConfigWithScopeHelpers.
 */
class PostShortConfigWithScopeHelpers extends Post
{
    use SluggableScopeHelpers;

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug_field',
        ];
    }
}
