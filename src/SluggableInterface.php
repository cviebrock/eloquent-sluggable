<?php namespace Cviebrock\EloquentSluggable;

/**
 * Interface SluggableInterface
 *
 * @package Cviebrock\EloquentSluggable
 */
interface SluggableInterface
{
    public function getSlug();

    public function sluggify($force = false);

    public function resluggify();
}
