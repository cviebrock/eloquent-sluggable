<?php

namespace Cviebrock\EloquentSluggable\Tests\Classes;

use Illuminate\Support\Str;

class SluggableCustomMethod
{
    public static function slug($string, $separator = '-')
    {
        return strrev(Str::slug($string, $separator));
    }
}
