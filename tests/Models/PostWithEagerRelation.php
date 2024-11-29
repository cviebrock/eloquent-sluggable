<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

/**
 * Class PostWithEagerRelations.
 */
class PostWithEagerRelation extends PostWithRelation
{
    protected $with = ['author'];
}
