<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Tests\Models\Author;
use Cviebrock\EloquentSluggable\Tests\Models\Post;
use Cviebrock\EloquentSluggable\Tests\Models\PostNotSluggable;
use Cviebrock\EloquentSluggable\Tests\Models\PostShortConfig;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomEngine;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomMethod;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomSeparator;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomSource;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomSuffix;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithEmptySeparator;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithMaxLength;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithMultipleSlugs;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithMultipleSources;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithNoSource;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithRelation;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithReservedSlug;

/**
 * Class BaseTests
 *
 * @package Tests
 */
class BaseTests extends TestCase
{

    /**
     * Test basic slugging functionality.
     *
     * @test
     */
    public function testSimpleSlug()
    {
        $post = Post::create([
            'title' => 'My First Post'
        ]);
        $this->assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test basic slugging functionality using short configuration syntax.
     *
     * @test
     */
    public function testShortConfig()
    {
        $post = PostShortConfig::create([
            'title' => 'My First Post'
        ]);
        $this->assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test that accented characters and other stuff is "fixed".
     *
     * @test
     */
    public function testAccentedCharacters()
    {
        $post = Post::create([
            'title' => 'My Dinner With André & François'
        ]);
        $this->assertEquals('my-dinner-with-andre-francois', $post->slug);
    }

    /**
     * Test building a slug from multiple attributes.
     *
     * @test
     */
    public function testMultipleSource()
    {
        $post = PostWithMultipleSources::create([
            'title' => 'A Post Title',
            'subtitle' => 'A Subtitle'
        ]);
        $this->assertEquals('a-post-title-a-subtitle', $post->slug);
    }

    /**
     * Test building a slug using a custom method.
     *
     * @test
     */
    public function testCustomMethod()
    {
        $post = PostWithCustomMethod::create([
            'title' => 'A Post Title',
            'subtitle' => 'A Subtitle'
        ]);
        $this->assertEquals('eltit-tsop-a', $post->slug);
    }

    /**
     * Test building a slug using a custom suffix.
     *
     * @test
     */
    public function testCustomSuffix()
    {
        for ($i = 0; $i < 20; $i++) {
            $post = PostWithCustomSuffix::create([
                'title' => 'A Post Title',
                'subtitle' => 'A Subtitle',
            ]);

            if ($i === 0) {
                $this->assertEquals('a-post-title', $post->slug);
            } else {
                $this->assertEquals('a-post-title-' . chr($i + 96), $post->slug);
            }
        }
    }

    /**
     * Test building a slug using the __toString method
     *
     * @test
     */
    public function testToStringMethod()
    {
        $post = PostWithNoSource::create([
            'title' => 'A Post Title'
        ]);
        $this->assertEquals('a-post-title', $post->slug);
    }

    /**
     * Test using a custom separator.
     *
     * @test
     */
    public function testCustomSeparator()
    {
        $post = PostWithCustomSeparator::create([
            'title' => 'A post title'
        ]);
        $this->assertEquals('a.post.title', $post->slug);
    }

    /**
     * Test using reserved word blocking.
     *
     * @test
     */
    public function testReservedWord()
    {
        $post = PostWithReservedSlug::create([
            'title' => 'Add'
        ]);
        $this->assertEquals('add-1', $post->slug);
    }

    /**
     * Test when reverting to a shorter version of a similar slug (issue #5)
     *
     * @test
     */
    public function testIssue5()
    {
        $post = Post::create([
            'title' => 'My first post'
        ]);
        $this->assertEquals('my-first-post', $post->slug);

        $post->title = 'My first post rocks';
        $post->slug = null;
        $post->save();
        $this->assertEquals('my-first-post-rocks', $post->slug);

        $post->title = 'My first post';
        $post->slug = null;
        $post->save();
        $this->assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test model replication (issue #20)
     *
     * @test
     */
    public function testIssue20()
    {
        $post1 = Post::create([
            'title' => 'My first post'
        ]);
        $this->assertEquals('my-first-post', $post1->slug);

        $post2 = $post1->replicate();
        $this->assertEquals('my-first-post-1', $post2->slug);
    }

    /**
     * Test that we don't try and slug models that don't implement Sluggable
     *
     * @test
     */
    public function testNonSluggableModels()
    {
        $post = new PostNotSluggable([
            'title' => 'My First Post'
        ]);
        $post->save();
        $this->assertEquals(null, $post->slug);
    }

    /**
     * Test for max_length option
     *
     * @test
     */
    public function testMaxLength()
    {
        $post = PostWithMaxLength::create([
            'title' => 'A post with a really long title'
        ]);
        $this->assertEquals('a-post-wit', $post->slug);
    }

    /**
     * Test for max_length option with increments
     *
     * @test
     */
    public function testMaxLengthWithIncrements()
    {
        for ($i = 0; $i < 20; $i++) {
            $post = PostWithMaxLength::create([
                'title' => 'A post with a really long title'
            ]);
            if ($i == 0) {
                $this->assertEquals('a-post-wit', $post->slug);
            } elseif ($i < 10) {
                $this->assertEquals('a-post-wit-' . $i, $post->slug);
            }
        }
    }

    /**
     * Test that models aren't slugged if the slug field is defined (issue #32)
     *
     * @test
     */
    public function testDoesNotNeedSluggingWhenSlugIsSet()
    {
        $post = Post::create([
            'title' => 'My first post',
            'slug' => 'custom-slug'
        ]);
        $this->assertEquals('custom-slug', $post->slug);
    }

    /**
     * Test that models aren't *re*slugged if the slug field is defined (issue #32)
     *
     * @test
     */
    public function testDoesNotNeedSluggingWithUpdateWhenSlugIsSet()
    {
        $post = Post::create([
            'title' => 'My first post',
            'slug' => 'custom-slug'
        ]);
        $this->assertEquals('custom-slug', $post->slug);

        $post->title = 'A New Title';
        $post->save();
        $this->assertEquals('custom-slug', $post->slug);

        $post->title = 'A Another New Title';
        $post->slug = 'new-custom-slug';
        $post->save();
        $this->assertEquals('new-custom-slug', $post->slug);
    }

    /**
     * Test generating slug from related model field.
     *
     * @test
     */
    public function testSlugFromRelatedModel()
    {
        $author = Author::create([
            'name' => 'Arthur Conan Doyle'
        ]);
        $post = new PostWithRelation([
            'title' => 'First'
        ]);
        $post->author()->associate($author);
        $post->save();
        $this->assertEquals('arthur-conan-doyle-first', $post->slug);
    }

    /**
     * Test generating slug when related model doesn't exists.
     *
     * @test
     */
    public function testSlugFromRelatedModelNotExists()
    {
        $post = PostWithRelation::create([
            'title' => 'First'
        ]);
        $this->assertEquals('first', $post->slug);
    }

    /**
     * Test that an empty slug source creates a null slug.
     *
     * @test
     */
    public function testEmptySourceGeneratesEmptySlug()
    {
        $post = PostWithCustomSource::create([
            'title' => 'My Test Post'
        ]);
        $this->assertEquals(null, $post->slug);
    }

    /**
     * Test using custom Slugify rules.
     *
     * @test
     */
    public function testCustomEngineRules()
    {
        $post = new PostWithCustomEngine([
            'title' => 'The quick brown fox jumps over the lazy dog'
        ]);
        $post->save();
        $this->assertEquals('tha-qaack-brawn-fax-jamps-avar-tha-lazy-dag', $post->slug);
    }

    /**
     * Test if using an empty separator works (issue 256).
     *
     * @test
     */
    public function testEmptySeparator()
    {
        $post = new PostWithEmptySeparator([
            'title' => 'My Test Post'
        ]);
        $post->save();
        $this->assertEquals('mytestpost', $post->slug);
    }

    public function testMultipleSlugs()
    {
        $post = new PostWithMultipleSlugs([
            'title' => 'My Test Post',
            'subtitle' => 'My Subtitle',
        ]);
        $post->save();

        $this->assertEquals('my-test-post', $post->slug);
        $this->assertEquals('my.subtitle', $post->dummy);
    }
}
