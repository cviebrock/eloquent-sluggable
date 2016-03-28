<?php namespace Tests;

use Cviebrock\EloquentSluggable\Events\Slugged;
use Cviebrock\EloquentSluggable\Events\Slugging;
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
        $this->expectsEvents([
            Slugging::class,
            Slugged::class
        ]);

        Post::create([
            'title' => 'My Test Post'
        ]);
    }

    /**
     * Test that the "slugging" event can be cancelled.
     *
     * @test
     */
    public function testCancelSluggingEvent()
    {
        $this->app['events']->listen(Slugging::class, AbortSlugging::class);

        $this->expectsEvents([
            Slugging::class,
        ]);

        $this->doesntExpectEvents([
            Slugged::class,
        ]);

        $post = Post::create([
            'title' => 'My Test Post'
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
