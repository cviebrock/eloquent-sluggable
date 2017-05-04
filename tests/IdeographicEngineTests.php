<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Tests\Models\PostWithIdeographicEngine;


/**
 * Class UniqueTests
 *
 * @package Tests
 */
class IdeographicEngineTests extends TestCase
{

    /**
     * Test Ideographic slugging functionality.
     */
    public function testIdeographicEngine()
    {
        $post = new PostWithIdeographicEngine([
            'title' => 'The quick brown fox jumps over the lazy dog'
        ]);
        $post->save();
        $this->assertEquals(12, strlen($post->slug));
    }
}
