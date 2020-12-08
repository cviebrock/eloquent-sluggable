# Eloquent-Sluggable

Easy creation of slugs for your Eloquent models in Laravel.

> **NOTE**: These instructions are for Laravel 7.0.  If you are using Laravel 5.8, please
> see the [previous version's docs](https://github.com/cviebrock/eloquent-sluggable/tree/4.8).

[![Build Status](https://travis-ci.org/cviebrock/eloquent-sluggable.svg?branch=master&format=flat)](https://travis-ci.org/cviebrock/eloquent-sluggable)
[![Total Downloads](https://poser.pugx.org/cviebrock/eloquent-sluggable/downloads?format=flat)](https://packagist.org/packages/cviebrock/eloquent-sluggable)
[![Latest Stable Version](https://poser.pugx.org/cviebrock/eloquent-sluggable/v/stable?format=flat)](https://packagist.org/packages/cviebrock/eloquent-sluggable)
[![Latest Unstable Version](https://poser.pugx.org/cviebrock/eloquent-sluggable/v/unstable?format=flat)](https://packagist.org/packages/cviebrock/eloquent-sluggable)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cviebrock/eloquent-sluggable/badges/quality-score.png?format=flat)](https://scrutinizer-ci.com/g/cviebrock/eloquent-sluggable)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0b966e13-6a6a-4d17-bcea-61037f04cfe7/mini.png)](https://insight.sensiolabs.com/projects/0b966e13-6a6a-4d17-bcea-61037f04cfe7)
[![License: MIT](https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square)](https://opensource.org/licenses/MIT)


* [Background: What is a slug](#background-what-is-a-slug)
* [Installation](#installation)
* [Updating your Eloquent Models](#updating-your-eloquent-models)
* [Usage](#usage)
* [The SlugService Class](#the-slugservice-class)
* [Events](#events)
* [Configuration](#configuration)
    * [includeTrashed](#includetrashed)
    * [maxLength](#maxlength)
    * [maxLengthKeepWords](#maxlengthkeepwords)
    * [method](#method)
    * [onUpdate](#onupdate)
    * [reserved](#reserved)
    * [separator](#separator)
    * [source](#source)
    * [unique](#unique)
    * [uniqueSuffix](#uniquesuffix)
* [Extending Sluggable](#extending-sluggable)
    * [customizeSlugEngine](#customizeslugengine)
    * [scopeWithUniqueSlugConstraints](#scopewithuniqueslugconstraints)
    * [scopeFindSimilarSlugs](#scopefindsimilarslugs)
* [SluggableScopeHelpers Trait](#sluggablescopehelpers-trait)
* [Route Model Binding](#route-model-binding)
* [Bugs, Suggestions, Contributions and Support](#bugs-suggestions-contributions-and-support)
* [Copyright and License](#copyright-and-license)


## Background: What is a slug?

A slug is a simplified version of a string, typically URL-friendly. The act of "slugging" 
a string usually involves converting it to one case, and removing any non-URL-friendly 
characters (spaces, accented letters, ampersands, etc.). The resulting string can 
then be used as an identifier for a particular resource.

For example, if you have a blog with posts, you could refer to each post via the ID:

    http://example.com/post/1
    http://example.com/post/2

... but that's not particularly friendly (especially for 
[SEO](http://en.wikipedia.org/wiki/Search_engine_optimization)). You probably would 
prefer to use the post's title in the URL, but that becomes a problem if your post 
is titled "My Dinner With André & François", because this is pretty ugly too:

    http://example.com/post/My+Dinner+With+Andr%C3%A9+%26+Fran%C3%A7ois

The solution is to create a slug for the title and use that instead. You might want 
to use Laravel's built-in `Str::slug()` method to convert that title into something 
friendlier:

    http://example.com/post/my-dinner-with-andre-francois

A URL like that will make users happier (it's readable, easier to type, etc.).

For more information, you might want to read 
[this](http://en.wikipedia.org/wiki/Slug_(web_publishing)#Slug) description on Wikipedia.

Slugs tend to be unique as well. So if you write another post with the same title, 
you'd want to distinguish between them somehow, typically with an incremental counter 
added to the end of the slug:

    http://example.com/post/my-dinner-with-andre-francois
    http://example.com/post/my-dinner-with-andre-francois-1
    http://example.com/post/my-dinner-with-andre-francois-2

This keeps the URLs unique.

The **Eloquent-Sluggable** package for Laravel aims to handle all of this for you 
automatically, with minimal configuration.


## Installation

Depending on your version of Laravel, you should install a different
version of the package.  **NOTE**: As of version 6.0, the package's 
version should match the Laravel version.

| Laravel Version | Package Version |
|:---------------:|:---------------:|
|       7.0       |      7.0.*      |
|       6.0       |      6.0.*      |
|       5.8       |      4.8.*      |
|       5.7       |   4.6.*|4.7.*   |
|       5.6       |      4.5.*      |
|       5.5       |   4.3.*|4.4.*   |
|       5.4       |      4.2.*      |

Older versions of Laravel can use older versions of the package, although they 
are no longer supported or maintained.  See [CHANGELOG.md](CHANGELOG.md) and
[UPGRADING.md](UPGRADING.md) for specifics, and be sure that you are reading 
the correct README.md for your version (Github displays the version in 
the _master_ branch by default, which might not be what you want).


1. Install the package via Composer:

    ```sh
    $ composer require cviebrock/eloquent-sluggable
    ```

    The package will automatically register its service provider.

2. Optionally, publish the configuration file if you want to change any defaults:

    ```sh
    php artisan vendor:publish --provider="Cviebrock\EloquentSluggable\ServiceProvider"
    ```


## Updating your Eloquent Models

Your models should use the Sluggable trait, which has an abstract method `sluggable()`
that you need to define.  This is where any model-specific configuration is set 
(see [Configuration](#configuration) below for details):

```php
use Cviebrock\EloquentSluggable\Sluggable;

class Post extends Model
{
    use Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
```

Of course, your model and database will need a column in which to store the slug. 
You can use `slug` or any other appropriate name you want; your configuration array
will determine to which field the data will be stored.  You will need to add the 
column manually via your own migration.

That's it ... your model is now "sluggable"!



## Usage

Saving a model is easy:

```php
$post = new Post([
    'title' => 'My Awesome Blog Post',
]);

$post->save();
```

And so is retrieving the slug:

```php
echo $post->slug;
```

Also note that if you are replicating your models using Eloquent's `replicate()` method, 
the package will automatically re-slug the model afterwards to ensure uniqueness.

```php
$post = new Post([
    'title' => 'My Awesome Blog Post',
]);

$post->save();
// $post->slug is "my-awesome-blog-post"

$newPost = $post->replicate();
// $newPost->slug is "my-awesome-blog-post-1"
```

Note that empty strings, non-strings or other "odd" source values will result in different slugs:

| Source Value | Resulting Slug        |
|--------------|-----------------------|
| string       | string                |
| empty string | _no slug will be set_ |
| `null`       | _no slug will be set_ |
| `0`          | `"0"`                 |
| `1`          | `"1"`                 |
| `false`      | `"0"`                 |
| `true`       | `"1"`                 |

(The above values would be subject to any unique or other checks as well.)

## The SlugService Class 

All the logic to generate slugs is handled
by the `\Cviebrock\EloquentSluggable\Services\SlugService` class.

Generally, you don't need to access this class directly, although there is one 
static method that can be used to generate a slug for a given string without actually
creating or saving an associated model.

```php
use \Cviebrock\EloquentSluggable\Services\SlugService;

$slug = SlugService::createSlug(Post::class, 'slug', 'My First Post');
```

This would be useful for Ajax-y controllers or the like, where you want to show a 
user what the unique slug _would_ be for a given test input, before actually creating
a model.  The first two arguments to the method are the model and slug field being
tested, and the third argument is the source string to use for testing the slug.

You can also pass an optional array of configuration values as the fourth argument.
These will take precedence over the normal configuration values for the slug field
being tested.  For example, if your model is configured to use unique slugs, but you 
want to generate the "base" version of a slug for some reason, you could do:

```php
$slug = SlugService::createSlug(Post::class, 'slug', 'My First Post', ['unique' => false]);
```



## Events

> **NOTE:** Events should be working but are not fully tested yet.
> [Please help me out!](#bugs-suggestions-and-contributions)

Sluggable models will fire two Eloquent model events: "slugging" and "slugged".
  
The "slugging" event is fired just before the slug is generated.  If the callback
from this event returns `false`, then the slugging is not performed. If anything
else is returned, including `null`, then the slugging will be performed.

The "slugged" event is fired just after a slug is generated.  It won't be called
in the case where the model doesn't need slugging (as determined by the `needsSlugging()`
method).

You can hook into either of these events just like any other Eloquent model event:

```php
Post::registerModelEvent('slugging', function($post) {
    if ($post->someCondition()) {
        // the model won't be slugged
        return false;
    }
});

Post::registerModelEvent('slugged', function($post) {
    Log::info('Post slugged: ' . $post->getSlug());
});
```



## Configuration

Configuration was designed to be as flexible as possible. You can set up defaults 
for all of your Eloquent models, and then override those settings for individual 
models.

By default, global configuration is set in the `config/sluggable.php` file. 
If a configuration isn't set, then the package defaults are used. 
Here is an example configuration, with all the default settings shown:

```php
return [
    'source'             => null,
    'maxLength'          => null,
    'maxLengthKeepWords' => true,
    'method'             => null,
    'separator'          => '-',
    'unique'             => true,
    'uniqueSuffix'       => null,
    'includeTrashed'     => false,
    'reserved'           => null,
    'onUpdate'           => false,
];
```

For individual models, configuration is handled in the `sluggable()` method that you
need to implement.  That method should return an indexed array where the keys represent
the fields where the slug value is stored and the values are the configuration for that
field.  This means you can create multiple slugs for the same model, based on different
source strings and with different configuration options.

```php
public function sluggable()
{
    return [
        'title-slug' => [
            'source' => 'title'
        ],
        'author-slug' => [
            'source' => ['author.lastname', 'author.firstname'],
            'separator' => '_'
        ],
    ];
}
```
  

### source

This is the field or array of fields from which to build the slug. Each `$model->field` 
is concatenated (with space separation) to build the sluggable string. These can be 
model attributes (i.e. fields in the database), relationship attributes, or custom getters.
 
To reference fields from related models, use dot-notation. For example, the 
slug for the following book will be generated from its author's name and the book's title:

```php
class Book extends Eloquent
{
    use Sluggable;

    protected $fillable = ['title'];

    public function sluggable() {
        return [
            'slug' => [
                'source' => ['author.name', 'title']
            ]
        ];
    }
    
    public function author() {
        return $this->belongsTo(Author::class);
    }
}
...
class Author extends Eloquent
{
    protected $fillable = ['name'];
}
```

An example using a custom getter:

```php
class Person extends Eloquent
{
    use Sluggable;

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'fullname'
            ]
        ];
    }

    public function getFullnameAttribute() {
        return $this->firstname . ' ' . $this->lastname;
    }
}
```

If `source` is empty, false or null, then the value of `$model->__toString()` is used
as the source for slug generation.

### maxLength

Setting this to a positive integer will ensure that your generated slugs are restricted 
to a maximum length (e.g. to ensure that they fit within your database fields). By default, 
this value is null and no limit is enforced.

Note: If `unique` is enabled (which it is by default), and you anticipate having 
several models with the same slug, then you should set this value to a few characters 
less than the length of your database field. The reason why is that the class will 
append "-1", "-2", "-3", etc., to subsequent models in order to maintain uniqueness. 
These incremental extensions aren't included in part of the `maxLength` calculation.

### maxLengthKeepWords

If you are truncating your slugs with the `maxLength` setting, than you probably
want to ensure that your slugs don't get truncated in the middle of a word.  For
example, if your source string is "My First Post", and your `maxLength` is 10,
the generated slug would end up being "my-first-p", which isn't ideal.

By default, the `maxLengthKeepWords` value is set to true which would trim the
partial words off the end of the slug, resulting in "my-first" instead of "my-first-p".

If you want to keep partial words, then set this configuration to false.

### method

Defines the method used to turn the sluggable string into a slug. There are three 
possible options for this configuration:

1. When `method` is null (the default setting), the package uses the default slugging
engine -- [cocur/slugify](https://github.com/cocur/slugify) -- to create the slug.

2. When `method` is a callable, then that function or class method is used. The function/method 
should expect two parameters: the string to process, and a separator string. 
For example, to use Laravel's `Str::slug`, you could do:

```php
'method' => ['Illuminate\\Support\\Str', 'slug'],
```

3. You can also define `method` as a closure (again, expecting two parameters):

```php
'method' => function ($string, $separator) {
    return strtolower(preg_replace('/[^a-z]+/i', $separator, $string));
},
```

Any other values for `method` will throw an exception.

For more complex slugging requirements, see [Extending Sluggable](#extending-sluggable) below.

### onUpdate

By default, updating a model will not try and generate a new slug value.  It is assumed
that once your slug is generated, you won't want it to change (this may be especially
true if you are using slugs for URLs and don't want to mess up your SEO mojo).

If you want to regenerate one or more of your model's slug fields, you can set those
fields to null or an empty string before the update:

```php
$post->slug = null;
$post->update(['title' => 'My New Title']);
```

If this is the behaviour you want every time you update a model, then set the `onUpdate`
option to true.

### separator

This defines the separator used when building a slug, and is passed to the `method` 
defined above. The default value is a hyphen.

### unique

This is a boolean defining whether slugs should be unique among all models of the given type. 
For example, if you have two blog posts and both are called "My Blog Post", then they 
will both sluggify to "my-blog-post" if `unique` is false. This could be a problem, e.g. 
if you use the slug in URLs.

By setting `unique` to true, then the second Post model will sluggify to "my-blog-post-1". 
If there is a third post with the same title, it will sluggify to "my-blog-post-2" 
and so on. Each subsequent model will get an incremental value appended to the end 
of the slug, ensuring uniqueness.

### uniqueSuffix

If you want to use a different way of identifying uniqueness (other than auto-incrementing
integers), you can set the `uniqueSuffix` configuration to a function or callable that 
generates the "unique" values for you.
 
The function should take three parameters: the base slug (i.e. the non-unique slug), the
separator string, and an `\Illuminate\Support\Collection` of all the other slug strings
that start with the same slug.  You can then do whatever you want to create a new suffix
that hasn't been used by any of the slugs in the collection.  For example, if you wanted
to use letters instead of numbers as a suffix, this is one way to achieve that:

```php
'uniqueSuffix' => function ($slug, $separator, Collection $list) {
    $size = count($list);

    return chr($size + 96);
}
```

### includeTrashed

Setting this to `true` will also check deleted models when trying to enforce uniqueness. 
This only affects Eloquent models that are using the 
[softDelete](http://laravel.com/docs/eloquent#soft-deleting) feature. Default is `false`, 
so soft-deleted models don't count when checking for uniqueness.

### reserved

An array of values that will never be allowed as slugs, e.g. to prevent collisions 
with existing routes or controller methods, etc.. This can be an array, or a closure 
that returns an array. Defaults to `null`: no reserved slug names.



## Short Configuration

The package supports a really short configuration syntax, if you are truly lazy:

```php
public function sluggable() {
    return [
        'slug'
    ];
}
```

This will use all the default options from `config/sluggable.php`, use the model's
`__toString()` method as the source, and store the slug in the `slug` field.



## Extending Sluggable

Sometimes the configuration options aren't sufficient for complex needs (e.g. maybe 
the uniqueness test needs to take other attributes into account).

In instances like these, the package offers hooks into the slugging workflow where you
can use your own functions, either on a per-model basis, or in your own trait that extends 
the package's trait.

### customizeSlugEngine

```php
/**
 * @param \Cocur\Slugify\Slugify $engine
 * @param string $attribute
 * @return \Cocur\Slugify\Slugify
 */
public function customizeSlugEngine(Slugify $engine, $attribute)
{
    ...
}
```

If this method exists on your model, the Slugify engine can be customized before slugging occurs.
This might be where you change the character mappings that are used, or alter language files, etc..

You can customize the engine on a per-model and per-attribute basis (maybe your model has 
two slug fields, and one of them needs customization).

Take a look at `tests/Models/PostWithCustomEngine.php` for an example.

### scopeWithUniqueSlugConstraints

```php
/**
 * @param \Illuminate\Database\Eloquent\Builder $query
 * @param \Illuminate\Database\Eloquent\Model $model
 * @param string $attribute
 * @param array $config
 * @param string $slug
 * @return \Illuminate\Database\Eloquent\Builder
 */
public function scopeWithUniqueSlugConstraints(Builder $query, Model $model, $attribute, $config, $slug)
{
    ...
}
```

If this scope exists on your model, then it will also be applied to the query used to determine 
if a given slug is unique.  The arguments passed to the scope are:

* `$model` -- the object being slugged
* `$attribute` -- the slug field being generated,
* `$config` -- the configuration array for the given model and attribute
* `$slug` -- the "base" slug (before any unique suffixes are applied)

Feel free to use these values anyway you like in your query scope.  As an example, look at 
`tests/Models/PostWithUniqueSlugConstraints.php` where the slug is generated for a post from it's title, but
the slug is scoped to the author.  So Bob can have a post with the same title as Pam's post, but both
will have the same slug.

### scopeFindSimilarSlugs

```php
/**
 * Query scope for finding "similar" slugs, used to determine uniqueness.
 *
 * @param \Illuminate\Database\Eloquent\Builder $query
 * @param string $attribute
 * @param array $config
 * @param string $slug
 * @return \Illuminate\Database\Eloquent\Builder
 */
public function scopeFindSimilarSlugs(Builder $query, $attribute, $config, $slug)
{
    ...
}
```

This is the default scope for finding "similar" slugs for a model.  Basically, the package looks for existing
slugs that are the same as the `$slug` argument, or that start with `$slug` plus the separator string.
The resulting collection is what is passed to the `uniqueSuffix` handler.

Generally, this query scope (which is defined in the Sluggable trait) should be left alone.
However, you are free to overload it in your models.



## SluggableScopeHelpers Trait

Adding the optional `SluggableScopeHelpers` trait to your model allows you to work with models
and their slugs.  For example:

```php
$post = Post::whereSlug($slugString)->get();

$post = Post::findBySlug($slugString);

$post = Post::findBySlugOrFail($slugString);
```

Because models can have more than one slug, this requires a bit more configuration.
See [SCOPE-HELPERS.md](SCOPE-HELPERS.md) for all the details.



## Route Model Binding

See [ROUTE-MODEL-BINDING.md](ROUTE-MODEL-BINDING.md) for details.



## Bugs, Suggestions, Contributions and Support

Thanks to [everyone](https://github.com/cviebrock/eloquent-taggable/graphs/contributors)
who has contributed to this project!  Special thanks to 
[JetBrains](https://www.jetbrains.com/?from=cviebrock/eloquent-sluggable) for their 
Open Source License Program ... and the excellent PHPStorm IDE, of course!

[![JetBrains](./.github/jetbrains.svg)](https://www.jetbrains.com/?from=cviebrock/eloquent-sluggable)

Please use [Github](https://github.com/cviebrock/eloquent-sluggable) for reporting bugs, 
and making comments or suggestions.
 
See [CONTRIBUTING.md](CONTRIBUTING.md) for how to contribute changes.



## Copyright and License

[eloquent-sluggable](https://github.com/cviebrock/eloquent-sluggable)
was written by [Colin Viebrock](http://viebrock.ca) and is released under the 
[MIT License](LICENSE.md).

Copyright (c) 2013 Colin Viebrock
