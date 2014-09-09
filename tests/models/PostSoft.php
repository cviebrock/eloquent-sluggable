<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class PostSoft extends Post {

    use SoftDeletingTrait;
}
