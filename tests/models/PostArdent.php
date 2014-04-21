<?php

use LaravelBook\Ardent\Ardent;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class PostArdent extends Ardent implements SluggableInterface {

	use SluggableTrait;

  protected $table = 'posts';

  public $timestamps = false;

	protected $fillable = array('title','subtitle');

	protected $sluggable = array(
		'build_from'      => 'title',
		'save_to'         => 'slug',
	);

	/**
	 * Rules for Ardent Validation
	 */

	public static $rules = array(
		'title' => 'required',
		'slug'  => 'required|unique:posts'
	);

	/**
	 * Ardent pre-validate hook to handle slug generation
	 */

	public function beforeValidate()
	{
		$this->sluggify();
	}


	public function __toString()
	{
		return $this->title;
	}

}