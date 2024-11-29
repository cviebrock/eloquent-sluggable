<?php

namespace Cviebrock\EloquentSluggable\Tests\Listeners;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AbortSlugging.
 */
class AbortSlugging
{
    public function handle(Model $model, string $event): bool
    {
        return false;
    }
}
