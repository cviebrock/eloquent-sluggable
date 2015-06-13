# Eloquent-Sluggable

Easy creation of slugs for your Eloquent models in Laravel 5.

[![Build Status](https://travis-ci.org/cviebrock/eloquent-sluggable.svg)](https://travis-ci.org/cviebrock/eloquent-sluggable)
[![Total Downloads](https://poser.pugx.org/cviebrock/eloquent-sluggable/downloads.png)](https://packagist.org/packages/cviebrock/eloquent-sluggable)
[![Latest Stable Version](https://poser.pugx.org/cviebrock/eloquent-sluggable/v/stable.png)](https://packagist.org/packages/cviebrock/eloquent-sluggable)
[![Latest Stable Version](https://poser.pugx.org/cviebrock/eloquent-sluggable/v/unstable.png)](https://packagist.org/packages/cviebrock/eloquent-sluggable)

* [Background](#background)
* [Installation and Requirements](#installation)
* [Updating your Eloquent Models](#eloquent)
* [Using the Class](#usage)
* [Configuration](#config)
* [Route-model Binding](#route-model)
* [Extending Sluggable](#extending)
* [Bugs, Suggestions and Contributions](#bugs)
* [Copyright and License](#copyright)


> **NOTE** If you are using Laravel 4, then use the `2.x` branch or tagged `2.*` releases. Currently, `master` is only tested against Laravel 5.*.


<a name="background"></a>
## Background: What is a slug?

A slug is a simplified version of a string, typically URL-friendly. The act of "slugging" a string usually involves converting it to one case, and removing any non-URL-friendly characters (spaces, accented letters, ampersands, etc.). The resulting string can then be used as an indentifier for a particular resource.

For example, I have a blog with posts. I could refer to each post via the ID:

	http://example.com/post/1
	http://example.com/post/2

... but that's not particularly friendly (especially for [SEO](http://en.wikipedia.org/wiki/Search_engine_optimization)). You probably would prefer to use the post's title in the URL, but that becomes a problem if your post is titled "My Dinner With André & François", because this is pretty ugly too:

	http://example.com/post/My+Dinner+With+Andr%C3%A9+%26+Fran%C3%A7ois

The solution is to create a slug for the title and use that instead. You might want to use Laravel's built-in `Str::slug()` method to convert that title into something friendlier:

	http://example.com/post/my-dinner-with-andre-francois

A URL like that will make users happier (readable, easier to type, etc.).

For more information, you might want to read [this](http://en.wikipedia.org/wiki/Slug_(web_publishing)#Slug) description on Wikipedia.

Slugs tend to be unique as well. So if I wrote another post with the same title, I'd want to distinguish between them somehow, typically with an incremental counter added to the end of the slug:

	http://example.com/post/my-dinner-with-andre-francois
	http://example.com/post/my-dinner-with-andre-francois-1
	http://example.com/post/my-dinner-with-andre-francois-2

This keeps URLs unique.

The **Eloquent-Sluggable** package for Laravel 5 will handle all of this for you automatically, with minimal configuration at the start.


<a name="installation"></a>
## Installation and Requirements

First, you'll need to require the package with Composer:

```bash
$ composer require cviebrock/eloquent-sluggable ">=3.0.0-beta"
```

> **NOTE**: Eloquent-Sluggable now uses traits, so you will need to be running PHP 5.4 or higher. If you are still using 5.3, then use the "1.*" version and follow the instructions in that version's README.md file.

Aftwards, run `composer update` from your command line.

Then, update `config/app.php` by adding an entry for the service provider.

```php
'providers' => [
    // ...
    'Cviebrock\EloquentSluggable\SluggableServiceProvider',
];
```

Finally, from the command line again, run `php artisan vendor:publish` to publish the default configuration file.

<a name="eloquent"></a>
## Updating your Eloquent Models

Your models should implement Sluggable's interface and use it's trait. You should also define a protected property `$sluggable` with any model-specific configurations (see [Configuration](#config) below for details):

```php
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class Post extends Model implements SluggableInterface
{
	use SluggableTrait;

	protected $sluggable = [
		'build_from' => 'title',
		'save_to'    => 'slug',
	];

}
```

Of course, your database will need a column in which to store the slug. You can do this manually, or use the built-in artisan command to create a migration for you. For example:

```
php artisan sluggable:table posts
```

Running that command will create a migration that adds a column named "slug" to your posts table. If you want to use a different name for the slug column, you can provide that as a second argument:

```
php artisan sluggable:table posts slug_column
```

Be sure to set your model's `save_to` configuration to match the column name.

That's it ... your model is now "sluggable"!



<a name="usage"></a>
## Using the Class

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

// or, if you don't know the name of the slug attribute:
echo $post->getSlug();
```

Also note that if you are replicating your models using Eloquent's `replicate()` method, then you will need to explicity tell the package to force a re-slugging of the model afterwards to ensure uniqueness:

```php
$new_post = $post->replicate()->resluggify();
```

See [issue #37](https://github.com/cviebrock/eloquent-sluggable/issues/37) if you want to use Eloquent-Sluggable with Eloquent but outside of Laravel.

There is also a handy helper in the trait for finding a model based on it's slug:

```php
$post = Post::findBySlug('my-slug');
```

This is basically a wrapper for `Post::where('slug-field','=','my-slug')->first()`.  If your slugs aren't unique, then use the `getBySlug()` method which will return an Eloquent collection.



<a name="config"></a>
## Configuration

Configuration was designed to be as flexible as possible. You can set up defaults for all of your Eloquent models, and then override those settings for individual models.

By default, global configuration can be set in the `app/config/sluggable.php` file. If a configuration isn't set, then the package defaults from `vendor/cviebrock/eloquent-sluggable/config/sluggable.php` are used. Here is an example configuration, with all the default settings shown:

```php
return [
	'build_from'      => null,
	'save_to'         => 'slug',
	'max_length'      => null,
	'method'          => null,
	'separator'       => '-',
	'unique'          => true,
	'include_trashed' => false,
	'on_update'       => false,
	'reserved'        => null,
];
```

### build_from

This is the field or array of fields from which to build the slug. Each `$model->field` is contactenated (with space separation) to build the sluggable string. This can be model attribues (i.e. fields in the database) or custom getters. So, for example, this works:

```php
class Person extends Eloquent implements SluggableInterface
{
	use SluggableTrait;

	protected $sluggable = [
		'build_from' => 'fullname',
	]

	public function getFullnameAttribute() {
		return $this->firstname . ' ' . $this->lastname;
	}
}
```

If `build_from` is empty, false or null, then the value of `$model->__toString()` is used.

### save_to

The attribute field in your model where the slug is stored. By default, this is "slug". You need to create this column in your table when defining your schema:

```php
Schema::create('posts', function ($table) {
	$table->increments('id');
	$table->string('title');
	$table->string('body');
	$table->string('slug');
	$table->timestamps();
});
```

### max_length

Setting this to a positive integer will ensure that your generated slugs are restricted to a maximum length (e.g. to ensure that they fit within your databse fields). By default, this value is null and no limit is enforced.

Note: If `unique` is enabled (which it is by default), and you anticipate having several models with the same slug, then you should set this value to a few characters less than the length of your database field. The reason why is that the class will append "-1", "-2", "-3", etc., to subsequent models in order to maintain uniqueness. These incremental extensions aren't included in part of the `max_length` calculation.

### method

Defines the method used to turn the sluggable string into a slug. There are three possible options for this configuration:

1. When `method` is null (the default setting), the package uses [Cocur/Slugify](https://github.com/cocur/slugify) to create the slug.

2. When `method` is a callable, then that function or class method is used. The function/method should expect two parameters: the string to process, and a separator string. For example, to duplicate the default behaviour, you could do:

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

For more complex slugging requirements, see [Extending Sluggable](#extending) below.

### separator

This defines the separator used when building a slug, and is passed to the `method` defined above. The default value is a hyphen.

### unique

This is a boolean defining whether slugs should be unique among all models of the given type. For example, if you have two blog posts and both are called "My Blog Post", then they will both sluggify to "my-blog-post" (when using Sluggable's default settings). This could be a problem, e.g. if you use the slug in URLs.

By turning `unique` on, then the second Post model will sluggify to "my-blog-post-1". If there is a third post with the same title, it will sluggify to "my-blog-post-2" and so on. Each subsequent model will get an incremental value appended to the end of the slug, ensuring uniqueness.

### include_trashed

Setting this to `true` will also check deleted models when trying to enforce uniqueness. This only affects Eloquent models that are using the [softDelete](http://laravel.com/docs/eloquent#soft-deleting) feature. Default is `false`, so soft-deleted models don't count when checking for uniqueness.

### on_update

A boolean. If it is `false` (the default value), then slugs will not be updated if a model is resaved (e.g. if you change the title of your blog post, the slug will remain the same) or the slug value has already been set. You can set it to `true` (or manually change the $model->slug value in your own code) if you want to override this behaviour.

(If you want to manually set the slug value using your model's Sluggable settings, you can run `$model->resluggify()` to force Sluggable to update the slug field.)

### reserved

An array of values that will never be allowed as slugs, e.g. to prevent collisions with existing routes or controller methods, etc.. This can be an array, or a closure that returns an array. Defaults to `null`: no reserved slug names.


<a name="route-model"></a>
##Route-model Binding

To start retrieving Models using the slug or the Id, you can update `/bootstap/app.php` by adding an entry to override the router.
In the section titled 'Bind Important Interfaces' add the following:

```php
    $app->singleton(
        'router',
        '\Cviebrock\EloquentSluggable\SluggableRouter'
    );
```

If you prefer finding the models within your Controller, or the routes file, you can use a couple of helper methods:

```php
    Post::findBySlugOrId('slug-or-id');
```

or

```php
    Post::findBySlugOrIdOrFail('slug-or-id');
```

<a name="extending"></a>
## Extending Sluggable

Sometimes the configuration options aren't sufficient for complex needs (e.g. maybe the uniqueness test needs to take other attributes into account, or maybe you need to make two slugs for the same model).

In instances like these, your best bet is to overload some of SluggableTrait's methods with your own functions, either on a per-model basis, or in your own trait that extends SluggableTrait. Each step of the slugging process is broken out into it's own method, and those are called in turn when the slug is generated.

Take a look at `SluggableTrait->sluggify()` to see the order of operations, but you might consider overloading any of the following protected methods:

### needsSlugging()

Determines if the model needs to be slugged. Should return a boolean.

### getSlugSource()

Returns a string that forms the source of the slug (usually based on the `build_from` configuration value).

### generateSlug($source)

The actual slugging code. Usually implements whatever is defined in the `method` configuration, but could call out to other slugging libraries. Takes the source string (above) and returns a string.

### validateSlug($slug)

Validates that the generated slug is valid, usually by checking it against anything defined in the `reserved` configuration. Should return a valid slug string.

### makeSlugUnique($slug)

Checks to see if the given slug is unique. Should return a unique slug string.

### generateSuffix($slug, $list)

Takes the current slug and a list of "similar" slugs (e.g. "slug-1", "slug-2", etc.), and returns the next in the series.  Usually just returns _N+1_ but could be modified to use random, or alphabetic suffixes instead of incrementing integers.

### getExistingSlugs($slug)

Returns all existing slugs that are "similar" to the given one. Should return an key-value array of existing slugs, where the values are the Eloquent model's slug values (from the `save_to` field) and the keys are the respective Eloquent model's ids.

### setSlug($slug)

Writes the (generated, valid, and unique) slug to the model's attributes.




<a name="bugs"></a>
## Bugs, Suggestions and Contributions

Thanks to [everyone](/cviebrock/eloquent-sluggable/graphs/contributors) who has contributed to this project!

Please use Github for bugs, comments, suggestions.

1. Fork the project.
2. Create your bugfix/feature branch and write your (well-commented) code.
3. Create unit tests for your code:
	- Run `composer install --dev` in the root directory to install required testing packages.
	- Add your test methods to `eloquent-sluggable/tests/SluggableTest.php`.
	- Run `vendor/bin/phpunit` to the new (and all previous) tests and make sure everything passes.
3. Commit your changes (and your tests) and push to your branch.
4. Create a new pull request against the eloquent-sluggable `master` branch.

**Please note that you must create your pull request against the `master` branch for fixes to the version compatible with Laravel 5.  If you are working on Laravel 4 support, use the `2.x` branch.**



<a name="copyright"></a>
## Copyright and License

Eloquent-Sluggable was written by Colin Viebrock and released under the MIT License. See the LICENSE file for details.

Copyright 2013 Colin Viebrock
