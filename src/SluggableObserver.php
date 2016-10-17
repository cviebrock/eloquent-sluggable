<?php namespace Cviebrock\EloquentSluggable;

use Cviebrock\EloquentSluggable\Contracts\SlugContract;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SluggableObserver
 *
 * @package Cviebrock\EloquentSluggable
 */
class SluggableObserver
{

    /**
     * @var SlugContract
     */
    private $slugService;

    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    private $events;

    /**
     * SluggableObserver constructor.
     *
     * @param SlugContract $slugService
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function __construct(SlugContract $slugService, Dispatcher $events)
    {
        $this->slugService = $slugService;
        $this->events = $events;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return boolean|null
     */
    public function saving(Model $model)
    {
        return $this->generateSlug($model, 'saving');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $event
     * @return boolean|null
     */
    protected function generateSlug(Model $model, $event)
    {
        // If the "slugging" event returns a value, abort
        if ($this->fireSluggingEvent($model, $event) !== null) {
            return;
        }
        $wasSlugged = $this->slugService->slug($model);

        $this->fireSluggedEvent($model, $wasSlugged);
    }

    /**
     * Fire the namespaced validating event.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  string $event
     * @return mixed
     */
    protected function fireSluggingEvent(Model $model, $event)
    {
        return $this->events->until('eloquent.slugging: ' . get_class($model), [$model, $event]);
    }

    /**
     * Fire the namespaced post-validation event.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  string $status
     * @return void
     */
    protected function fireSluggedEvent(Model $model, $status)
    {
        $this->events->fire('eloquent.slugged: ' . get_class($model), [$model, $status]);
    }
}
