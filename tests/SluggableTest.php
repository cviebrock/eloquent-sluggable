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


	protected function makePost($title, $subtitle=null)
	{
		$post = new Post;
		$post->title = $title;
		if ($subtitle)
		{
			$post->subtitle = $subtitle;
		}
		return $post;
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
		$this->assertEquals($post->slug, 'my-first-post');
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
		$post = $this->makePost('My First Post');
		$post->save();
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
		$post = $this->makePost('My First Post');
		$post->setSlugConfig(array(
			'on_update' => true
		));
		$post->save();
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
			$post = $this->makePost('A post title');
			$post->save();
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
	 * Test building a slug from multiple attributes.
	 *
	 * @test
	 */
	public function testMultipleSource()
	{
		$post =$this->makePost('A Post Title','A Subtitle');
		$post->setSlugConfig(array(
			'build_from' => array('title','subtitle')
		));
		$post->save();
		$this->assertEquals($post->slug, 'a-post-title-a-subtitle');
	}

	/**
	 * Test building a slug using a custom method.
	 *
	 * @test
	 */
	public function testCustomMethod()
	{
		$post =$this->makePost('A Post Title','A Subtitle');
		$post->setSlugConfig(array(
			'method' => function($string, $separator)
			{
				return strrev( \Str::slug($string,$separator) );
			}
		));
		$post->save();
		$this->assertEquals($post->slug, 'eltit-tsop-a');
	}

	/**
	 * Test building a slug using the __toString method
	 *
	 * @test
	 */
	public function testToStringMethod()
	{
		$post = $this->makePost('A Post Title');
		$post->setSlugConfig(array(
			'build_from' => null
		));
		$post->save();
		$this->assertEquals($post->slug, 'a-post-title');
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
		$this->assertEquals($post1->slug, 'a-post-title');

		$post2 = $this->makePost('A post title');
		$post2->save();
		$this->assertEquals($post2->slug, 'a-post-title-1');

		$post1->delete();

		$post3 = $this->makePost('A post title');
		$post3->save();
		$this->assertEquals($post3->slug, 'a-post-title');
	}

	/**
	 * Test using a custom separator.
	 *
	 * @test
	 */
	public function testCustomSeparator()
	{
		$post = $this->makePost('A post title');
		$post->setSlugConfig(array(
			'separator' => '.'
		));
		$post->save();
		$this->assertEquals($post->slug, 'a.post.title');
	}

	/**
	 * Test using reserved word blocking.
	 *
	 * @test
	 */
	public function testReservedWord()
	{
		$post = $this->makePost('Add');
		$post->setSlugConfig(array(
			'reserved' => array('add')
		));
		$post->save();
		$this->assertEquals($post->slug, 'add-1');
	}

	/**
	 * Test when reverting to a shorter version of a similar slug (issue #5)
	 *
	 * @test
	 */
	public function testIssue5()
	{
		$post = $this->makePost('My first post');
		$post->setSlugConfig(array(
			'on_update' => true
		));
		$post->save();
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
		$post1 = new PostSoft(array(
			'title' => 'A Post Title'
		));
		$post1->setSlugConfig(array(
			'include_trashed' => false
		));
		$post1->save();
		$this->assertEquals($post1->slug, 'a-post-title');

		$post1->delete();

		$post2 = new PostSoft(array(
			'title' => 'A Post Title'
		));
		$post2->setSlugConfig(array(
			'include_trashed' => false
		));
		$post2->save();
		$this->assertEquals($post2->slug, 'a-post-title');
	}

	/**
	 * Test uniqueness with soft deletes when we include trashed models.
	 *
	 * @test
	 */
	public function testSoftDeletesWithTrashed()
	{
		$post1 = new PostSoft(array(
			'title' => 'A Post Title'
		));
		$post1->setSlugConfig(array(
			'include_trashed' => true
		));
		$post1->save();
		$this->assertEquals($post1->slug, 'a-post-title');

		$post1->delete();

		$post2 = new PostSoft(array(
			'title' => 'A Post Title'
		));
		$post2->setSlugConfig(array(
			'include_trashed' => true
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
		$post = $this->makePost('My first post');
		$post->setSlugConfig(array(
			'unique'    => true,
			'on_update' => true,
		));
		$post->save();
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
		$post = new PostArdent(array(
			'title' => 'My First Post'
		));
		$post->save();
		$this->assertEquals($post->slug, 'my-first-post');
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
		$this->assertEquals($post1->slug, 'my-first-post');

		$post2 = $post1->replicate();
		$post2->slug();
		$this->assertEquals($post2->slug, 'my-first-post-1');
	}

}
