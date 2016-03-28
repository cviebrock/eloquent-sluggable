<?php namespace Tests\Models;

/**
 * Class PostSuffix
 *
 * A test model that uses a custom suffix generation method.
 */
class PostWithCustomSeparator extends Post
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
                'separator' => '.'
            ]
        ];
    }
}
