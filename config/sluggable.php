<?php

return [

    /*
     * What attributes do we use to build the slug?
     * This can be a single field, like "name" which will build a slug from:
     *
     *     $model->name;
     *
     * Or it can be an array of fields, like ("name", "company"), which builds a slug from:
     *
     *     $model->name . ' ' . $model->company;
     *
     * If you've defined custom getters in your model, you can use those too,
     * since Eloquent will call them when you request a custom attribute.
     *
     * Defaults to null, which uses the toString() method on your model.
     */
    'build_from' => null,

    /*
     * What field to we store the slug in?  Defaults to "slug".
     * You need to configure this when building the SQL for your database, e.g.:
     *
     * Schema::create('users', function($table)
     * {
     *    $table->string('slug');
     * });
     */
    'save_to' => 'slug',

    /*
     * The maximum length of a generated slug.  Defaults to "null", which means
     * no length restrictions are enforced.  Set it to a positive integer if you
     * want to make sure your slugs aren't too long.
     */
    'max_length' => null,

    /*
     * If left to "null", then use the cocur/slugify package to generate the slug
     * (with the separator defined below).
     *
     * Set this to a closure that accepts two parameters (string and separator)
     * to define a custom slugger.  e.g.:
     *
     *    'method' => function( $string, $sep ) {
     *       return preg_replace('/[^a-z]+/i', $sep, $string);
     *    },
     *
     * Otherwise, this will be treated as a callable to be used.  e.g.:
     *
     *    'method' => array('Str','slug'),
     */
    'method' => null,

    /*
     * Separator to use when generating slugs.  Defaults to a hyphen.
     */
    'separator' => '-',

    /*
     * Enforce uniqueness of slugs?  Defaults to true.
     * If a generated slug already exists, an incremental numeric
     * value will be appended to the end until a unique slug is found.  e.g.:
     *
     *     my-slug
     *     my-slug-1
     *     my-slug-2
     */
    'unique' => true,

    /*
     * Should we include the trashed items when generating a unique slug?
     * This only applies if the softDelete property is set for the Eloquent model.
     * If set to "false", then a new slug could duplicate one that exists on a trashed model.
     * If set to "true", then uniqueness is enforced across trashed and existing models.
     */
    'include_trashed' => false,

    /*
     * Whether to update the slug value when a model is being
     * re-saved (i.e. already exists).  Defaults to false, which
     * means slugs are not updated.
     */
    'on_update' => false,

    /*
     * An array of slug names that can never be used for this model,
     * e.g. to prevent collisions with existing routes or controller methods, etc..
     * Defaults to null (i.e. no reserved names).
     * Can be a static array, e.g.:
     *
     *    'reserved' => array('add', 'delete'),
     *
     * or a closure that returns an array of reserved names.
     * If using a closure, it will accept one parameter: the model itself, and should
     * return an array of reserved names, or null. e.g.
     *
     *    'reserved' => function( Model $model) {
     *      return $model->some_method_that_returns_an_array();
     *    }
     *
     * In the case of a slug that gets generated with one of these reserved names,
     * we will do:
     *
     *    $slug .= $seperator + "1"
     *
     * and continue from there.
     */
    'reserved' => null,

];
