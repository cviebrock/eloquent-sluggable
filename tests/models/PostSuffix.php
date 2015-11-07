<?php

/**
 * Class PostSuffix
 *
 * A test model that uses a custom suffix generation method.
 */
class PostSuffix extends Post
{
    /**
     * Sluggable configuration.
     *
     * @var array
     */
    protected $sluggable = [
      'build_from' => 'title',
      'save_to' => 'slug',
    ];

    /**
     * Custom suffix generator.
     *
     * @param string $slug
     * @param array $list
     * @return string
     */
    protected function generateSuffix($slug, $list)
    {
        $size = count($list);

        return chr($size + 96);
    }
}
