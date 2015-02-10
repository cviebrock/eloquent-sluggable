<?php

use Illuminate\Database\Eloquent\SoftDeletes;

class PostSoft extends Post {
		use SoftDeletes;
}
