<?php namespace Tests;

use Tests\Models\Post;


/**
 * Class SluggableTest
 */
class UniqueTests extends TestCase
{

    /**
     * Test uniqueness of generated slugs.
     *
     * @test
     */
    public function testUnique()
    {
        for ($i = 0; $i < 20; $i++) {
            $post = Post::create([
                'title' => 'A post title'
            ]);
            if ($i == 0) {
                $this->assertEquals('a-post-title', $post->slug);
            } else {
                $this->assertEquals('a-post-title-' . $i, $post->slug);
            }
        }
    }

    /**
     * Test uniqueness after deletion.
     *
     * @test
     */
    public function testUniqueAfterDelete()
    {
        $post1 = Post::create([
            'title' => 'A post title'
        ]);
        $this->assertEquals('a-post-title', $post1->slug);

        $post2 = Post::create([
            'title' => 'A post title'
        ]);
        $this->assertEquals('a-post-title-1', $post2->slug);

        $post1->delete();

        $post3 = Post::create([
            'title' => 'A post title'
        ]);
        $this->assertEquals('a-post-title', $post3->slug);
    }

}
