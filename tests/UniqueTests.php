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
    public function testUnique(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $post = Post::create([
                'title' => 'A post title'
            ]);
            if ($i === 0) {
                self::assertEquals('a-post-title', $post->slug);
            } else {
                self::assertEquals('a-post-title-' . $i, $post->slug);
            }
        }
    }

    /**
     * Test uniqueness after deletion.
     */
    public function testUniqueAfterDelete(): void
    {
        $post1 = Post::create([
            'title' => 'A post title'
        ]);
        self::assertEquals('a-post-title', $post1->slug);

        $post2 = Post::create([
            'title' => 'A post title'
        ]);
        self::assertEquals('a-post-title-1', $post2->slug);

        $post1->delete();

        $post3 = Post::create([
            'title' => 'A post title'
        ]);
        self::assertEquals('a-post-title', $post3->slug);
    }

    /**
     * Test custom unique query scopes.
     */
    public function testCustomUniqueQueryScope(): void
    {
        $authorBob = Author::create(['name' => 'Bob']);
        $authorPam = Author::create(['name' => 'Pam']);

        // Bob's first post
        $post = new PostWithUniqueSlugConstraints(['title' => 'My first post']);
        $post->author()->associate($authorBob);
        $post->save();

        self::assertEquals('my-first-post', $post->slug);

        // Bob's second post with same title is made unique
        $post = new PostWithUniqueSlugConstraints(['title' => 'My first post']);
        $post->author()->associate($authorBob);
        $post->save();

        self::assertEquals('my-first-post-1', $post->slug);

        // Pam's first post with same title is scoped to her
        $post = new PostWithUniqueSlugConstraints(['title' => 'My first post']);
        $post->author()->associate($authorPam);
        $post->save();

        self::assertEquals('my-first-post', $post->slug);

        // Pam's second post with same title is scoped to her and made unique
        $post = new PostWithUniqueSlugConstraints(['title' => 'My first post']);
        $post->author()->associate($authorPam);
        $post->save();

        self::assertEquals('my-first-post-1', $post->slug);
    }

    public function testIssue431(): void
    {
        $post1 = Post::create([
            'title' => 'A post title'
        ]);
        self::assertEquals('a-post-title', $post1->slug);

        $post2 = new Post;
        $post2->title = 'A post title';
        $post2->save();
        self::assertEquals('a-post-title-1', $post2->slug);
    }
}
