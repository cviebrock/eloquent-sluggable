<?php

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;


class Post extends Model implements SluggableInterface {

	use SluggableTrait;

  protected $table = 'posts';

  public $timestamps = false;

	protected $fillable = array('title','subtitle');

	protected $sluggable = array(
		'build_from'      => 'title',
		'save_to'         => 'slug',
	);


	/**
	 * Helper to set slug options for tests.
	 *
	 * @param array $array Array of new slug options
	 */
	public function setSlugConfig($array)
	{
		foreach($array as $key=>$value)
		{
			$this->sluggable[$key] = $value;
		}
	}


	public function __toString()
	{
		return $this->title;
	}

}