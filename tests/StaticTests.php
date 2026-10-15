<?php

namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Cviebrock\EloquentSluggable\Tests\Models\Post;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomSeparator;

/**
 * Class StaticTests.
 *
 * @internal
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
            'separator' => '.',
        ];
        $slug = SlugService::createSlug(Post::class, 'slug', 'My Test Post', $config);
        self::assertEquals('my.test.post', $slug);
    }

    public function testStaticOverridesPreserveModelConfiguration(): void
    {
        $post = PostWithCustomSeparator::create(['title' => 'My Test Post']);
        self::assertEquals('my.test.post', $post->fresh()->slug);

        $slug = SlugService::createSlug(PostWithCustomSeparator::class, 'slug', 'My Test Post', ['unique' => false]);
        self::assertEquals('my.test.post', $slug);
    }

    public function testEmptyStaticOverridesPreserveModelConfiguration(): void
    {
        PostWithCustomSeparator::create(['title' => 'My Test Post']);

        $slug = SlugService::createSlug(PostWithCustomSeparator::class, 'slug', 'My Test Post', []);
        self::assertEquals('my.test.post.2', $slug);
    }

    public function testStaticOverridesTakePrecedenceOverModelConfiguration(): void
    {
        $slug = SlugService::createSlug(PostWithCustomSeparator::class, 'slug', 'My Test Post', ['separator' => '_']);
        self::assertEquals('my_test_post', $slug);
    }

    public function testStaticExplicitConfigurationForAnotherAttribute(): void
    {
        $slug = SlugService::createSlug(Post::class, 'dummy', 'My Test Post', ['separator' => '_']);
        self::assertEquals('my_test_post', $slug);
    }

    /**
     * Test passing an invalid attribute to static method.
     */
    public function testStaticSlugWithInvalidAttribute(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        SlugService::createSlug(Post::class, 'foo', 'My Test Post');
    }
}
