<?php namespace Tests\Models;

use Illuminate\Support\Collection;


/**
 * Class PostSuffix
 *
 * A test model that uses a custom suffix generation method.
 */
class PostWithCustomSource extends Post
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
              'source' => 'subtitle'
          ]
        ];
    }
}
