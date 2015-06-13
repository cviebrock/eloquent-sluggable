<?php namespace Cviebrock\EloquentSluggable;

use Illuminate\Routing\Router;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class SluggableRouter extends Router {

	/**
	 * Register a model binder for a wildcard. However, it's changed so that if the
	 * model implements the SluggableInterface, we'll use a different method.
	 *
	 * @param string $key
	 * @param string $class
	 * @param Closure|null $callback
	 * @throws NotFoundHttpException
	 */
	public function model($key, $class, Closure $callback = null) {
		$this->bind($key, function ($value) use ($class, $callback) {
			if (is_null($value)) {
				return null;
			}

			// For model binders, we attempt to get the model using the findBySlugOrId
			// method when the model uses a SluggableInterface, or by using the find
			// method on the model instance. If we cannot retrieve the models we'll
			// throw a not found exception otherwise we will return the instance.
			$model = new $class;

			if ($model instanceof SluggableInterface) {
				$model = $model->findBySlugOrId($value);
			} else {
				$model = $model->find($value);
			}

			if (!is_null($model)) {
				return $model;
			}

			// If a callback was supplied to the method we will call that to determine
			// what we should do when the model is not found. This just gives these
			// developer a little greater flexibility to decide what will happen.
			if ($callback instanceof Closure) {
				return call_user_func($callback, $value);
			}

			throw new NotFoundHttpException;
		});
	}
}
