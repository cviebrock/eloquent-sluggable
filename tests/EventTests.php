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
    public function testEventsAreFired()
    {
        $this->markTestIncomplete('Event tests are not yet reliable.');

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
     * @todo Figure out how to accurately test Eloquent model events
     */
    public function testDoNotCancelSluggingEventWhenItReturnsAnythingOtherThanFalse()
    {
        $this->markTestIncomplete('Event tests are not yet reliable.');

        $this->app['events']->listen('eloquent.slugging: ' . Post::class, DoNotAbortSlugging::class);

        $post = Post::create([
            'title' => 'My Test Post'
        ]);

        $this->expectsEvents([
            'eloquent.slugging: ' . Post::class,
        ]);

        $this->doesntExpectEvents([
            'eloquent.slugged: ' . Post::class,
        ]);

        $this->assertEquals('my-test-post', $post->slug);
    }

    public function testCancelSluggingEvent()
    {
        $this->markTestIncomplete('Event tests are not yet reliable.');

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
     * @todo Figure out how to accurately test Eloquent model events
     */
    public function testSluggedEvent()
    {
        $this->markTestIncomplete('Event tests are not yet reliable.');

        $post = Post::create([
            'title' => 'My Test Post'
        ]);

        $this->assertEquals('my-test-post', $post->slug);
        $this->assertEquals('I have been slugged!', $post->subtitle);
    }
}
