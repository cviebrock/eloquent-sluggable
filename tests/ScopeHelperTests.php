<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Tests\Models\PostShortConfigWithScopeHelpers;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithMultipleSlugsAndCustomSlugKey;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithMultipleSlugsAndHelperTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class ScopeHelperTests
 *
 * @package Tests
 */
class ScopeHelperTests extends TestCase
{

    /**
     * Test that primary slug is set to $model->slugKeyName when set.
     */
    public function testSlugKeyNameProperty(): void
    {

        $post = PostWithMultipleSlugsAndCustomSlugKey::create([
            'title' => 'A Post Title',
            'subtitle' => 'A Post Subtitle'
        ]);

        self::assertEquals('dummy', $post->getSlugKeyName());
        self::assertEquals('a.post.subtitle', $post->dummy);
        self::assertEquals('a.post.subtitle', $post->getSlugKey());
    }

    /**
     * Test primary slug is set to first defined slug if $model->slugKeyName is not set.
     */
    public function testFirstSlugAsFallback(): void
    {
        $post = PostWithMultipleSlugsAndHelperTrait::create([
            'title' => 'A Post Title'
        ]);

        self::assertEquals('slug', $post->getSlugKeyName());
        self::assertEquals('a-post-title', $post->getSlugKey());
    }

    /**
     * Test primary slug query scope.
     */
    public function testQueryScope(): void
    {

        PostWithMultipleSlugsAndHelperTrait::create([
            'title' => 'A Post Title A'
        ]);

        $post = PostWithMultipleSlugsAndHelperTrait::create([
            'title' => 'A Post Title B'
        ]);

        PostWithMultipleSlugsAndHelperTrait::create([
            'title' => 'A Post Title C'
        ]);

        self::assertEquals($post->getKey(),
            PostWithMultipleSlugsAndHelperTrait::whereSlug('a-post-title-b')->first()->getKey());
    }

    /**
     * Test finding a model by its primary slug.
     */
    public function testFindBySlug(): void
    {

        PostWithMultipleSlugsAndHelperTrait::create([
            'title' => 'A Post Title A'
        ]);

        $post = PostWithMultipleSlugsAndHelperTrait::create([
            'title' => 'A Post Title B'
        ]);

        PostWithMultipleSlugsAndHelperTrait::create([
            'title' => 'A Post Title C'
        ]);

        self::assertEquals($post->getKey(),
            PostWithMultipleSlugsAndHelperTrait::findBySlug('a-post-title-b')->getKey());
    }

    /**
     * Test finding a model by its primary slug fails if the slug does not exist.
     */
    public function testFindBySlugReturnsNullForNoRecord(): void
    {
        self::assertNull(PostWithMultipleSlugsAndHelperTrait::findBySlug('not a real record'));
    }

    /**
     * Test finding a model by its primary slug throws an exception if the slug does not exist.
     */
    public function testFindBySlugOrFail(): void
    {
        PostWithMultipleSlugsAndHelperTrait::create([
            'title' => 'A Post Title A'
        ]);

        $post = PostWithMultipleSlugsAndHelperTrait::create([
            'title' => 'A Post Title B'
        ]);

        PostWithMultipleSlugsAndHelperTrait::create([
            'title' => 'A Post Title C'
        ]);

        self::assertEquals($post->getKey(),
            PostWithMultipleSlugsAndHelperTrait::findBySlugOrFail('a-post-title-b')->getKey());

        $this->expectException(ModelNotFoundException::class);

        PostWithMultipleSlugsAndHelperTrait::findBySlugOrFail('not a real record');
    }

    /**
     * Test that getSlugKeyName() works with the short configuration syntax.
     */
    public function testGetSlugKeyNameWithShortConfig(): void
    {
        $post = new PostShortConfigWithScopeHelpers();
        self::assertEquals('slug_field', $post->getSlugKeyName());
    }
}
