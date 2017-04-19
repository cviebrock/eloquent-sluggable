<?php namespace Cviebrock\EloquentSluggable;

use Illuminate\Database\Eloquent\Builder;

trait FindBySlug
{
    public function scopeWhereOneOfMySlugs(Builder $query, $slug, $fields = [])
    {
        $fields = $this->slugFieldsGuard($query, $fields);

        return $query->where(function ($q) use ($slug, $fields) {
            foreach ($fields as $column) {
                $q->orWhere($column, $slug);
            }
        });
    }

    public static function findBySlug($slug, $fields = [])
    {
        return static::whereOneOfMySlugs($slug, $fields)->first();
    }

    public static function findBySlugOrFail($slug, $fields = [])
    {
        return static::whereOneOfMySlugs($slug, $fields)->firstOrFail();
    }

    public static function findBySlugOrId($slugOrId, $fields = [])
    {
        $found = is_numeric($slugOrId) ? static::find($slugOrId) : null;

        return ! is_null($found) ? $found: static::whereOneOfMySlugs($slugOrId, $fields)->first();
    }

    public static function findBySlugOrIdOrFail($slugOrId, $fields = [])
    {
        $found = is_numeric($slugOrId) ? static::find($slugOrId) : null;

        return ! is_null($found) ? $found : static::whereOneOfMySlugs($slugOrId, $fields)->firstOrFail();
    }

    protected function slugFieldsGuard(Builder $query, $fields = [])
    {
        $availableSlugFields = array_keys($query->getModel()->sluggable());
        $fields = (array) $fields;

        if (empty($fields)) {
            return empty($this->findBySlugDefault()) ? $availableSlugFields: $this->findBySlugDefault();
        }

        if (! empty($diff = array_diff($fields, $availableSlugFields))) {
            throw new \InvalidArgumentException('Invalid slugs field(s) ' . implode(', ', $diff));
        }

        return $fields;
    }

    protected function findBySlugDefault()
    {
        return [];
    }
}
