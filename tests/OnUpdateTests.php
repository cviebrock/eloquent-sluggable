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

    /**
     * Test that the slug is not regenerated if onUpdate is true
     * but the source fields didn't change.
     */
    public function testSlugDoesNotChangeIfSourceDoesNotChange()
    {
        $post = PostWithOnUpdate::create([
            'title' => 'My First Post'
        ]);
        $post->save();
        $this->assertEquals('my-first-post', $post->slug);

        $post->update([
            'subtitle' => 'A Subtitle'
        ]);
        $this->assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test that the slug is not regenerated if onUpdate is true
     * but the source fields didn't change, even with multiple
     * increments of the same slug.
     *
     * @see https://github.com/cviebrock/eloquent-sluggable/issues/317
     */
    public function testSlugDoesNotChangeIfSourceDoesNotChangeMultiple()
    {
        $data = [
            'title' => 'My First Post'
        ];
        $post0 = PostWithOnUpdate::create($data);
        $post1 = PostWithOnUpdate::create($data);
        $post2 = PostWithOnUpdate::create($data);
        $post3 = PostWithOnUpdate::create($data);
        $this->assertEquals('my-first-post-3', $post3->slug);

        $post3->update([
            'subtitle' => 'A Subtitle'
        ]);
        $this->assertEquals('my-first-post-3', $post3->slug);
    }
}
