<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Tests\Listeners\AbortSlugging;
use Cviebrock\EloquentSluggable\Tests\Listeners\DoNotAbortSlugging;
use Cviebrock\EloquentSluggable\Tests\Models\Post;

/**
 * Class EventTests
 *
 * @package Tests
 */
class EventTests extends TestCase
{

    /**
     * Test that the "slugging" event is fired.
     *
     * @todo Figure out how to accurately test Eloquent model events
     */
    public function testEventsAreFired(): void
    {
        self::markTestIncomplete('Event tests are not yet reliable.');

        $this->expectsEvents([
            'eloquent.slugging: ' . Post::class,
            'eloquent.slugged: ' . Post::class,
        ]);

        Post::create([
            'title' => 'My Test Post'
        ]);
    }

    /**
     * Test that the "slugging" event can be cancelled.
     *
     * @todo Figure out how to accurately test Eloquent model events
     */
    public function testDoNotCancelSluggingEventWhenItReturnsAnythingOtherThanFalse(): void
    {
        self::markTestIncomplete('Event tests are not yet reliable.');

        $this->app['events']->listen('eloquent.slugging: ' . Post::class, DoNotAbortSlugging::class);

        $this->expectsEvents([
            'eloquent.slugging: ' . Post::class,
        ]);

        $this->doesntExpectEvents([
            'eloquent.slugged: ' . Post::class,
        ]);

        $post = Post::create([
            'title' => 'My Test Post'
        ]);

        self::assertEquals('my-test-post', $post->slug);
    }

    public function testCancelSluggingEvent(): void
    {
        self::markTestIncomplete('Event tests are not yet reliable.');

        $this->app['events']->listen('eloquent.slugging: ' . Post::class, AbortSlugging::class);

        $this->expectsEvents([
            'eloquent.slugging: ' . Post::class,
        ]);

        $this->doesntExpectEvents([
            'eloquent.slugged: ' . Post::class,
        ]);

        $post = Post::create([
            'title' => 'My Test Post'
        ]);

        self::assertEquals(null, $post->slug);
    }

    /**
     * Test that the "slugged" event is fired.
     *
     * @todo Figure out how to accurately test Eloquent model events
     */
    public function testSluggedEvent(): void
    {
        self::markTestIncomplete('Event tests are not yet reliable.');

        $post = Post::create([
            'title' => 'My Test Post'
        ]);

        self::assertEquals('my-test-post', $post->slug);
        self::assertEquals('I have been slugged!', $post->subtitle);
    }
}
