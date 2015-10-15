<?php

use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;


/**
 * Class SluggableTest
 */
class SluggableTest extends TestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        // Call migrations specific to our tests, e.g. to seed the db
        $this->artisan('migrate', [
          '--database' => 'testbench',
          '--path' => '../tests/database/migrations',
        ]);
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../src';

        // set up database configuration
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
          'driver' => 'sqlite',
          'database' => ':memory:',
          'prefix' => '',
        ]);
    }

    /**
     * Get Sluggable package providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['Cviebrock\EloquentSluggable\SluggableServiceProvider'];
    }

    /**
     * Helper to create "Post" model for tests.
     *
     * @param string $title
     * @param string|null $subtitle
     * @param string|null $slug
     * @return Post
     */
    protected function makePost($title, $subtitle = null, $slug = null)
    {
        $post = new Post;
        $post->title = $title;
        if ($subtitle) {
            $post->subtitle = $subtitle;
        }
        if ($slug) {
            $post->slug = $slug;
        }

        return $post;
    }

    /**
     * Helper to create "Author" model for tests.
     *
     * @param $name
     * @return Author
     */
    protected function makeAuthor($name)
    {
        $author = new Author();
        $author->name = $name;
        $author->save();

        return $author;
    }

    /**
     * Test basic slugging functionality.
     *
     * @test
     */
    public function testSimpleSlug()
    {
        $post = $this->makePost('My First Post');
        $post->save();
        $this->assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test that accented characters and other stuff is "fixed".
     *
     * @test
     */
    public function testAccentedCharacters()
    {
        $post = $this->makePost('My Dinner With André & François');
        $post->save();
        $this->assertEquals('my-dinner-with-andre-francois', $post->slug);
    }

    /**
     * Test that renaming the sluggable fields doesn't update the slug if on_update is false.
     *
     * @test
     */
    public function testRenameSlugWithoutUpdate()
    {
        $post = $this->makePost('My First Post');
        $post->save();
        $post->title = 'A New Title';
        $post->save();
        $this->assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test that renaming the sluggable fields does update the slug if on_update is true.
     *
     * @test
     */
    public function testRenameSlugWithUpdate()
    {
        $post = $this->makePost('My First Post');
        $post->setSlugConfig([
          'on_update' => true
        ]);
        $post->save();
        $post->title = 'A New Title';
        $post->save();
        $this->assertEquals('a-new-title', $post->slug);
    }

    /**
     * Test uniqueness of generated slugs.
     *
     * @test
     */
    public function testUnique()
    {
        for ($i = 0; $i < 20; $i++) {
            $post = $this->makePost('A post title');
            $post->save();
            if ($i == 0) {
                $this->assertEquals('a-post-title', $post->slug);
            } else {
                $this->assertEquals('a-post-title-' . $i, $post->slug);
            }
        }
    }

    /**
     * Test building a slug from multiple attributes.
     *
     * @test
     */
    public function testMultipleSource()
    {
        $post = $this->makePost('A Post Title', 'A Subtitle');
        $post->setSlugConfig([
          'build_from' => ['title', 'subtitle']
        ]);
        $post->save();
        $this->assertEquals('a-post-title-a-subtitle', $post->slug);
    }

    /**
     * Test building a slug using a custom method.
     *
     * @test
     */
    public function testCustomMethod()
    {
        $post = $this->makePost('A Post Title', 'A Subtitle');
        $post->setSlugConfig([
          'method' => function ($string, $separator) {
              return strrev(Str::slug($string, $separator));
          }
        ]);
        $post->save();
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
            $post = new PostSuffix;
            $post->title = 'A Post Title';
            $post->subtitle = 'A Subtitle';
            $post->save();

            if ($i === 0) {
                $this->assertEquals('a-post-title', $post->slug);
            } else {
                $this->assertEquals('a-post-title-' . chr($i + 96),
                  $post->slug);
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
        $post = $this->makePost('A Post Title');
        $post->setSlugConfig([
          'build_from' => null
        ]);
        $post->save();
        $this->assertEquals('a-post-title', $post->slug);
    }

    /**
     * Test uniqueness after deletion.
     *
     * @test
     */
    public function testUniqueAfterDelete()
    {
        $post1 = $this->makePost('A post title');
        $post1->save();
        $this->assertEquals('a-post-title', $post1->slug);

        $post2 = $this->makePost('A post title');
        $post2->save();
        $this->assertEquals('a-post-title-1', $post2->slug);

        $post1->delete();

        $post3 = $this->makePost('A post title');
        $post3->save();
        $this->assertEquals('a-post-title', $post3->slug);
    }

    /**
     * Test using a custom separator.
     *
     * @test
     */
    public function testCustomSeparator()
    {
        $post = $this->makePost('A post title');
        $post->setSlugConfig([
          'separator' => '.'
        ]);
        $post->save();
        $this->assertEquals('a.post.title', $post->slug);
    }

    /**
     * Test using reserved word blocking.
     *
     * @test
     */
    public function testReservedWord()
    {
        $post = $this->makePost('Add');
        $post->setSlugConfig([
          'reserved' => ['add']
        ]);
        $post->save();
        $this->assertEquals('add-1', $post->slug);
    }

    /**
     * Test when reverting to a shorter version of a similar slug (issue #5)
     *
     * @test
     */
    public function testIssue5()
    {
        $post = $this->makePost('My first post');
        $post->setSlugConfig([
          'on_update' => true
        ]);
        $post->save();
        $this->assertEquals('my-first-post', $post->slug);

        $post->title = 'My first post rocks';
        $post->save();
        $this->assertEquals('my-first-post-rocks', $post->slug);

        $post->title = 'My first post';
        $post->save();
        $this->assertEquals('my-first-post', $post->slug);
    }

    /**
     * Test uniqueness with soft deletes when we ignore trashed models.
     *
     * @test
     */
    public function testSoftDeletesWithoutTrashed()
    {
        $post1 = new PostSoft([
          'title' => 'A Post Title'
        ]);
        $post1->setSlugConfig([
          'include_trashed' => false
        ]);
        $post1->save();
        $this->assertEquals('a-post-title', $post1->slug);

        $post1->delete();

        $post2 = new PostSoft([
          'title' => 'A Post Title'
        ]);
        $post2->setSlugConfig([
          'include_trashed' => false
        ]);
        $post2->save();
        $this->assertEquals('a-post-title', $post2->slug);
    }

    /**
     * Test uniqueness with soft deletes when we include trashed models.
     *
     * @test
     */
    public function testSoftDeletesWithTrashed()
    {
        $post1 = new PostSoft([
          'title' => 'A Post Title'
        ]);
        $post1->setSlugConfig([
          'include_trashed' => true
        ]);
        $post1->save();
        $this->assertEquals('a-post-title', $post1->slug);

        $post1->delete();

        $post2 = new PostSoft([
          'title' => 'A Post Title'
        ]);
        $post2->setSlugConfig([
          'include_trashed' => true
        ]);
        $post2->save();
        $this->assertEquals('a-post-title-1', $post2->slug);
    }

    /**
     * Test ignoring current model when generating unique slugs (issue #16)
     *
     * @test
     */
    public function testIssue16()
    {
        $post = $this->makePost('My first post');
        $post->save();
        $this->assertEquals('my-first-post', $post->slug);

        $post->setSlugConfig([
          'unique' => true,
          'on_update' => true,
        ]);
        $post->dummy = 'Dummy data';
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
        $post1 = $this->makePost('My first post');
        $post1->save();
        $this->assertEquals('my-first-post', $post1->slug);

        $post2 = $post1->replicate();
        $post2->resluggify();
        $this->assertEquals('my-first-post-1', $post2->slug);
    }

    /**
     * Test findBySlug() scope method
     *
     * @test
     */
    public function testFindBySlug()
    {
        $post1 = $this->makePost('My first post');
        $post1->save();

        $post2 = $this->makePost('My second post');
        $post2->save();

        $post3 = $this->makePost('My third post');
        $post3->save();

        $post = Post::findBySlug('my-second-post');

        $this->assertEquals($post2->id, $post->id);
    }

    /**
     * Test findBySlugOrFail() scope method
     *
     * @test
     */
    public function testFindBySlugOrFail()
    {
        $post1 = $this->makePost('My first post');
        $post1->save();

        $post2 = $this->makePost('My second post');
        $post2->save();

        $post3 = $this->makePost('My third post');
        $post3->save();

        $post = Post::findBySlugOrFail('my-second-post');
        $this->assertEquals($post2->id, $post->id);

        try {
            Post::findBySlugOrFail('my-fourth-post');
            $this->fail('Not found exception not raised');
        } catch (Exception $e) {
            $this->assertInstanceOf('Illuminate\Database\Eloquent\ModelNotFoundException',
              $e);
        }
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
        $post = $this->makePost('A post with a really long title');
        $post->setSlugConfig([
          'max_length' => 10,
        ]);
        $post->save();
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
            $post = $this->makePost('A post with a really long title');
            $post->setSlugConfig([
              'max_length' => 10,
            ]);
            $post->save();
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
        $post = $this->makePost('My first post', null, 'custom-slug');
        $post->save();
        $this->assertEquals('custom-slug', $post->slug);
    }

    /**
     * Test that models aren't *re*slugged if the slug field is defined (issue #32)
     *
     * @test
     */
    public function testDoesNotNeedSluggingWithUpdateWhenSlugIsSet()
    {
        $post = $this->makePost('My first post', null, 'custom-slug');
        $post->setSlugConfig([
          'on_update' => true,
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
     * Test that include_trashed is ignored if the model doesn't use the softDelete trait.
     *
     * @test
     */
    public function testSoftDeletesWithNonSoftDeleteModel()
    {
        $post1 = new Post([
          'title' => 'A Post Title'
        ]);
        $post1->setSlugConfig([
          'include_trashed' => true
        ]);
        $post1->save();
        $this->assertEquals('a-post-title', $post1->slug);
    }

    /**
     * Test findBySlug() scope method
     *
     * @test
     */
    public function testFindBySlugOrId()
    {
        $post1 = $this->makePost('My first post');
        $post1->save();

        $post2 = $this->makePost('My second post');
        $post2->save();

        $post3 = $this->makePost('My third post');
        $post3->save();
        
        $post4 = $this->makePost(5);
        $post4->save();

        $post = Post::findBySlugOrId('my-second-post');

        $this->assertEquals($post2->id, $post->id);

        $post = Post::findBySlugOrId(3);

        $this->assertEquals($post3->id, $post->id);
        
        $post = Post::findBySlugOrId(5);

        $this->assertEquals($post4->id, $post->id);
    }

    /**
     * Test findBySlugOrFail() scope method
     *
     * @test
     */
    public function testFindBySlugOrIdOrFail()
    {
        $post1 = $this->makePost('My first post');
        $post1->save();

        $post2 = $this->makePost('My second post');
        $post2->save();

        $post3 = $this->makePost('My third post');
        $post3->save();
        
        $post4 = $this->makePost(5);
        $post4->save();

        $post = Post::findBySlugOrIdOrFail('my-second-post');
        $this->assertEquals($post2->id, $post->id);

        $post = Post::findBySlugOrIdOrFail(3);
        $this->assertEquals($post3->id, $post->id);
        
        $post = Post::findBySlugOrIdOrFail(5);
		$this->assertEquals($post4->id, $post->id);

        try {
            Post::findBySlugOrFail('my-fourth-post');
            $this->fail('Not found exception not raised');
        } catch (Exception $e) {
            $this->assertInstanceOf('Illuminate\Database\Eloquent\ModelNotFoundException',
              $e);
        }
    }

    /**
     * Test findBySlug returns null when no record found
     *
     * @test
     */
    public function testFindBySlugReturnsNullForNoRecord()
    {
        $this->assertNull(Post::findBySlug('not a real record'));
    }

    /**
     * Test Non static call for findBySlug is working
     *
     * @test
     */
    public function testNonStaticCallOfFindBySlug()
    {
        $post1 = $this->makePost('My first post');
        $post1->save();

        $post = Post::first();
        $resultId = $post->findBySlug('my-first-post')->id;

        $this->assertEquals($post1->id, $resultId);
    }

    /**
     * Test that the unique suffix does not increment when the title
     * is unchanged in models where 'on_update' is set to true.
     *
     * @test
     */
    public function testUniqueSuffixDoesNotIncrement()
    {
        $post1 = new Post(['title' => 'My Test Post']);
        $post1->save(); // my-test-post

        $post2 = new Post(['title' => 'My Test Post']);
        $post2->save(); // my-test-post-1

        $post2->setSlugConfig(['on_update' => true]);

        $post2->dummy = 'Some update happens, and the unique value increments...';
        $post2->save(); // before fix, my-test-post-2

        $this->assertEquals('my-test-post-1', $post2->slug); // previously failed
    }

    /**
     * Test generating slug from related model field.
     *
     * @test
     */
    public function testSlugFromRelatedModel()
    {
        $author = $this->makeAuthor('Arthur Conan Doyle');
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
        $post = new PostWithRelation([
          'title' => 'First'
        ]);
        $post->save();
        $this->assertEquals('first', $post->slug);
    }

    /**
     * Test that an empty slug source creates a null slug.
     *
     * @test
     */
    public function testEmptySourceGeneratesEmptySlug()
    {
        $post = new Post(['title' => 'My Test Post']);
        $post->setSlugConfig([
          'build_from' => 'subtitle'
        ]);

        $post->save();
        $this->assertEquals(null, $post->slug);
    }

    /**
     * Test using custom Slugify rules.
     *
     * @test
     */
    public function testCustomEngineRules()
    {
        $post = new PostCustomEngine([
          'title' => 'The quick brown fox jumps over the lazy dog'
        ]);
        $post->save();
        $this->assertEquals('tha-qaack-brawn-fax-jamps-avar-tha-lazy-dag', $post->slug);
    }

    /**
     * Test that the "slugging" event is fired.
     *
     * @test
     */
    public function testSluggingEvent()
    {
        // test event to modify the model before slugging
        Post::registerModelEvent('slugging', function($post) {
            $post->title = 'Modified by event';
        });

        $post = new Post(['title' => 'My Test Post']);
        $post->save();
        $this->assertEquals('modified-by-event', $post->slug);
    }

    /**
     * Test that the "slugging" event can be cancelled.
     *
     * @test
     */
    public function testCancelSluggingEvent()
    {
        // test event to cancel the slugging
        Post::registerModelEvent('slugging', function($post) {
            return false;
        });

        $post = new Post(['title' => 'My Test Post']);
        $post->save();
        $this->assertEquals(null, $post->slug);
    }

    /**
     * Test that the "slugged" event is fired.
     *
     * @test
     */
    public function testSluggedEvent()
    {
        Post::registerModelEvent('slugged', function($post) {
            $post->subtitle = 'I have been slugged!';
        });

        $post = new Post(['title' => 'My Test Post']);
        $post->save();
        $this->assertEquals('my-test-post', $post->slug);
        $this->assertEquals('I have been slugged!', $post->subtitle);
    }

    /**
     * Test that we can generate a slug statically.
     * 
     * @test
     */
    public function testStaticSlugGenerator()
    {
        $this->assertEquals('my-test-post', Post::createSlug('My Test Post'));
    }

    /**
     * Test that we generate unique slugs in a static context.
     * 
     * @test
     */
    public function testStaticSlugGeneratorWhenEntriesExist()
    {
        $post = Post::create(['title' => 'My Test Post']);

        $this->assertEquals('my-test-post', $post->slug);
        $this->assertEquals('my-test-post-1', Post::createSlug('My Test Post'));
    }
}
