<?php namespace Tests;

use Cviebrock\EloquentSluggable\Events\Slugged;
use Cviebrock\EloquentSluggable\Events\Slugging;
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
        $this->app['events']->listen(Slugging::class, function($event) {
            return false;
        });

        $this->expectsEvents([
            Slugging::class,
        ]);

        $this->doesntExpectEvents([
            Slugged::class,
        ]);

        $post = Post::create([
            'title' => 'My Test Post'
        ]);
        dd($post);
        $this->assertEquals(null, $post->slug);
    }

    /**
     * Test that the "slugged" event is fired.
     *
     * @test
     */
    public function testSluggedEvent()
    {
        //        Post::registerModelEvent('slugged', function ($post) {
        //            $post->subtitle = 'I have been slugged!';
        //        });

        $post = Post::create([
            'title' => 'My Test Post'
        ]);
        $this->assertEquals('my-test-post', $post->slug);
        $this->assertEquals('I have been slugged!', $post->subtitle);
    }
}
