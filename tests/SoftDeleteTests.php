<?php namespace Tests;

use Tests\Models\PostWithIncludeTrashed;
use Tests\Models\PostWithSoftDeleting;
use Tests\Models\PostWithSoftDeletingIncludeTrashed;


/**
 * Class SoftDeleteTests
 *
 * @package Tests
 */
class SoftDeleteTests extends TestCase
{

    /**
     * Test uniqueness with soft deletes when we ignore trashed models.
     *
     * @test
     */
    public function testSoftDeletesWithoutTrashed()
    {
        $post1 = PostWithSoftDeleting::create([
            'title' => 'A Post Title'
        ]);
        $this->assertEquals('a-post-title', $post1->slug);

        $post1->delete();

        $post2 = PostWithSoftDeleting::create([
            'title' => 'A Post Title'
        ]);
        $this->assertEquals('a-post-title', $post2->slug);
    }

    /**
     * Test uniqueness with soft deletes when we include trashed models.
     *
     * @test
     */
    public function testSoftDeletesWithTrashed()
    {
        $post1 = PostWithSoftDeletingIncludeTrashed::create([
            'title' => 'A Post Title'
        ]);
        $this->assertEquals('a-post-title', $post1->slug);

        $post1->delete();

        $post2 = PostWithSoftDeletingIncludeTrashed::create([
            'title' => 'A Post Title'
        ]);
        $this->assertEquals('a-post-title-1', $post2->slug);
    }

    /**
     * Test that include_trashed is ignored if the model doesn't use the softDelete trait.
     *
     * @test
     */
    public function testSoftDeletesWithNonSoftDeleteModel()
    {
        $post1 = PostWithIncludeTrashed::create([
            'title' => 'A Post Title'
        ]);
        $this->assertEquals('a-post-title', $post1->slug);
    }
}
