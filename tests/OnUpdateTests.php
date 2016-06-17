<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Tests\Models\Post;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithOnUpdate;

/**
 * Class OnUpdateTests
 *
 * @package Tests
 */
class OnUpdateTests extends TestCase
{

    /**
     * Test that the slug isn't regenerated if onUpdate is false.
     *
     * @test
     */
    public function testSlugDoesntChangeWithoutOnUpdate()
    {
        $post = Post::create([
            'title' => 'My First Post'
        ]);
        $post->save();
        $this->assertEquals('my-first-post', $post->slug);

        $post->update([
            'title' => 'A New Title'
        ]);
        $this->assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test that the slug is regenerated if the field is emptied manually.
     *
     * @test
     */
    public function testSlugDoesChangeWhenEmptiedManually()
    {
        $post = Post::create([
            'title' => 'My First Post'
        ]);
        $post->save();
        $this->assertEquals('my-first-post', $post->slug);

        $post->slug = null;
        $post->update([
            'title' => 'A New Title'
        ]);
        $this->assertEquals('a-new-title', $post->slug);
    }

    /**
     * Test that the slug is regenerated if onUpdate is true.
     *
     * @test
     */
    public function testSlugDoesChangeWithOnUpdate()
    {
        $post = PostWithOnUpdate::create([
            'title' => 'My First Post'
        ]);
        $post->save();
        $this->assertEquals('my-first-post', $post->slug);

        $post->update([
            'title' => 'A New Title'
        ]);
        $this->assertEquals('a-new-title', $post->slug);
    }
}
