<?php

/* LARAVEL >= 4.2 */
if (trait_exists('Illuminate\Database\Eloquent\SoftDeletingTrait')) {

	class PostSoft extends Post {
		use Illuminate\Database\Eloquent\SoftDeletingTrait;
	}

/* LARAVEL <= 4.1 */
} else {

	class PostSoft extends Post {
		protected $softDelete = true;
	}

}
