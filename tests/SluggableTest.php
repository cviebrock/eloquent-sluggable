<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use Mockery;

class SluggableTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{

		Capsule::schema()->dropIfExists('posts');
		Capsule::schema()->create('posts', function($t) {
			$t->increments('id');
			$t->string('title', 255);
			$t->string('slug', 255);
		});

	}


	public function setUp()
	{

		Model::unguard();
		//
		Model::reguard();

	}


	public function tearDown()
	{

		Capsule::table('posts')->delete();
		Mockery::close();

	}


	protected function post($title)
	{
		$post = new Post(array('title'=>$title));
		dd($post);
		return $post;
	}


	public function testSimpleSlug()
	{
		$post = $this->post('My first post');
		$post->save();
		$this->assertEquals($post->slug, 'my-first-post');
	}

}
