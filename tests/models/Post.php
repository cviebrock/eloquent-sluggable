<?php namespace Cviebrock\EloquentSluggable\Test;

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
		'method'          => null,
		'separator'       => '-',
		'unique'          => true,
		'include_trashed' => false,
		'on_update'       => false,
		'reserved'        => null,
		'use_cache'				=> true,
	);


	public function __toString()
	{
		return $this->title;
	}

}