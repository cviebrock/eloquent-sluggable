<?php namespace Tests\Listeners;

use Cviebrock\EloquentSluggable\Events\Slugging;


class AbortSlugging
{

    public function handle(Slugging $event)
    {
        echo 'foo';
        return false;
    }
}
