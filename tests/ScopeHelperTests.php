<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Tests\Models\PostShortConfigWithScopeHelpers;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithMultipleSlugsAndCustomSlugKey;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithMultipleSlugsAndHelperTrait;

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
    public function testSlugKeyNameProperty()
    {

        $post = PostWithMultipleSlugsAndCustomSlugKey::create([
            'title' => 'A Post Title',
            'subtitle' => 'A Post Subtitle'
        ]);

        $this->assertEquals('dummy', $post->getSlugKeyName());
        $this->assertEquals('a.post.subtitle', $post->dummy);
        $this->assertEquals('a.post.subtitle', $post->getSlugKey());
    }

    /**
     * Test primary slug is set to first defined slug if $model->slugKeyName is not set.
     */
    public function testFirstSlugAsFallback()
    {
        $post = PostWithMultipleSlugsAndHelperTrait::create([
            'title' => 'A Post Title'
        ]);

        $this->assertEquals('slug', $post->getSlugKeyName());
        $this->assertEquals('a-post-title', $post->getSlugKey());
    }

    /**
     * Test primary slug query scope.
     */
    public function testQueryScope()
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

        $this->assertEquals($post->getKey(),
            PostWithMultipleSlugsAndHelperTrait::whereSlug('a-post-title-b')->first()->getKey());
    }

    /**
     * Test finding a model by its primary slug.
     */
    public function testFindBySlug()
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

        $this->assertEquals($post->getKey(),
            PostWithMultipleSlugsAndHelperTrait::findBySlug('a-post-title-b')->getKey());
    }

    /**
     * Test finding a model by its primary slug fails if the slug does not exist.
     */
    public function testFindBySlugReturnsNullForNoRecord()
    {
        $this->assertNull(PostWithMultipleSlugsAndHelperTrait::findBySlug('not a real record'));
    }

    /**
     * Test finding a model by its primary slug throws an exception if the slug does not exist.
     */
    public function testFindBySlugOrFail()
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

        $this->assertEquals($post->getKey(),
            PostWithMultipleSlugsAndHelperTrait::findBySlugOrFail('a-post-title-b')->getKey());

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        PostWithMultipleSlugsAndHelperTrait::findBySlugOrFail('not a real record');
    }

    /**
     * Test that getSlugKeyName() works with the short configuration syntax.
     */
    public function testGetSlugKeyNameWithShortConfig()
    {
        $post = new PostShortConfigWithScopeHelpers();
        $this->assertEquals('slug_field', $post->getSlugKeyName());
    }
}
