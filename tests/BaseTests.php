<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Tests\Models\Author;
use Cviebrock\EloquentSluggable\Tests\Models\Post;
use Cviebrock\EloquentSluggable\Tests\Models\PostNotSluggable;
use Cviebrock\EloquentSluggable\Tests\Models\PostShortConfig;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomCallableMethod;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomEngine;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomEngine2;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomEngineOptions;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomMethod;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomMethodArrayCall;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomSeparator;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomSource;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithForeignRuleset2;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithIdSource;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithCustomSuffix;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithEmptySeparator;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithForeignRuleset;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithIdSourceOnSaved;
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
    public function testSimpleSlug(): void
    {
        $post = Post::create([
            'title' => 'My First Post'
        ]);
        self::assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test basic slugging functionality using short configuration syntax.
     */
    public function testShortConfig(): void
    {
        $post = PostShortConfig::create([
            'title' => 'My First Post'
        ]);
        self::assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test that accented characters and other stuff is "fixed".
     */
    public function testAccentedCharacters(): void
    {
        $post = Post::create([
            'title' => 'My Dinner With André & François'
        ]);
        self::assertEquals('my-dinner-with-andre-francois', $post->slug);
    }

    /**
     * Test building a slug from multiple attributes.
     */
    public function testMultipleSource(): void
    {
        $post = PostWithMultipleSources::create([
            'title' => 'A Post Title',
            'subtitle' => 'A Subtitle'
        ]);
        self::assertEquals('a-post-title-a-subtitle', $post->slug);
    }

    public function testLeadingTrailingSpaces(): void
    {
        $post = Post::create([
            'title' => "\tMy First Post \r\n"
        ]);
        self::assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test building a slug using a custom method.
     */
    public function testCustomMethod(): void
    {
        $post = PostWithCustomMethod::create([
            'title' => 'A Post Title',
            'subtitle' => 'A Subtitle'
        ]);
        self::assertEquals('eltit-tsop-a', $post->slug);
    }

    /**
     * Test building a slug using a custom method.
     */
    public function testCustomCallableMethod(): void
    {
        $post = PostWithCustomCallableMethod::create([
            'title' => 'A Post Title',
            'subtitle' => 'A Subtitle'
        ]);
        self::assertEquals('eltit-tsop-a', $post->slug);
    }

    /**
     * Test building a slug using a custom suffix.
     */
    public function testCustomSuffix(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $post = PostWithCustomSuffix::create([
                'title' => 'A Post Title',
                'subtitle' => 'A Subtitle',
            ]);

            if ($i === 1) {
                self::assertEquals('a-post-title', $post->slug);
            } else {
                self::assertEquals('a-post-title-' . chr($i + 95), $post->slug);
            }
        }
    }

    /**
     * Test building a slug using the __toString method.
     */
    public function testToStringMethod(): void
    {
        $post = PostWithNoSource::create([
            'title' => 'A Post Title'
        ]);
        self::assertEquals('a-post-title', $post->slug);
    }

    /**
     * Test using a custom separator.
     */
    public function testCustomSeparator(): void
    {
        $post = PostWithCustomSeparator::create([
            'title' => 'A post title'
        ]);
        self::assertEquals('a.post.title', $post->slug);
    }

    /**
     * Test using reserved word blocking.
     */
    public function testReservedWord(): void
    {
        $post = PostWithReservedSlug::create([
            'title' => 'Add'
        ]);
        self::assertEquals('add-2', $post->slug);
    }

    /**
     * Test when reverting to a shorter version of a similar slug.
     *
     * @see https://github.com/cviebrock/eloquent-sluggable/issues/5
     */
    public function testIssue5(): void
    {
        $post = Post::create([
            'title' => 'My first post'
        ]);
        self::assertEquals('my-first-post', $post->slug);

        $post->title = 'My first post rocks';
        $post->slug = null;
        $post->save();
        self::assertEquals('my-first-post-rocks', $post->slug);

        $post->title = 'My first post';
        $post->slug = null;
        $post->save();
        self::assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test model replication.
     *
     * @see https://github.com/cviebrock/eloquent-sluggable/issues/20
     */
    public function testIssue20(): void
    {
        $post1 = Post::create([
            'title' => 'My first post'
        ]);
        self::assertEquals('my-first-post', $post1->slug);

        $post2 = $post1->replicate();
        self::assertEquals('my-first-post-2', $post2->slug);
    }

    /**
     * Test that we don't try and slug models that don't implement Sluggable.
     */
    public function testNonSluggableModels(): void
    {
        $post = PostNotSluggable::create([
            'title' => 'My First Post'
        ]);
        self::assertEquals(null, $post->slug);
    }

    /**
     * Test for max_length option.
     */
    public function testMaxLength(): void
    {
        $post = PostWithMaxLength::create([
            'title' => 'A post with a really long title'
        ]);
        self::assertEquals('a-post', $post->slug);
    }

    /**
     * Test for max_length option with word splitting.
     */
    public function testMaxLengthSplitWords(): void
    {
        $post = PostWithMaxLengthSplitWords::create([
            'title' => 'A post with a really long title'
        ]);
        self::assertEquals('a-post-wit', $post->slug);
    }

    /**
     * Test for max_length option with increments.
     */
    public function testMaxLengthWithIncrements(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $post = PostWithMaxLength::create([
                'title' => 'A post with a really long title'
            ]);
            if ($i === 1) {
                self::assertEquals('a-post', $post->slug);
            } else {
                self::assertEquals('a-post-' . $i, $post->slug);
            }
        }
    }

    /**
     * Test for max_length option with increments and word splitting.
     */
    public function testMaxLengthSplitWordsWithIncrements(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $post = PostWithMaxLengthSplitWords::create([
                'title' => 'A post with a really long title'
            ]);
            if ($i === 1) {
                self::assertEquals('a-post-wit', $post->slug);
            } else {
                self::assertEquals('a-post-wit-' . $i, $post->slug);
            }
        }
    }

    /**
     * Test for max_length option with a slug that might end in separator.
     */
    public function testMaxLengthDoesNotEndInSeparator(): void
    {
        $post = PostWithMaxLengthSplitWords::create([
            'title' => 'It should work'
        ]);
        self::assertEquals('it-should', $post->slug);
    }

    /**
     * Test that models aren't slugged if the slug field is defined.
     *
     * @see https://github.com/cviebrock/eloquent-sluggable/issues/32
     */
    public function testDoesNotNeedSluggingWhenSlugIsSet(): void
    {
        $post = Post::create([
            'title' => 'My first post',
            'slug' => 'custom-slug'
        ]);
        self::assertEquals('custom-slug', $post->slug);
    }

    /**
     * Test that models aren't *re*slugged if the slug field is defined.
     *
     * @see https://github.com/cviebrock/eloquent-sluggable/issues/32
     */
    public function testDoesNotNeedSluggingWithUpdateWhenSlugIsSet(): void
    {
        $post = Post::create([
            'title' => 'My first post',
            'slug' => 'custom-slug'
        ]);
        self::assertEquals('custom-slug', $post->slug);

        $post->title = 'A New Title';
        $post->save();
        self::assertEquals('custom-slug', $post->slug);

        $post->title = 'A Another New Title';
        $post->slug = 'new-custom-slug';
        $post->save();
        self::assertEquals('new-custom-slug', $post->slug);
    }

    /**
     * Test that models are still updated even if slug is not updated.
     *
     * @see https://github.com/cviebrock/eloquent-sluggable/issues/559
     */
    public function testModelStillSavesWhenSlugIsNotUpdated()
    {
        $post = Post::create([
            'title' => 'My Post',
            'subtitle' => 'My First Subtitle',
        ]);

        self::assertEquals('my-post', $post->slug);

        $post->subtitle = 'My Second Subtitle';
        $post->save();
        $post->refresh();

        self::assertEquals('my-post', $post->slug);
        self::assertEquals('My Second Subtitle', $post->subtitle);
    }

    /**
     * Test generating slug from related model field.
     */
    public function testSlugFromRelatedModel(): void
    {
        $author = Author::create([
            'name' => 'Arthur Conan Doyle'
        ]);
        $post = new PostWithRelation([
            'title' => 'First'
        ]);
        $post->author()->associate($author);
        $post->save();
        self::assertEquals('arthur-conan-doyle-first', $post->slug);
    }

    /**
     * Test generating slug when related model doesn't exists.
     */
    public function testSlugFromRelatedModelNotExists(): void
    {
        $post = PostWithRelation::create([
            'title' => 'First'
        ]);
        self::assertEquals('first', $post->slug);
    }

    /**
     * Test that a null slug source creates a null slug.
     */
    public function testNullSourceGeneratesEmptySlug(): void
    {
        $post = PostWithCustomSource::create([
            'title' => 'My Test Post'
        ]);
        self::assertEquals(null, $post->slug);
    }

    /**
     * Test that a zero length slug source creates a null slug.
     */
    public function testZeroLengthSourceGeneratesEmptySlug(): void
    {
        $post = Post::create([
            'title' => ''
        ]);
        self::assertNull($post->slug);
    }

    /**
     * Test using custom Slugify rules.
     */
    public function testCustomEngineRules(): void
    {
        $post = PostWithCustomEngine::create([
            'title' => 'The quick brown fox jumps over the lazy dog'
        ]);
        self::assertEquals('tha-qaack-brawn-fax-jamps-avar-tha-lazy-dag', $post->slug);
    }

    /**
     * Test using additional custom Slugify rules.
     */
    public function testCustomEngineRules2(): void
    {
        $post = PostWithCustomEngine2::create([
            'title' => 'The quick brown fox/jumps over/the lazy dog'
        ]);
        self::assertEquals('the-quick-brown-fox/jumps-over/the-lazy-dog', $post->slug);
    }

    public function testCustomEngineOptions(): void
    {
        $post = PostWithCustomEngineOptions::create([
            'title' => 'My First Post'
        ]);
        self::assertEquals('My-First-Post', $post->slug);
    }

    /**
     * Test using a custom Slugify ruleset.
     */
    public function testForeignRuleset(): void
    {
        $post = PostWithForeignRuleset::create([
            'title' => 'Mia unua poŝto'
        ]);
        self::assertEquals('mia-unua-posxto', $post->slug);
    }

    /**
     * Test using a custom Slugify ruleset.
     */
    public function testForeignRuleset2(): void
    {
        $post = PostWithForeignRuleset2::create([
            'title' => 'Jyväskylä'
        ]);
        self::assertEquals('jyvaskyla', $post->slug);
    }

    /**
     * Test if using an empty separator works.
     *
     * @see https://github.com/cviebrock/eloquent-sluggable/issues/256
     */
    public function testEmptySeparator(): void
    {
        $post = PostWithEmptySeparator::create([
            'title' => 'My Test Post'
        ]);
        self::assertEquals('mytestpost', $post->slug);
    }

    /**
     * Test models with multiple slug fields.
     */
    public function testMultipleSlugs(): void
    {
        $post = PostWithMultipleSlugs::create([
            'title' => 'My Test Post',
            'subtitle' => 'My Subtitle',
        ]);

        self::assertEquals('my-test-post', $post->slug);
        self::assertEquals('my.subtitle', $post->dummy);
    }

    /**
     * Test subscript characters in slug field
     */
    public function testSubscriptCharacters(): void
    {
        $post = Post::create([
            'title' => 'RDA-125-15/30/45m³/h CAV'
        ]);

        self::assertEquals('rda-125-15-30-45m3-h-cav', $post->slug);
    }

    /**
     * Test that a false-y string slug source creates a slug.
     */
    public function testFalsyString(): void
    {
        $post = Post::create([
            'title' => '0'
        ]);
        self::assertEquals('0', $post->slug);
    }

    /**
     * Test that a false-y int slug source creates a slug.
     */
    public function testFalsyInt(): void
    {
        $post = Post::create([
            'title' => 0
        ]);
        self::assertEquals('0', $post->slug);
    }

    /**
     * Test that a boolean true source creates a slug.
     */
    public function testTrueSource(): void
    {
        $post = Post::create([
            'title' => true
        ]);
        self::assertEquals('1', $post->slug);
    }

    /**
     * Test that a boolean false slug source creates a slug.
     */
    public function testFalseSource(): void
    {
        $post = Post::create([
            'title' => false
        ]);
        self::assertEquals('0', $post->slug);
    }

    /**
     * Test that manually setting the slug to "0" doesn't
     * force a re-slugging.
     */
    public function testIssue527(): void
    {
        $post = Post::create([
            'title' => 'example title'
        ]);
        self::assertEquals('example-title', $post->slug);

        $post->slug = '0';
        $post->save();
        self::assertEquals('0', $post->slug);

        $post->slug = '';
        $post->save();
        self::assertEquals('example-title', $post->slug);
    }

    /**
     * Test that you can use the model's primary key
     * as part of the source field when the sluggableEvent
     * is using the SAVED observer.
     */
    public function testPrimaryKeyInSource(): void
    {
        $post = PostWithIdSourceOnSaved::create([
            'title' => 'My First Post'
        ]);
        self::assertEquals('my-first-post-1', $post->slug);

        $post2 = PostWithIdSourceOnSaved::create([
            'title' => 'My Second Post'
        ]);
        self::assertEquals('my-second-post-2', $post2->slug);

        $post->title = 'Still My First Post';
        $post->save();
        self::assertEquals('still-my-first-post-1', $post->slug);
    }

    /**
     * Test that when using the SAVED observer the slug is
     * actually persisted in storage.
     */
    public function testOnSavedPersistsSlug()
    {
        $post = PostWithIdSourceOnSaved::create([
            'title' => 'My Test Post',
        ]);
        $post->refresh();

        self::assertEquals('my-test-post-1', $post->slug);
    }

    /**
     * Test that you can't use the model's primary key
     * as part of the source field if the sluggableEvent
     * is the default SAVING.
     */
    public function testPrimaryKeyInSourceOnSaving(): void
    {
        $post = PostWithIdSource::create([
            'title' => 'My First Post'
        ]);
        self::assertEquals('my-first-post', $post->slug);

        $post->title = 'Still My First Post';
        $post->save();

        self::assertEquals('still-my-first-post-1', $post->slug);
    }

    /**
     * Test building a slug using a custom method with array call.
     */
    public function testCustomMethodArrayCall(): void
    {
        $post = PostWithCustomMethodArrayCall::create([
            'title' => 'A Post Title',
            'subtitle' => 'A Subtitle'
        ]);
        self::assertEquals('eltit-tsop-a', $post->slug);
    }
}
