<?php namespace Tests\Models;

/**
 * Class Post
 */
class PostWithMultipleSources extends Post
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
            'source' => ['title', 'subtitle'],
          ]
        ];
    }
}
