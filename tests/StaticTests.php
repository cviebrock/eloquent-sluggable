<?php namespace Tests;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Tests\Models\Post;


/**
 * Class SluggableTest
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
}
