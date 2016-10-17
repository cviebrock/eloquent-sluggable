<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Contracts\SlugContract;
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
    public function testStaticSlugGenerator()
    {
        $slug = app(SlugContract::class)->createSlug(Post::class, 'slug', 'My Test Post');
        $this->assertEquals('my-test-post', $slug);
    }

    /**
     * Test that we generate unique slugs in a static context.
     */
    public function testStaticSlugGeneratorWhenEntriesExist()
    {
        $post = Post::create(['title' => 'My Test Post']);
        $this->assertEquals('my-test-post', $post->slug);

        $slug = app(SlugContract::class)->createSlug(Post::class, 'slug', 'My Test Post');
        $this->assertEquals('my-test-post-1', $slug);
    }

    /**
     * Test that we can generate a slug statically with different configuration.
     */
    public function testStaticSlugGeneratorWithConfig()
    {
        $config = [
            'separator' => '.'
        ];
        $slug = app(SlugContract::class)->createSlug(Post::class, 'slug', 'My Test Post', $config);
        $this->assertEquals('my.test.post', $slug);
    }
}
