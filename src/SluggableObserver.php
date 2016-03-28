<?php namespace Cviebrock\EloquentSluggable;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;


class SluggableObserver
{

    /**
     * @var \Cviebrock\EloquentSluggable\Services\SlugService
     */
    private $slugService;

    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    private $events;

    /**
     * SluggableObserver constructor.
     *
     * @param \Cviebrock\EloquentSluggable\Services\SlugService $slugService
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function __construct(SlugService $slugService, Dispatcher $events)
    {
        $this->slugService = $slugService;
        $this->events = $events;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function saving(Model $model)
    {
        return $this->generateSlug($model, 'saving');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param $event
     */
    protected function generateSlug(Model $model, $event)
    {
        return $this->slugService->slug($model);
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
        return $this->events->until('eloquent.slugging: '.get_class($model), [$model, $event]);
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
        $this->events->fire('eloquent.slugged: '.get_class($model), [$model, $status]);
    }
}
