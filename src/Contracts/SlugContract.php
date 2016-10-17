<?php

namespace Cviebrock\EloquentSluggable\Contracts;

use Illuminate\Database\Eloquent\Model;

interface SlugContract
{
    /**
     * Generate a unique slug for a given string.
     *
     * @param \Illuminate\Database\Eloquent\Model|string $model
     * @param string $attribute
     * @param string $fromString
     * @param array $config
     * @return string
     */
    public static function createSlug($model, $attribute, $fromString, array $config = null);

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return $this
     */
    public function setModel(Model $model);

    /**
     * Slug the current model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param bool $force
     * @return bool
     */
    public function slug(Model $model, $force = false);

    /**
     * Build the slug for the given attribute of the current model.
     *
     * @param string $attribute
     * @param array $config
     * @param bool $force
     * @return null|string
     */
    public function buildSlug($attribute, array $config, $force = null);

    /**
     * Get the sluggable configuration for the current model,
     * including default values where not specified.
     *
     * @param array $overrides
     * @return array
     */
    public function getConfiguration(array $overrides = []);
}