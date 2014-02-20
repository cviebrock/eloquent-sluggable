# Eloquent-Sluggable

Easy creation of slugs for your Eloquent models in Laravel 4.

[![Latest Stable Version](https://poser.pugx.org/cviebrock/eloquent-sluggable/v/stable.png)](https://packagist.org/packages/cviebrock/eloquent-sluggable)
[![Total Downloads](https://poser.pugx.org/cviebrock/eloquent-sluggable/downloads.png)](https://packagist.org/packages/cviebrock/eloquent-sluggable)

* [Background](#background)
* [Installation](#installation)
* [Updating your Eloquent Models](#eloquent)
* [Using the Class](#usage)
* [Configuration](#config)
* [Bugs, Suggestions and Contributions](#bugs)
* [Copyright and License](#copyright)



<a name="background"></a>
## Background: What is a slug?

A slug is a simplified version of a string, typically URL-friendly.  The act of "slugging" a string usually involves converting it to one case, and removing any non-URL-friendly characters (spaces, accented letters, ampersands, etc.).  The resulting string can then be used as an indentifier for a particular resource.

For example, I have a blog with posts.  I could refer to each post via the ID:

	http://example.com/post/1
	http://example.com/post/2

... but that's not particularly friendly (especially for [SEO](http://en.wikipedia.org/wiki/Search_engine_optimization)). You probably would prefer to use the post's title in the URL, but that becomes a problem if your post is titled "My Dinner With André & François", because this is pretty ugly too:

	http://example.com/post/My+Dinner+With+Andr%C3%A9+%26+Fran%C3%A7ois

The solution is to create a slug for the title and use that instead.  You might want to use Laravel's built-in `Str::slug()` method to convert that title into something friendlier:

	http://example.com/post/my-dinner-with-andre-francois

A URL like that will make users happier (readable, easier to type, etc.).

For more information, you might want to read [this](http://en.wikipedia.org/wiki/Slug_(web_publishing)#Slug) description on Wikipedia.

Slugs tend to be unique as well.  So if I wrote another post with the same title, I'd want to distinguish between them somehow, typically with an incremental counter added to the end of the slug:

	http://example.com/post/my-dinner-with-andre-francois
	http://example.com/post/my-dinner-with-andre-francois-1
	http://example.com/post/my-dinner-with-andre-francois-2

This keeps URLs unique.

The **Eloquent-Sluggable** package for Laravel 4 will handle all of this for you automatically, with minimal configuration at the start.



<a name="installation"></a>
## Installation

First, you'll need to add the package to the `require` attribute of your `composer.json` file:

```json
{
    "require": {
        "cviebrock/eloquent-sluggable": "1.0.*"
    },
}
```

Aftwards, run `composer update` from your command line.

Then, update `app/config/app.php` by adding entries for the service providers and class aliases:

```php

	'providers' => array(

		// ...

		'Cviebrock\EloquentSluggable\SluggableServiceProvider',

	);

	// ...

	'aliases' => array(

		// ...

		'Sluggable' => 'Cviebrock\EloquentSluggable\Facades\Sluggable',

	);


```

Finally, from the command line again, run `php artisan config:publish cviebrock/eloquent-sluggable` to publish the configuration file.



<a name="eloquent"></a>
## Updating your Eloquent Models

Define a public property `$sluggable` with the definitions (see [Configuration](#config) below for details):

```php
class Post extends Eloquent
{

	public static $sluggable = array(
		'build_from' => 'title',
		'save_to'    => 'slug',
	);

}
```

That's it ... your model is now "sluggable"!



<a name="usage"></a>
## Using the Class

Saving a model is easy:

```php
$post = new Post(array(
	'title' => 'My Awesome Blog Post'
));

$post->save();
```

And so is retrieving the slug:

```php
echo $post->slug;
```

See the [README-Ardent.md](./README-Ardent.md) file for using Eloquent-Sluggable with [Ardent](//github.com/laravelbook/ardent).

Also note that if you are replicating your models using Eloquent's `replicate()` method, then you will need to explicity tell the package to force a re-slugging of the model afterwards to ensure uniqueness:

```php
$new_post = $post->replicate();
Sluggable::make($new_post, true);
```


<a name="config"></a>
## Configuration

Configuration was designed to be as flexible as possible.  You can set up defaults for all of your Eloquent models, and then override those settings for individual models.

By default, global configuration can be set in the `app/config/packages/cviebrock/eloquent-sluggable/config.php` file.  If a configuration isn't set, then the package defaults from `vendor/cviebrock/eloquent-sluggable/src/config/config.php` are used.  Here is an example configuration, with all the default settings shown:

```php
return array(
	'build_from'      => null,
	'save_to'         => 'slug',
	'method'          => null,
	'separator'       => '-',
	'unique'          => true,
	'include_trashed' => false,
	'on_update'       => false,
	'reserved'        => null,
);
```

### build_from

This is the field or array of fields from which to build the slug. Each `$model->field` is contactenated (with space separation) to build the sluggable string.  This can be model attribues (i.e. fields in the database) or custom getters.  So, for example, this works:

```php
class Person extends Eloquent {

	public static $sluggable = array(
		'build_from' => 'fullname'
	);

	public function getFullnameAttribute() {
		return $this->firstname . ' ' . $this->lastname;
	}

}
```

If `build_from` is empty, false or null, then the value of `$model->__toString()` is used.

### save_to

The attribute field in your model where the slug is stored.  By default, this is "slug".  You need to create this column in your table when defining your schema:

```php
Schema::create('posts', function($table)
{
	$table->increments('id');
	$table->string('title');
	$table->string('body');
	$table->string('slug');
	$table->timestamps();
});
```

### method

Defines the method used to turn the sluggable string into a slug.  There are three possible options for this configuration:

1. When `method` is null (the default setting), the package uses Laravel's `Str::slug()` method to create the slug.

2. When `method` is a callable, then that function or class method is used.  The function/method should expect two parameters: the string to process, and a separator string.  For example, to duplicate the default behaviour, you could do:

	```php
		'method' => array('Illuminate\\Support\\Str', 'slug'),
	```

3. You can also define `method` as a closure (again, expecting two parameters):

	```php
		'method' => function( $string, $separator ) {
			return strtolower( preg_replace('/[^a-z]+/i', $separator, $string) );
		},
	```

Any other values for `method` will throw an exception.

### separator

This defines the separator used when building a slug, and is passed to the `method` defined above.  The default value is a hyphen.

### unique

This is a boolean defining whether slugs should be unique among all models of the given type.  For example, if you have two blog posts and both are called "My Blog Post", then they will both sluggify to "my-blog-post" (when using Sluggable's default settings).  This could be a problem, e.g. if you use the slug in URLs.

By turning `unique` on, then the second Post model will sluggify to "my-blog-post-1".  If there is a third post with the same title, it will sluggify to "my-blog-post-2" and so on.  Each subsequent model will get an incremental value appended to the end of the slug, ensuring uniqueness.

### include_trashed

Setting this to `true` will also check deleted models when trying to enforce uniqueness.  This only affects Eloquent models that are using the [softDelete](http://laravel.com/docs/eloquent#soft-deleting) feature.  Default is `false`, so soft-deleted models don't count when checking for uniqueness.

### on_update

A boolean.  If it is `false` (the default value), then slugs will not be updated if a model is resaved (e.g. if you change the title of your blog post, the slug will remain the same) or the slug value has already been set.  You can set it to `true` (or manually change the $model->slug value in your own code) if you want to override this behaviour.

(If you want to manually set the slug value using your model's Sluggable settings, you can run `Sluggable::make($model, true)`.  The second arguement forces Sluggable to update the slug field.)

### reserved

An array of values that will never be allowed as slugs, e.g. to prevent collisions with existing routes or controller methods, etc..  This can be an array, or a closure that returns an array.  Defaults to `null`: no reserved slug names.



<a name="bugs"></a>
## Bugs, Suggestions and Contributions

Please use Github for bugs, comments, suggestions.

1. Fork the project.
2. Create your bugfix/feature branch and write your code.
3. Create unit tests for your code:
	- Run `composer install --dev` in the root directory to install required testing packages.
	- Add your test methods to `eloquent-sluggable/tests/SluggableTest.php`.
	- Run `vendor/bin/phpunit` to the new (and all previous) tests and make sure everything passes.
3. Commit your changes (and your tests) and push to your branch.
4. Create a new pull request against the eloquent-sluggable `develop` branch.

**Please note that you must create your pull request against the `develop` branch.**



<a name="copyright"></a>
## Copyright and License

Eloquent-Sluggable was written by Colin Viebrock and released under the MIT License. See the LICENSE file for details.

Copyright 2013 Colin Viebrock
