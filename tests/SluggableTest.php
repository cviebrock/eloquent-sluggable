<?php namespace Cviebrock\EloquentSluggable\Test;

use Orchestra\Testbench\TestCase;


class SluggableTest extends TestCase {

  /**
   * Setup the test environment.
   */
	public function setUp()
	{
		parent::setUp();

		// Create an artisan object for calling migrations
		$artisan = $this->app->make('artisan');

		// Call migrations specific to our tests, e.g. to seed the db
		$artisan->call('migrate', array(
			'--database' => 'testbench',
			'--path'     => '../tests/migrations',
		));

		// Reset the default Sluggable settings since we might change them during tests and they are static.
		// (There is probably a better way to do this, but I can't think of it without using different models
		// for each possible configuration we want to test.)
		$this->settings(array(
			'build_from'      => 'title',
			'save_to'         => 'slug',
			'method'          => null,
			'separator'       => '-',
			'unique'          => true,
			'include_trashed' => false,
			'on_update'       => false,
			'reserved'        => null,
		));

	}

  /**
   * Define environment setup.
   *
   * @param  Illuminate\Foundation\Application    $app
   * @return void
   */
	protected function getEnvironmentSetUp($app)
	{
			// reset base path to point to our package's src directory
			$app['path.base'] = __DIR__ . '/../src';

			$app['config']->set('database.default', 'testbench');
			$app['config']->set('database.connections.testbench', array(
					'driver'   => 'sqlite',
					'database' => ':memory:',
					'prefix'   => '',
			));

	}


  /**
   * Get Sluggable package providers.
   *
   * @return array
   */
	protected function getPackageProviders()
	{
		return array('Cviebrock\EloquentSluggable\SluggableServiceProvider');
	}


  /**
   * Get Sluggable package aliases.
   *
   * @return array
   */
	protected function getPackageAliases()
	{
		return array(
			'Sluggable' => 'Cviebrock\EloquentSluggable\Facades\Sluggable'
		);
	}

	/**
	 * Helper function to create a post.
	 *
	 * @param  string $title
	 * @param  string $subtitle
	 * @param  array $settings
	 * @return Post
	 */
	protected function post($title, $subtitle=null)
	{
		return Post::create(array(
			'title'    => $title,
			'subtitle' => $subtitle,
		));
	}

	protected function settings($settings = array())
	{
		foreach($settings as $setting => $value)
		{
			Post::$sluggable[$setting] = $value;
		}
	}


	/**
	 * Test basic slugging functionality.
	 *
	 * @test
	 */
	public function testSimpleSlug()
	{
		$post = $this->post('My First Post');
		$this->assertEquals($post->slug, 'my-first-post');
	}

	/**
	 * Test that accented characters and other stuff is "fixed".
	 *
	 * @test
	 */
	public function testAccentedCharacters()
	{
		$post = $this->post('My Dinner With André & François');
		$this->assertEquals($post->slug, 'my-dinner-with-andre-francois');
	}

	/**
	 * Test that renaming the sluggable fields doesn't update the slug if on_update is false.
	 *
	 * @param  Post $post
	 * @test
	 */
	public function testRenameSlugWithoutUpdate()
	{
		$post = $this->post('My First Post');
		$post->title = 'A New Title';
		$post->save();
		$this->assertEquals($post->slug, 'my-first-post');
	}

	/**
	 * Test that renaming the sluggable fields does update the slug if on_update is true.
	 *
	 * @param  Post $post
	 * @test
	 */
	public function testRenameSlugWithUpdate()
	{
		$this->settings(array(
			'on_update' => true
		));
		$post = $this->post('My First Post');
		$post->title = 'A New Title';
		$post->save();
		$this->assertEquals($post->slug, 'a-new-title');
	}

	/**
	 * Test uniqueness of generated slugs.
	 *
	 * @test
	 */
	public function testUnique()
	{
		for ($i=0; $i < 20; $i++)
		{
			$post = $this->post('A post title');
			if ($i==0)
			{
				$this->assertEquals($post->slug, 'a-post-title');
			}
			else
			{
				$this->assertEquals($post->slug, 'a-post-title-'.$i);
			}
		}
	}

	/**
	 * Test that building a slug from multiple attributes.
	 *
	 * @test
	 */
	public function testMultipleSource()
	{
		$this->settings(array(
			'build_from' => array('title','subtitle')
		));
		$post = $this->post('A Post Title', 'A Subtitle');
		$this->assertEquals($post->slug, 'a-post-title-a-subtitle');
	}

