<?php namespace Tests\Models;

/**
 * Class Post
 */
class PostWithMaxLength extends Post
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
                'maxLength' => 10,
            ]
        ];
    }
}
