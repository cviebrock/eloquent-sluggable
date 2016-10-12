<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Tests\Models\Author;
use Cviebrock\EloquentSluggable\Tests\Models\Post;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithUniqueSlugConstraints;

/**
 * Class UniqueTests
 *
 * @package Tests
 */
class UniqueTests extends TestCase
{

    /**
     * Test uniqueness of generated slugs.
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

    /**
     * Test custom unique query scopes.
     */
    public function testCustomUniqueQueryScope()
    {
        $authorBob = Author::create(['name' => 'Bob']);
        $authorPam = Author::create(['name' => 'Pam']);

        // Bob's first post
        $post = new PostWithUniqueSlugConstraints(['title' => 'My first post']);
        $post->author()->associate($authorBob);
        $post->save();

        $this->assertEquals('my-first-post', $post->slug);

        // Bob's second post with same title is made unique
        $post = new PostWithUniqueSlugConstraints(['title' => 'My first post']);
        $post->author()->associate($authorBob);
        $post->save();

        $this->assertEquals('my-first-post-1', $post->slug);

        // Pam's first post with same title is scoped to her
        $post = new PostWithUniqueSlugConstraints(['title' => 'My first post']);
        $post->author()->associate($authorPam);
        $post->save();

        $this->assertEquals('my-first-post', $post->slug);

        // Pam's second post with same title is scoped to her and made unique
        $post = new PostWithUniqueSlugConstraints(['title' => 'My first post']);
        $post->author()->associate($authorPam);
        $post->save();

        $this->assertEquals('my-first-post-1', $post->slug);
    }
}
