<?php

namespace Cviebrock\EloquentSluggable\Tests\Listeners;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AbortSlugging.
 */
class DoNotAbortSlugging
{
    public function handle(Model $model, string $event): bool
    {
        return true;
    }
}
