<?php namespace Tests;

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
        $this->assertEquals('my-test-post', Post::createSlug('My Test Post'));
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
        $this->assertEquals('my-test-post-1', Post::createSlug('My Test Post'));
    }
}
