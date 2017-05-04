<?php namespace Cviebrock\EloquentSluggable\Engines;

use Cocur\Slugify\SlugifyInterface;

/**
 * Class IdeographicEngine
 *
 * @package Cviebrock\EloquentSluggable\Engines
 */
class IdeographicEngine implements SlugifyInterface
{


    /**
     * For ideographic Engine, Return a unique Random string instead of Slug
     *
     * @param string $string
     * @param string|array|null $options
     *
     * @return string
     *
     * @api
     */
    public function slugify($string, $options = null)
    {
        $fullString = md5(uniqid(rand(), true));
        return substr($fullString, 0, 12);
    }
}
