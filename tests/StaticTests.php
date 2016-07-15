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
     *
     * @test
     */
    public function testStaticSlugGenerator()
    {
        $slug = SlugService::createSlug(Post::class, 'slug', 'My Test Post');
        $this->assertEquals('my-test-post', $slug);
    }

    /**
     * Test that we generate unique slugs in a static context.
     *
     * @test
     */
    public function testStaticSlugGeneratorWhenEntriesExist()
    {
        $post = Post::create(['title' => 'My Test Post']);
        $this->assertEquals('my-test-post', $post->slug);

        $slug = SlugService::createSlug(Post::class, 'slug', 'My Test Post');
        $this->assertEquals('my-test-post-1', $slug);
    }

    /**
     * Test that we can generate a slug statically with different configuration.
     *
     * @test
     */
    public function testStaticSlugGeneratorWithConfig()
    {
        $config = [
            'separator' => '.'
        ];
        $slug = SlugService::createSlug(Post::class, 'slug', 'My Test Post', $config);
        $this->assertEquals('my.test.post', $slug);
    }
}
