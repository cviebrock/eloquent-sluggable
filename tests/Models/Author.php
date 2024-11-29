<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Author.
 *
 * A test model used for the relationship tests.
 *
 * @property int    $id
 * @property string $name
 */
class Author extends Model
{
    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = ['name'];
}
