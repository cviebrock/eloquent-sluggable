<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Tests\Models\PostWithIncludeTrashed;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithSoftDeleting;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithSoftDeletingIncludeTrashed;

/**
 * Class SoftDeleteTests
 *
 * @package Tests
 */
class SoftDeleteTests extends TestCase
{

    /**
     * Test uniqueness with soft deletes when we ignore trashed models.
     */
    public function testSoftDeletesWithoutTrashed(): void
    {
        $post1 = PostWithSoftDeleting::create([
            'title' => 'A Post Title'
        ]);
        self::assertEquals('a-post-title', $post1->slug);

        $post1->delete();

        $post2 = PostWithSoftDeleting::create([
            'title' => 'A Post Title'
        ]);
        self::assertEquals('a-post-title', $post2->slug);
    }

    /**
     * Test uniqueness with soft deletes when we include trashed models.
     */
    public function testSoftDeletesWithTrashed(): void
    {
        $post1 = PostWithSoftDeletingIncludeTrashed::create([
            'title' => 'A Post Title'
        ]);
        self::assertEquals('a-post-title', $post1->slug);

        $post1->delete();

        $post2 = PostWithSoftDeletingIncludeTrashed::create([
            'title' => 'A Post Title'
        ]);
        self::assertEquals('a-post-title-1', $post2->slug);
    }

    /**
     * Test that include_trashed is ignored if the model doesn't use the softDelete trait.
     */
    public function testSoftDeletesWithNonSoftDeleteModel(): void
    {
        $post1 = PostWithIncludeTrashed::create([
            'title' => 'A Post Title'
        ]);
        self::assertEquals('a-post-title', $post1->slug);
    }
}
