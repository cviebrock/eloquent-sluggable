<?php namespace Tests;

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
    public function testSluggingEvent()
    {
        // test event to modify the model before slugging
        Post::registerModelEvent('slugging', function ($post) {
            $post->title = 'Modified by event';
        });

        $post = new Post(['title' => 'My Test Post']);
        $post->save();
        $this->assertEquals('modified-by-event', $post->slug);
    }

    /**
     * Test that the "slugging" event can be cancelled.
     *
     * @test
     */
    public function testCancelSluggingEvent()
    {
        // test event to cancel the slugging
        Post::registerModelEvent('slugging', function ($post) {
            return false;
        });

        $post = new Post(['title' => 'My Test Post']);
        $post->save();
        $this->assertEquals(null, $post->slug);
    }

    /**
     * Test that the "slugged" event is fired.
     *
     * @test
     */
    public function testSluggedEvent()
    {
        Post::registerModelEvent('slugged', function ($post) {
            $post->subtitle = 'I have been slugged!';
        });

        $post = new Post(['title' => 'My Test Post']);
        $post->save();
        $this->assertEquals('my-test-post', $post->slug);
        $this->assertEquals('I have been slugged!', $post->subtitle);
    }
}
