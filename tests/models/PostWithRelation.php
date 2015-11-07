<?php

/**
 * Class PostWithRelation
 *
 * A test model used for the relationship tests.
 */
class PostWithRelation extends Post
{
    /**
     * Sluggable configuration.
     *
     * @var array
     */
    protected $sluggable = [
      'build_from' => ['author.name', 'title'],
      'save_to' => 'slug',
    ];

    /**
     * Relation to Author model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo('Author');
    }
}
