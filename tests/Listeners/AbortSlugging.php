<?php namespace Cviebrock\EloquentSluggable\Tests\Listeners;

/**
 * Class AbortSlugging
 *
 * @package Tests\Listeners
 */
class AbortSlugging
{

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $event
     * @return bool
     */
    public function handle($model, $event)
    {
        echo "SLUGGING ABORTED!\n";

        return false;
    }
}
