<?php

use Illuminate\Database\Eloquent\SoftDeletes;

class PostSuffix extends Post {
    protected $sluggable = array(
        'build_from'    => 'title',
        'save_to'       => 'slug',
    );

    /**
     * @param string $slug
     * @param array  $list
     *
     * @return string
     */
    protected function generateSuffix($slug, $list)
    {
        $size = count($list);
        return chr($size + 96);
    }
}
