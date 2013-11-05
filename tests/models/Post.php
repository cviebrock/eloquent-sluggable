<?php namespace Cviebrock\EloquentSluggable\Test;

use Illuminate\Database\Eloquent\Model;


class Post extends Model {

  protected $table = 'posts';

  public $timestamps = false;

	protected $fillable = array('title','subtitle');

	public static $sluggable = array(
		'build_from'      => 'title',
		'save_to'         => 'slug',
		'method'          => null,
		'separator'       => '-',
		'unique'          => true,
		'include_trashed' => false,
		'on_update'       => false,
		'reserved'        => null,
	);


	public function __toString()
	{
		return $this->title;
	}

}