<?php

namespace Cviebrock\EloquentSluggable;

use Cviebrock\EloquentSluggable\Sluggable;

trait PrimarySlug {

    /**
     * Primary slug column of this model.
     *
     * @return string
     */
    public function primarySlug(){
        if(isset($this->primarySlug)){
            return $this->primarySlug;
        }
        return 'slug';
    }
//
//    /**
//     * how to map the primary slug to the router
//     *
//     * @return string
//     */
//    public function getRouteKeyName() {
//        return $this->primarySlug();
//    }
//
//    /**
//     * Return the sluggable configuration array for this model.
//     *
//     * @return array
//     */
//    public function sluggable() {
//        $config = (method_exists(get_parent_class($this), 'getConfig')) ? parent::sluggable() : [];
//        return array_merge($config, [
//            $this->primarySlug() => $this->primarySlugConfig()
//        ]);
//    }

    /**
     * Query scope for finding a model by its slug.
     * @param $scope
     * @param $slug
     * @return mixed
     */
    public function scopeWhereSlug($scope, $slug) {
        return $scope->where($this->primarySlug(), $slug);
    }

    /**
     * Find a model by slug.
     * @param $slug
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function findBySlug($slug) {
        return static::whereSlug($slug)->first();
    }

    /**
     * Find a model by slug or fail.
     * @param $slug
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function findBySlugOrFail($slug) {
        return static::whereSlug($slug)->firstOrFail();
    }

    /**
     * Simple find by Id if it's numeric or slug if not. Fail if not found.
     * @param $slug
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection
     */
    public static function findBySlugOrIdOrFail($slug) {
        return static::findBySlug($slug) ?: static::findOrFail((int)$slug);
    }

    /**
     * Simple find by Id if it's numeric or slug if not.
     * @param $slug
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection|null
     */
    public static function findBySlugOrId($slug) {
        return  static::findBySlug($slug) ?: static::find($slug);
    }
}
