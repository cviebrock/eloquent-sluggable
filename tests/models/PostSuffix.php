<?php

use Illuminate\Database\Eloquent\SoftDeletes;

class PostSuffix extends Post {
    protected $sluggable = array(
        'build_from'    => 'title',
        'save_to'       => 'slug',
        'uniqueMethod'  => 'generateUnique',
    );

    /**
     * @param $slug
     * @param $list
     *
     * @return string
     */
    protected function generateUnique($slug, $list)
    {
        $size = count($list);
        return chr($size + 96);
    }
}
