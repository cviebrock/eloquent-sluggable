<?php namespace Cviebrock\EloquentSluggable\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PostWithUniqueSlugConstraints
 *
 * @package Cviebrock\EloquentSluggable\Tests\Models
 *
 * @property \Cviebrock\EloquentSluggable\Tests\Models\Author|null $author
 */
class PostWithUniqueSlugConstraints extends Post
{

    /**
     * Relation to Author model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * @inheritDoc
     */
    public function scopeWithUniqueSlugConstraints(Builder $query, Model $model, $attribute, $config, $slug): Builder
    {
        /** @var self $model */
        $author = $model->author;

        return $query->where('author_id', $author->getKey());
    }

}
