<?php

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
			'--path'     => '../tests/database/migrations',
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

		// set up database configuration
		$app['config']->set('database.default', 'testbench');
		$app['config']->set('database.connections.testbench', array(
				'driver'   => 'sqlite',
				'database' => ':memory:',
				'prefix'   => '',
		));

		// set up caching configuration
		$app['config']->set('cache.driver', 'redis');
		$app['config']->set('cache.prefix', 'SluggableTest');

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


	protected function makePost($title, $subtitle=null, $slug=null)
	{
		$post = new Post;
		$post->title = $title;
		if ($subtitle)
		{
			$post->subtitle = $subtitle;
		}
		if ($slug)
		{
			$post->slug = $slug;
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
	 * @param  Post $post
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
		$this->assertEquals('a-new-title', $post->slug);
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
				$this->assertEquals('a-post-title', $post->slug);
			}
			else
			{
				$this->assertEquals('a-post-title-'.$i, $post->slug);
			}
		}
	}

	/**
	 * Test uniqueness of generated slugs using caching
	 *
	 * @test
	 */
	public function testUniqueWithCache()
	{
		// Manually flush the cache for tests
		\Cache::tags('sluggable')->flush();

		for ($i=0; $i < 20; $i++)
		{
			$post = $this->makePost('A post title');
			$post->setSlugConfig(array(
				'use_cache' => 10,
			));
			$post->save();
			if ($i==0)
			{
				$this->assertEquals('a-post-title', $post->slug);
			}
			else
			{
				$this->assertEquals('a-post-title-'.$i, $post->slug);
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
		$this->assertEquals('a-post-title-a-subtitle', $post->slug);
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
		$this->assertEquals('eltit-tsop-a', $post->slug);
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
		$post->setSlugConfig(array(
			'separator' => '.'
		));
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
		$post->setSlugConfig(array(
			'reserved' => array('add')
		));
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
		$post->setSlugConfig(array(
			'on_update' => true
		));
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
		$post1 = new PostSoft(array(
			'title' => 'A Post Title'
		));
		$post1->setSlugConfig(array(
			'include_trashed' => false
		));
		$post1->save();
		$this->assertEquals('a-post-title', $post1->slug);

		$post1->delete();

		$post2 = new PostSoft(array(
			'title' => 'A Post Title'
		));
		$post2->setSlugConfig(array(
			'include_trashed' => false
		));
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
		$post1 = new PostSoft(array(
			'title' => 'A Post Title'
		));
		$post1->setSlugConfig(array(
			'include_trashed' => true
		));
		$post1->save();
		$this->assertEquals('a-post-title', $post1->slug);

		$post1->delete();

		$post2 = new PostSoft(array(
			'title' => 'A Post Title'
		));
		$post2->setSlugConfig(array(
			'include_trashed' => true
		));
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

		$post->setSlugConfig(array(
			'unique'    => true,
			'on_update' => true,
		));
		$post->dummy = 'Dummy data';
		$post->save();
		$this->assertEquals('my-first-post', $post->slug);
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
	 * Test static findBySlug() method
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
	 * Test that we don't try and slug models that don't implement Sluggable
	 *
	 * @test
	 */
	public function testNonSluggableModels()
	{
		$post = new PostNotSluggable(array(
			'title' => 'My First Post'
		));
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
		$post->setSlugConfig(array(
			'max_length' => 10,
		));
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
		for ($i=0; $i < 20; $i++)
		{
			$post = $this->makePost('A post with a really long title');
			$post->setSlugConfig(array(
				'max_length' => 10,
			));
			$post->save();
			if ($i==0)
			{
				$this->assertEquals('a-post-wit', $post->slug);
			}
			elseif ($i<10)
			{
				$this->assertEquals('a-post-wit-'.$i, $post->slug);
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
		$post->setSlugConfig(array(
			'on_update' => true,
		));
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
		$post1 = new Post(array(
			'title' => 'A Post Title'
		));
		$post1->setSlugConfig(array(
			'include_trashed' => true
		));
		$post1->save();
		$this->assertEquals('a-post-title', $post1->slug);
	}

}
