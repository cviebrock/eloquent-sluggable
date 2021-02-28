<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Cviebrock\EloquentSluggable\Tests\Models\Post;

/**
 * Class StaticTests
 *
 * @package Tests
 */
class StaticTests extends TestCase
{

    /**
     * Test that we can generate a slug statically.
     */
    public function testStaticSlugGenerator(): void
    {
        $slug = SlugService::createSlug(Post::class, 'slug', 'My Test Post');
        self::assertEquals('my-test-post', $slug);
    }

    /**
     * Test that we generate unique slugs in a static context.
     */
    public function testStaticSlugGeneratorWhenEntriesExist(): void
    {
        $post = Post::create(['title' => 'My Test Post']);
        self::assertEquals('my-test-post', $post->slug);

        $slug = SlugService::createSlug(Post::class, 'slug', 'My Test Post');
        self::assertEquals('my-test-post-2', $slug);
    }

    /**
     * Test that we can generate a slug statically with different configuration.
     */
    public function testStaticSlugGeneratorWithConfig(): void
    {
        $config = [
            'separator' => '.'
        ];
        $slug = SlugService::createSlug(Post::class, 'slug', 'My Test Post', $config);
        self::assertEquals('my.test.post', $slug);
    }

    /**
     * Test passing an invalid attribute to static method
     */
    public function testStaticSlugWithInvalidAttribute(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        SlugService::createSlug(Post::class, 'foo', 'My Test Post');
    }
}
