<?php namespace Cviebrock\EloquentSluggable\Tests;

use Cviebrock\EloquentSluggable\Tests\Models\Post;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithMultipleSlugs;
use Cviebrock\EloquentSluggable\Tests\Models\PostWithMultipleSlugsAndFindByDefault;

/**
 * Class FindBySlugTests
 *
 * @package Tests
 */
class FindBySlugTests extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        PostWithMultipleSlugs::create([
            'title' => 'My Test Post',
            'subtitle' => 'My Subtitle',
        ]);

        Post::create([
            'title' => 'Simple Post'
        ]);
    }

    public function testWhereOneOfMySlugs()
    {
        $this->assertNotNull(Post::whereOneOfMySlugs('simple-post')->first());

        $this->assertNotNull(PostWithMultipleSlugs::whereOneOfMySlugs('my-test-post')->first());
        $this->assertNotNull(PostWithMultipleSlugs::whereOneOfMySlugs('my-test-post', 'slug')->first());

        $this->assertNotNull(PostWithMultipleSlugs::whereOneOfMySlugs('my.subtitle')->first());
        $this->assertNotNull(PostWithMultipleSlugs::whereOneOfMySlugs('my.subtitle', ['dummy'])->first());

        $this->assertNull(PostWithMultipleSlugs::whereOneOfMySlugs('my-test-post', 'dummy')->first());
    }

    public function testFindBySlug()
    {
        $this->assertNotNull(Post::findBySlug('simple-post'));

        $this->assertNotNull(PostWithMultipleSlugs::findBySlug('my-test-post'));
        $this->assertNotNull(PostWithMultipleSlugs::findBySlug('my-test-post', 'slug'));

        $this->assertNotNull(PostWithMultipleSlugs::findBySlug('my.subtitle'));
        $this->assertNotNull(PostWithMultipleSlugs::findBySlug('my.subtitle', 'dummy'));

        $this->assertNull(PostWithMultipleSlugs::findBySlug('my-test-post', 'dummy'));
    }

    public function testFindBySlugOrFail()
    {
        $this->assertNotNull(Post::findBySlugOrFail('simple-post'));

        $this->assertNotNull(PostWithMultipleSlugs::findBySlugOrFail('my-test-post'));
        $this->assertNotNull(PostWithMultipleSlugs::findBySlugOrFail('my.subtitle'));
        $this->assertNotNull(PostWithMultipleSlugs::findBySlugOrFail('my-test-post', 'slug'));

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        PostWithMultipleSlugs::findBySlugOrFail('my-test-post', 'dummy');
    }

    public function testFindBySlugOrId()
    {
        $this->assertNotNull(Post::findBySlugOrId(2));
        $this->assertNotNull(Post::findBySlugOrId('simple-post'));

        $this->assertNotNull(PostWithMultipleSlugs::findBySlugOrId(1));
        $this->assertNotNull(PostWithMultipleSlugs::findBySlugOrId('my-test-post'));
        $this->assertNotNull(PostWithMultipleSlugs::findBySlugOrId('my.subtitle'));
        $this->assertNotNull(PostWithMultipleSlugs::findBySlugOrId('my-test-post', 'slug'));

        $this->assertNull(PostWithMultipleSlugs::findBySlugOrId(5));
        $this->assertNull(PostWithMultipleSlugs::findBySlugOrId('my-test-post', 'dummy'));
    }

    public function testFindBySlugOrIdOrFail()
    {
        $this->assertNotNull(Post::findBySlugOrIdOrFail(2));
        $this->assertNotNull(Post::findBySlugOrIdOrFail('simple-post'));

        $this->assertNotNull(PostWithMultipleSlugs::findBySlugOrIdOrFail(1));
        $this->assertNotNull(PostWithMultipleSlugs::findBySlugOrIdOrFail('my-test-post'));
        $this->assertNotNull(PostWithMultipleSlugs::findBySlugOrIdOrFail('my.subtitle'));
        $this->assertNotNull(PostWithMultipleSlugs::findBySlugOrIdOrFail('my-test-post', 'slug'));

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        PostWithMultipleSlugs::findBySlugOrIdOrFail(5);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        PostWithMultipleSlugs::findBySlugOrIdOrFail('my-test-post', 'dummy');
    }

    public function testDefaultFieldWasSet()
    {
        PostWithMultipleSlugsAndFindByDefault::create([
            'title' => 'My Test Post',
            'subtitle' => 'My Subtitle',
        ]);

        $this->assertNull(PostWithMultipleSlugsAndFindByDefault::findBySlug('my-test-post'));
        $this->assertNotNull(PostWithMultipleSlugsAndFindByDefault::findBySlug('my.subtitle'));
    }
}
