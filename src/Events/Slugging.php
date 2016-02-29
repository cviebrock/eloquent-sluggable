<?php namespace Cviebrock\EloquentSluggable\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;


class Slugging
{

    use SerializesModels;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    /**
     * Slugging constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(Model $model)
    {

        $this->model = $model;
    }

}
