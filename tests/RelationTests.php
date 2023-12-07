<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Tests\Models\Author;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithEagerRelation;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RelationTests
 *
 * @package Tests
 */
class RelationTests extends TestCase
{

    /**
     * Test basic slugging functionality.
     */
    public function testEagerLoading(): void
    {
        Model::shouldBeStrict(true);

        $author = Author::create([
            'name' => 'Arthur Conan Doyle'
        ]);
        $post = new PostWithEagerRelation([
            'title' => 'My First Post'
        ]);
        $post->author()->associate($author);
        $post->save();

        self::assertEquals('arthur-conan-doyle-my-first-post', $post->slug);

        $post2 = new PostWithEagerRelation([
            'title' => 'My second post',
        ]);
        $post2->author()->associate($author);
        $post2->save();
        self::assertEquals('arthur-conan-doyle-my-second-post', $post2->slug);
    }

}
