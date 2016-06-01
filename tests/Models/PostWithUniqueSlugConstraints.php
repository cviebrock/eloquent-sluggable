<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PostWithUniqueSlugConstraints
 */
class PostWithUniqueSlugConstraints extends Post
{

    /**
     * Relation to Author model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $attribute
     * @param array $config
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithUniqueSlugConstraints(Builder $query, Model $model, $attribute, $config, $slug)
    {
        $author = $model->author;

        return $query->where('author_id', $author->getKey());
    }

}
