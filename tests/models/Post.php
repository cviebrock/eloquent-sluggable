<?php namespace Cviebrock\EloquentSluggable\Test;

use Illuminate\Database\Eloquent\Model;


class Post extends Model {

  protected $table = 'posts';

  public $timestamps = false;

	protected $fillable = array('title');

	public static $sluggable = array(
		'build_from' => 'title',
		'save_to'    => 'slug',
	);

}