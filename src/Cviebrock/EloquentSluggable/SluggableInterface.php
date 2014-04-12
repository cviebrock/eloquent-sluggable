<?php

namespace Cviebrock\EloquentSluggable;


interface SluggableInterface {

	public function getSlug();

	public function slug($force=false);

	public function reslug();

}