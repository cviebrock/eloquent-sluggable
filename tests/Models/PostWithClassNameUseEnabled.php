<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithClassNameUseEnabled
 *
 * A test model that uses class_name_when_null.
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithClassNameUseEnabled extends Post
{

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title',
                'class_name_when_null' => true
            ]
        ];
    }
}
