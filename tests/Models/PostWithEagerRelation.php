<?php namespace Cviebrock\EloquentSluggable\Tests\Models;


/**
 * Class PostWithEagerRelations
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 */
class PostWithEagerRelation extends PostWithRelation
{

    protected $with = ['author'];

}
