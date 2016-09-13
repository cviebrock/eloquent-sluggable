<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

/**
 * Class PostShortConfigWithScopeHelpers
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostShortConfigWithScopeHelpers extends Post
{

    use SluggableScopeHelpers;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug_field'
        ];
    }
}
