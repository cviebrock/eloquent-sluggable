<?php

use Illuminate\Database\Eloquent\Model;

/**
 * Class Author
 *
 * A test model used for the relationship tests.
 */
class Author extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
