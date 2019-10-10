<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Tests\Models\Author;
use Cviebrock\EloquentSluggable\Tests\Models\Post;
use Cviebrock\EloquentSluggable\Tests\Models\PostNotSluggable;
use Cviebrock\EloquentSluggable\Tests\Models\PostShortConfig;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomCallableMethod;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomEngine;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomEngine2;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomMethod;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomSeparator;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomSource;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomSuffix;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithEmptySeparator;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithForeignRuleset;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithMaxLength;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithMaxLengthSplitWords;
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
     */
    public function testMultipleSource()
    {
        $post = PostWithMultipleSources::create([
            'title' => 'A Post Title',
            'subtitle' => 'A Subtitle'
        ]);
        $this->assertEquals('a-post-title-a-subtitle', $post->slug);
    }

    public function testLeadingTrailingSpaces()
    {
        $post = Post::create([
            'title' => "\tMy First Post \r\n"
        ]);
        $this->assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test building a slug using a custom method.
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
     * Test building a slug using a custom method.
     */
    public function testCustomCallableMethod()
    {
        $post = PostWithCustomCallableMethod::create([
            'title' => 'A Post Title',
            'subtitle' => 'A Subtitle'
        ]);
        $this->assertEquals('eltit-tsop-a', $post->slug);
    }

    /**
     * Test building a slug using a custom suffix.
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
     * Test building a slug using the __toString method.
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
     */
    public function testReservedWord()
    {
        $post = PostWithReservedSlug::create([
            'title' => 'Add'
        ]);
        $this->assertEquals('add-2', $post->slug);
    }

    /**
     * Test when reverting to a shorter version of a similar slug.
     *
     * @see https://github.com/cviebrock/eloquent-sluggable/issues/5
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
     * Test model replication.
     *
     * @see https://github.com/cviebrock/eloquent-sluggable/issues/20
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
     * Test that we don't try and slug models that don't implement Sluggable.
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
     * Test for max_length option.
     */
    public function testMaxLength()
    {
        $post = PostWithMaxLength::create([
            'title' => 'A post with a really long title'
        ]);
        $this->assertEquals('a-post', $post->slug);
    }

    /**
     * Test for max_length option with word splitting.
     */
    public function testMaxLengthSplitWords()
    {
        $post = PostWithMaxLengthSplitWords::create([
            'title' => 'A post with a really long title'
        ]);
        $this->assertEquals('a-post-wit', $post->slug);
    }

    /**
     * Test for max_length option with increments.
     */
    public function testMaxLengthWithIncrements()
    {
        for ($i = 0; $i < 20; $i++) {
            $post = PostWithMaxLength::create([
                'title' => 'A post with a really long title'
            ]);
            if ($i == 0) {
                $this->assertEquals('a-post', $post->slug);
            } elseif ($i < 10) {
                $this->assertEquals('a-post-' . $i, $post->slug);
            }
        }
    }

    /**
     * Test for max_length option with increments and word splitting.
     */
    public function testMaxLengthSplitWordsWithIncrements()
    {
        for ($i = 0; $i < 20; $i++) {
            $post = PostWithMaxLengthSplitWords::create([
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
     * Test for max_length option with a slug that might end in separator.
     */
    public function testMaxLengthDoesNotEndInSeparator()
    {
        $post = PostWithMaxLengthSplitWords::create([
            'title' => 'It should work'
        ]);
        $this->assertEquals('it-should', $post->slug);
    }

    /**
     * Test that models aren't slugged if the slug field is defined.
     *
     * @see https://github.com/cviebrock/eloquent-sluggable/issues/32
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
     * Test that models aren't *re*slugged if the slug field is defined.
     *
     * @see https://github.com/cviebrock/eloquent-sluggable/issues/32
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
     */
    public function testSlugFromRelatedModelNotExists()
    {
        $post = PostWithRelation::create([
            'title' => 'First'
        ]);
        $this->assertEquals('first', $post->slug);
    }

    /**
     * Test that a null slug source creates a null slug.
     */
    public function testNullSourceGeneratesEmptySlug()
    {
        $post = PostWithCustomSource::create([
            'title' => 'My Test Post'
        ]);
        $this->assertEquals(null, $post->slug);
    }

    /**
     * Test that a zero length slug source creates a null slug.
     */
    public function testZeroLengthSourceGeneratesEmptySlug()
    {
        $post = Post::create([
            'title' => ''
        ]);
        $this->assertNull($post->slug);
    }

    /**
     * Test using custom Slugify rules.
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
     * Test using additional custom Slugify rules.
     */
    public function testCustomEngineRules2()
    {
        $post = new PostWithCustomEngine2([
            'title' => 'The quick brown fox/jumps over/the lazy dog'
        ]);
        $post->save();
        $this->assertEquals('the-quick-brown-fox/jumps-over/the-lazy-dog', $post->slug);
    }

    /**
     * Test using a custom Slugify ruleset.
     */
    public function testForeignRuleset()
    {
        $post = PostWithForeignRuleset::create([
            'title' => 'Mia unua poŝto'
        ]);
        $this->assertEquals('mia-unua-posxto', $post->slug);
    }

    /**
     * Test if using an empty separator works.
     *
     * @see https://github.com/cviebrock/eloquent-sluggable/issues/256
     */
    public function testEmptySeparator()
    {
        $post = new PostWithEmptySeparator([
            'title' => 'My Test Post'
        ]);
        $post->save();
        $this->assertEquals('mytestpost', $post->slug);
    }

    /**
     * Test models with multiple slug fields.
     */
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

    /**
     * Test subscript characters in slug field
     */
    public function testSubscriptCharacters()
    {
        $post = new Post([
            'title' => 'RDA-125-15/30/45m³/h CAV'
        ]);
        $post->save();

        $this->assertEquals('rda-125-15-30-45m3-h-cav', $post->slug);
    }

    /**
     * Test that a false-y string slug source creates a slug.
     */
    public function testFalsyString()
    {
        $post = Post::create([
            'title' => '0'
        ]);
        $this->assertEquals('0', $post->slug);
    }

    /**
     * Test that a false-y int slug source creates a slug.
     */
    public function testFalsyInt()
    {
        $post = Post::create([
            'title' => 0
        ]);
        $this->assertEquals('0', $post->slug);
    }

    /**
     * Test that a boolean true source creates a slug.
     */
    public function testTrueSource()
    {
        $post = Post::create([
            'title' => true
        ]);
        $this->assertEquals('1', $post->slug);
    }

    /**
     * Test that a boolean false slug source creates a slug.
     */
    public function testFalseSource()
    {
        $post = Post::create([
            'title' => false
        ]);
        $this->assertEquals('0', $post->slug);
    }
}
