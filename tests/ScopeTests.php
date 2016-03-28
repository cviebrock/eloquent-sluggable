<?php namespace Tests;

use Tests\Models\Post;
use Tests\Models\PostWithCustomMethod;
use Tests\Models\PostWithCustomSeparator;
use Tests\Models\PostWithCustomSuffix;
use Tests\Models\PostWithMultipleSources;
use Tests\Models\PostWithNoSource;
use Tests\Models\PostWithOnUpdate;
use Tests\Models\PostWithReservedSlug;
use Tests\Models\PostWithSoftDeleting;
use Tests\Models\PostWithSoftDeletingIncludeTrashed;


/**
 * Class ScopeTests
 */
class ScopeTests extends TestCase
{

    /**
     * Test findBySlug() scope method
     *
     * @test
     */
    public function testFindBySlug()
    {
        $post1 = $this->makePost('My first post');
        $post1->save();

        $post2 = $this->makePost('My second post');
        $post2->save();

        $post3 = $this->makePost('My third post');
        $post3->save();

        $post = Post::findBySlug('my-second-post');

        $this->assertEquals($post2->id, $post->id);
    }

    /**
     * Test findBySlugOrFail() scope method
     *
     * @test
     */
    public function testFindBySlugOrFail()
    {
        $post1 = $this->makePost('My first post');
        $post1->save();

        $post2 = $this->makePost('My second post');
        $post2->save();

        $post3 = $this->makePost('My third post');
        $post3->save();

        $post = Post::findBySlugOrFail('my-second-post');
        $this->assertEquals($post2->id, $post->id);

        try {
            Post::findBySlugOrFail('my-fourth-post');
            $this->fail('Not found exception not raised');
        } catch (Exception $e) {
            $this->assertInstanceOf('Illuminate\Database\Eloquent\ModelNotFoundException',
                $e);
        }
    }

    /**
     * Test findBySlug() scope method
     *
     * @test
     */
    public function testFindBySlugOrId()
    {
        $post1 = $this->makePost('My first post');
        $post1->save();

        $post2 = $this->makePost('My second post');
        $post2->save();

        $post3 = $this->makePost('My third post');
        $post3->save();

        $post4 = $this->makePost(5);
        $post4->save();

        $post = Post::findBySlugOrId('my-second-post');

        $this->assertEquals($post2->id, $post->id);

        $post = Post::findBySlugOrId(3);

        $this->assertEquals($post3->id, $post->id);

        $post = Post::findBySlugOrId(5);

        $this->assertEquals($post4->id, $post->id);
    }

    /**
     * Test findBySlugOrFail() scope method
     *
     * @test
     */
    public function testFindBySlugOrIdOrFail()
    {
        $post1 = $this->makePost('My first post');
        $post1->save();

        $post2 = $this->makePost('My second post');
        $post2->save();

        $post3 = $this->makePost('My third post');
        $post3->save();

        $post4 = $this->makePost(5);
        $post4->save();

        $post = Post::findBySlugOrIdOrFail('my-second-post');
        $this->assertEquals($post2->id, $post->id);

        $post = Post::findBySlugOrIdOrFail(3);
        $this->assertEquals($post3->id, $post->id);

        $post = Post::findBySlugOrIdOrFail(5);
        $this->assertEquals($post4->id, $post->id);

        try {
            Post::findBySlugOrFail('my-fourth-post');
            $this->fail('Not found exception not raised');
        } catch (Exception $e) {
            $this->assertInstanceOf('Illuminate\Database\Eloquent\ModelNotFoundException',
                $e);
        }
    }

    /**
     * Test findBySlug returns null when no record found
     *
     * @test
     */
    public function testFindBySlugReturnsNullForNoRecord()
    {
        $this->assertNull(Post::findBySlug('not a real record'));
    }

    /**
     * Test Non static call for findBySlug is working
     *
     * @test
     */
    public function testNonStaticCallOfFindBySlug()
    {
        $post1 = $this->makePost('My first post');
        $post1->save();

        $post = Post::first();
        $resultId = $post->findBySlug('my-first-post')->id;

        $this->assertEquals($post1->id, $resultId);
    }

}
