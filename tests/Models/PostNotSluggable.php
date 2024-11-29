<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PostNotSluggable.
 *
 * A test model that doesn't use the Sluggable package.
 *
 * @property int     $id
 * @property string  $title
 * @property ?string $subtitle
 * @property ?string $slug
 * @property ?string $dummy
 * @property ?int    $author_id
 */
class PostNotSluggable extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'posts';

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = ['title', 'subtitle'];

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->title;
    }
}
