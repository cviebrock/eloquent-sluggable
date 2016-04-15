<?php namespace Tests;

use Tests\Listeners\AbortSlugging;
use Tests\Models\Post;


/**
 * Class SluggableTest
 */
class EventTests extends TestCase
{

    /**
     * Test that the "slugging" event is fired.
     *
     * @test
     */
    public function testEventsAreFired()
    {
        Post::create([
            'title' => 'My Test Post'
        ]);

        $this->expectsEvents([
            'eloquent.slugging: ' . Post::class,
            'eloquent.slugged: ' . Post::class,
        ]);
    }

    /**
     * Test that the "slugging" event can be cancelled.
     *
     * @test
     */
    public function testCancelSluggingEvent()
    {
        $this->app['events']->listen('eloquent.slugging: ' . Post::class, AbortSlugging::class);

        $post = Post::create([
            'title' => 'My Test Post'
        ]);

        $this->expectsEvents([
            'eloquent.slugging: ' . Post::class,
        ]);

        $this->doesntExpectEvents([
            'eloquent.slugged: ' . Post::class,
        ]);

        $this->assertEquals(null, $post->slug);
    }

    /**
     * Test that the "slugged" event is fired.
     *
     * @test
     */
    public function testSluggedEvent()
    {
        $post = Post::create([
            'title' => 'My Test Post'
        ]);

        $this->assertEquals('my-test-post', $post->slug);
        $this->assertEquals('I have been slugged!', $post->subtitle);
    }
}
