<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Cviebrock\EloquentSluggable\Tests\Models\Post;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithMultipleSlugsAndPrimary;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithMultipleSlugsAndPrimaryProperty;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithPrimarySlug;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class StaticTests
 *
 * @package Tests
 */
class PrimarySlugTests extends TestCase
{

    /**
     * Test primary slug is set to first defined slug if $model->slugKeyName is not set
     *
     * @test
     */
    public function testSlugKeyNameProperty()
    {

        $post = PostWithMultipleSlugsAndPrimaryProperty::create([
            'title' => 'A Post Title',
            'subtitle' => 'A Post Subtitle'
        ]);

        $this->assertEquals('dummy', $post->getSlugKeyName());
        $this->assertEquals('a.post.subtitle', $post->dummy);
        $this->assertEquals('a.post.subtitle', $post->getSlugKey());

    }

    /**
     * Test primary slug is set to first defined slug if $model->slugKeyName is not set
     *
     * @test
     */
    public function testFirstSlugAsFallback()
    {
        $post = PostWithMultipleSlugsAndPrimary::create([
            'title' => 'A Post Title'
        ]);

        $this->assertEquals('slug', $post->getSlugKeyName());
        $this->assertEquals('a-post-title', $post->getSlugKey());
    }


    /**
     * Test primary slug query scope
     *
     * @test
     */
    public function testQueryScope()
    {

        PostWithMultipleSlugsAndPrimary::create([
            'title' => 'A Post Title A'
        ]);

        $post = PostWithMultipleSlugsAndPrimary::create([
            'title' => 'A Post Title B'
        ]);

        PostWithMultipleSlugsAndPrimary::create([
            'title' => 'A Post Title C'
        ]);

        $this->assertEquals($post->id, PostWithMultipleSlugsAndPrimary::whereSlug('a-post-title-b')->first()->id);

    }

    public function testFindBySlug()
    {

        PostWithMultipleSlugsAndPrimary::create([
            'title' => 'A Post Title A'
        ]);

        $post = PostWithMultipleSlugsAndPrimary::create([
            'title' => 'A Post Title B'
        ]);

        PostWithMultipleSlugsAndPrimary::create([
            'title' => 'A Post Title C'
        ]);

        $this->assertEquals($post->id, PostWithMultipleSlugsAndPrimary::findBySlug('a-post-title-b')->id);
    }

    public function testFindBySlugReturnsNullForNoRecord()
    {
        $this->assertNull(PostWithMultipleSlugsAndPrimary::findBySlug('not a real record'));
    }

    public function testFindBySlugOrFail()
    {
        PostWithMultipleSlugsAndPrimary::create([
            'title' => 'A Post Title A'
        ]);

        $post = PostWithMultipleSlugsAndPrimary::create([
            'title' => 'A Post Title B'
        ]);

        PostWithMultipleSlugsAndPrimary::create([
            'title' => 'A Post Title C'
        ]);

        $this->assertEquals($post->id, PostWithMultipleSlugsAndPrimary::findBySlugOrFail('a-post-title-b')->id);

        $this->expectException(ModelNotFoundException::class);

        PostWithMultipleSlugsAndPrimary::findBySlugOrFail('not a real record');

    }

}
