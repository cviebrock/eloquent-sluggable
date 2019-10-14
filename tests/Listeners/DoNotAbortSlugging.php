<?php
namespace Cviebrock\EloquentSluggable\Tests\Listeners;

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
    public function handle($model, $event)
    {
        return true;
    }
}
