<?php namespace Cviebrock\EloquentSluggable\Tests\Listeners;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AbortSlugging
 *
 * @package Tests\Listeners
 */
class DoNotAbortSlugging
{

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $event
     * @return bool
     */
    public function handle(Model $model, string $event): bool
    {
        return true;
    }
}
