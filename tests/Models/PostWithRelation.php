<?php

namespace Cviebrock\EloquentSluggable\Tests\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PostWithRelation.
 *
 * A test model used for the relationship tests.
 *
 * @property Author|null $author
 */
class PostWithRelation extends Post
{
    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['author.name', 'title'],
            ],
        ];
    }

    /**
     * Relation to Author model.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
