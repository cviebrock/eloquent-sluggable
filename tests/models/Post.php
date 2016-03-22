<?php namespace Tests\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;


/**
 * Class Post
 */
class Post extends Model
{

    use Sluggable;

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

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
          'slug'
        ];
    }
}
