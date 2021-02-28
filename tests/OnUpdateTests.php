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
    public function testSlugDoesntChangeWithoutOnUpdate(): void
    {
        $post = Post::create([
            'title' => 'My First Post'
        ]);
        $post->save();
        self::assertEquals('my-first-post', $post->slug);

        $post->update([
            'title' => 'A New Title'
        ]);
        self::assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test that the slug is regenerated if the field is emptied manually.
     */
    public function testSlugDoesChangeWhenEmptiedManually(): void
    {
        $post = Post::create([
            'title' => 'My First Post'
        ]);
        $post->save();
        self::assertEquals('my-first-post', $post->slug);

        $post->slug = null;
        $post->update([
            'title' => 'A New Title'
        ]);
        self::assertEquals('a-new-title', $post->slug);
    }

    /**
     * Test that the slug is regenerated if onUpdate is true.
     */
    public function testSlugDoesChangeWithOnUpdate(): void
    {
        $post = PostWithOnUpdate::create([
            'title' => 'My First Post'
        ]);
        $post->save();
        self::assertEquals('my-first-post', $post->slug);

        $post->update([
            'title' => 'A New Title'
        ]);
        self::assertEquals('a-new-title', $post->slug);
    }

    /**
     * Test that the slug is not regenerated if onUpdate is true
     * but the source fields didn't change.
     */
    public function testSlugDoesNotChangeIfSourceDoesNotChange(): void
    {
        $post = PostWithOnUpdate::create([
            'title' => 'My First Post'
        ]);
        $post->save();
        self::assertEquals('my-first-post', $post->slug);

        $post->update([
            'subtitle' => 'A Subtitle'
        ]);
        self::assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test that the slug is not regenerated if onUpdate is true
     * but the source fields didn't change, even with multiple
     * increments of the same slug.
     *
     * @see https://github.com/cviebrock/eloquent-sluggable/issues/317
     */
    public function testSlugDoesNotChangeIfSourceDoesNotChangeMultiple(): void
    {
        $data = [
            'title' => 'My First Post'
        ];
        $post1 = PostWithOnUpdate::create($data);
        $post2 = PostWithOnUpdate::create($data);
        $post3 = PostWithOnUpdate::create($data);
        $post4 = PostWithOnUpdate::create($data);
        self::assertEquals('my-first-post-4', $post4->slug);

        $post4->update([
            'subtitle' => 'A Subtitle'
        ]);
        self::assertEquals('my-first-post-4', $post4->slug);
    }

    /**
     * Test that the slug isn't set to null if the source fields
     * not loaded in model.
     */
    public function testSlugDoesNotChangeIfSourceNotProvidedInModel(): void
    {
        $post = Post::create([
            'title' => 'My First Post'
        ]);
        self::assertEquals('my-first-post', $post->slug);

        $post = Post::whereKey($post->id)->get(['id','subtitle'])->first();
        $post->update([
            'subtitle' => 'A Subtitle'
        ]);

        $post = Post::findOrFail($post->id);
        self::assertEquals('my-first-post', $post->slug);
    }
}
