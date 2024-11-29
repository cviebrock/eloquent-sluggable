<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Author
 *
 * A test model used for the relationship tests.
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 *
 * @property integer $id
 * @property string $name
 */
class Author extends Model
{

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $fillable = ['name'];
}
