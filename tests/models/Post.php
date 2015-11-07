<?php

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Post
 */
class Post extends Model implements SluggableInterface
{
    use SluggableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

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
    protected $fillable = ['title', 'subtitle'];

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
     * Helper to set slug options for tests.
     *
     * @param array $array Array of new slug options
     */
    public function setSlugConfig($array)
    {
        foreach ($array as $key => $value) {
            $this->sluggable[$key] = $value;
        }
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}
