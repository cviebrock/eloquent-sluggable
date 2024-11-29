<?php

namespace Cviebrock\EloquentSluggable;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SluggableObserver.
 */
class SluggableObserver
{
    /** @var string */
    public const SAVING = 'saving';

    /** @var string */
    public const SAVED = 'saved';

    /**
     * @var SlugService
     */
    private $slugService;

    /**
     * @var Dispatcher
     */
    private $events;

    /**
     * SluggableObserver constructor.
     */
    public function __construct(SlugService $slugService, Dispatcher $events)
    {
        $this->slugService = $slugService;
        $this->events = $events;
    }

    /**
     * @return bool|void
     */
    public function saving(Model $model)
    {
        // @phpstan-ignore-next-line
        if ($model->sluggableEvent() !== self::SAVING) {
            return;
        }

        $this->generateSlug($model, 'saving');
    }

    /**
     * @return bool|void
     */
    public function saved(Model $model)
    {
        // @phpstan-ignore-next-line
        if ($model->sluggableEvent() !== self::SAVED) {
            return;
        }
        if ($this->generateSlug($model, 'saved')) {
            return $model->saveQuietly();
        }
    }

    protected function generateSlug(Model $model, string $event): bool
    {
        // If the "slugging" event returns false, abort
        if ($this->fireSluggingEvent($model, $event) === false) {
            return false;
        }
        $wasSlugged = $this->slugService->slug($model);

        $this->fireSluggedEvent($model, $wasSlugged);

        return $wasSlugged;
    }

    /**
     * Fire the namespaced validating event.
     */
    protected function fireSluggingEvent(Model $model, string $event): ?bool
    {
        return $this->events->until('eloquent.slugging: ' . get_class($model), [$model, $event]);
    }

    /**
     * Fire the namespaced post-validation event.
     */
    protected function fireSluggedEvent(Model $model, string $status): void
    {
        $this->events->dispatch('eloquent.slugged: ' . get_class($model), [$model, $status]);
    }
}
