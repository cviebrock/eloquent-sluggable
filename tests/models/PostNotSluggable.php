<?php

use Illuminate\Database\Eloquent\Model;


class PostNotSluggable extends Model {

  protected $table = 'posts';

  public $timestamps = false;

	protected $fillable = array('title','subtitle');

}