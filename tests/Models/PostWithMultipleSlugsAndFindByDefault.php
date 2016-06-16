<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithMultipleSlugs
 */
class PostWithMultipleSlugsAndFindByDefault extends Post
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
            ],
            'dummy' => [
                'source' => 'subtitle',
                'separator' => '.',
            ],
        ];
    }

    public function findBySlugDefault()
    {
        return ['dummy'];
    }
}
