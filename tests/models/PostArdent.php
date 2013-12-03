<?php namespace Cviebrock\EloquentSluggable\Test;

use LaravelBook\Ardent\Ardent;

class PostArdent extends Ardent {

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

	/**
	 * Rules for Ardent Validation
	 */

	public static $rules = array(
		'title' => 'required',
		'slug' => 'required|unique:posts'
	);

	/**
	 * Ardent pre-validate hook to handle slug generation
	 */

	public function beforeValidate()
	{
		\Sluggable::make($this,true);
	}


	public function __toString()
	{
		return $this->title;
	}

}