	/**
	 * Test building a slug using a custom method.
	 *
	 * @test
	 */
	public function testCustomMethod()
	{
		$this->settings(array(
			'method' => function($string, $separator)
			{
				return strrev( \Str::slug($string,$separator) );
			}
		));
		$post = $this->post('A Post Title');
		$this->assertEquals($post->slug, 'eltit-tsop-a');
	}

	/**
	 * Test building a slug using the __toString method
	 *
	 * @test
	 */
	public function testToStringMethod()
	{
		$this->settings(array(
			'build_from' => null
		));
		$post = $this->post('A Post Title');
		$this->assertEquals($post->slug, 'a-post-title');
	}

	/**
	 * Test uniqueness after deletion.
	 *
	 * @test
	 */
	public function testUniqueAfterDelete()
	{
		$post1 = $this->post('A post title');
		$this->assertEquals($post1->slug, 'a-post-title');

		$post2 = $this->post('A post title');
		$this->assertEquals($post2->slug, 'a-post-title-1');

		$post2->delete();

		$post3 = $this->post('A post title');
		$this->assertEquals($post3->slug, 'a-post-title-1');
	}

	/**
	 * Test using a custom separator.
	 *
	 * @test
	 */
	public function testCustomSeparator()
	{
		$this->settings(array(
			'separator' => '.'
		));
		$post = $this->post('A post title');
		$this->assertEquals($post->slug, 'a.post.title');
	}

	/**
	 * Test using reserved word blocking.
	 *
	 * @test
	 */
	public function testReservedWord()
	{
		$this->settings(array(
			'reserved' => array('add')
		));
		$post = $this->post('Add');
		$this->assertEquals($post->slug, 'add-1');
	}

	/**
	 * Test when reverting to a shorter version of a similar slug (issue #5)
	 *
	 * @test
	 */
	public function testIssue5()
	{
		$this->settings(array(
			'on_update' => true
		));
		$post = $this->post('My first post');
		$this->assertEquals($post->slug, 'my-first-post');

		$post->title = 'My first post rocks';
		$post->save();
		$this->assertEquals($post->slug, 'my-first-post-rocks');

		$post->title = 'My first post';
		$post->save();
		$this->assertEquals($post->slug, 'my-first-post');
	}

	/**
	 * Test uniqueness with soft deletes when we ignore trashed models.
	 *
	 * @test
	 */
	public function testSoftDeletesWithoutTrashed()
	{
		PostSoft::$sluggable['include_trashed'] = false;
		$post1 = PostSoft::create(array(
			'title' => 'A Post Title'
		));
		$post1->delete();

		$post2 = PostSoft::create(array(
			'title' => 'A Post Title'
		));
		$this->assertEquals($post2->slug, 'a-post-title');
	}

	/**
	 * Test uniqueness with soft deletes when we include trashed models.
	 *
	 * @test
	 */
	public function testSoftDeletesWithTrashed()
	{
		PostSoft::$sluggable['include_trashed'] = true;

		$post1 = new PostSoft(array(
			'title' => 'A Post Title'
		));
		$post1->save();
		$this->assertEquals($post1->slug, 'a-post-title');

		$post1->delete();

		$post2 = new PostSoft(array(
			'title' => 'A Post Title'
		));
		$post2->save();
		$this->assertEquals($post2->slug, 'a-post-title-1');
	}

	/**
	 * Test ignoring current model when generating unique slugs (issue #16)
	 *
	 * @test
	 */
	public function testIssue16()
	{
		$this->settings(array(
			'unique' => true,
			'on_update' => true,
		));
		$post = $this->post('My first post');
		$this->assertEquals($post->slug, 'my-first-post');

		$post->dummy = 'Dummy data';
		$post->save();
		$this->assertEquals($post->slug, 'my-first-post');
	}

	/**
	 * Test ignoring current model when generating unique slugs (issue #16)
	 *
	 * @test
	 */
	public function testArdent()
	{
		$post = PostArdent::create(array(
			'title'    => 'My First Post'
		));
		$post->save();
		// \Sluggable::make($post, true);
		$this->assertEquals($post->slug, 'my-first-post');
	}

	/**
	 * Test model replication (issue #20)
	 *
	 * @test
	 */
	public function testIssue20()
	{
		$post = $this->post('My first post');
		$this->assertEquals($post->slug, 'my-first-post');

		$new_post = $post->replicate();
		\Sluggable::make($new_post,true);
		$this->assertEquals($new_post->slug, 'my-first-post-1');
	}

}
