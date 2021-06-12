<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Tests\Listeners\AbortSlugging;
use Cviebrock\EloquentSluggable\Tests\Listeners\DoNotAbortSlugging;
use Cviebrock\EloquentSluggable\Tests\Models\Post;
use Illuminate\Support\Facades\Event;

/**
 * Class EventTests
 *
 * @package Tests
 */
class EventTests extends TestCase
{

    /**
     * Test that the "slugging" event is fired.
     */
    public function testEventsAreFired(): void
    {
        Event::fake([
            'eloquent.slugging: ' . Post::class,
            'eloquent.slugged: ' . Post::class,
        ]);

        Post::create([
            'title' => 'My Test Post'
        ]);

        Event::assertDispatched('eloquent.slugging: ' . Post::class);
        Event::assertDispatched('eloquent.slugged: ' . Post::class);
    }

    /**
     * Test that the "slugging" event can be cancelled.
     */
    public function testDoNotCancelSluggingEventWhenItReturnsAnythingOtherThanFalse(): void
    {
        Event::fake([
            'eloquent.slugged: ' . Post::class,
        ]);

        $this->app['events']->listen('eloquent.slugging: ' . Post::class, DoNotAbortSlugging::class);

        $post = Post::create([
            'title' => 'My Test Post'
        ]);

        self::assertEquals('my-test-post', $post->slug);
        Event::assertDispatched('eloquent.slugged: ' . Post::class);
    }

    public function testCancelSluggingEvent(): void
    {
        Event::fake([
            'eloquent.slugged: ' . Post::class,
        ]);

        $this->app['events']->listen('eloquent.slugging: ' . Post::class, AbortSlugging::class);

        $post = Post::create([
            'title' => 'My Test Post'
        ]);

        self::assertEquals(null, $post->slug);
        Event::assertNotDispatched('eloquent.slugged: ' . Post::class);
    }
}
