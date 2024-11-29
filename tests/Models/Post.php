<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Post.
 *
 * @property int     $id
 * @property string  $title
 * @property ?string $subtitle
 * @property ?string $slug
 * @property ?string $dummy
 * @property ?int    $author_id
 */
class Post extends Model
{
    use Sluggable;

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
    protected $fillable = ['title', 'subtitle', 'slug', 'dummy', 'author_id'];

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }
}
