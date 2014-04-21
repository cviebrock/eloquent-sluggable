# Eloquent-Sluggable Integration with Ardent

[Ardent](//github.com/laravelbook/ardent) is a package that "provides self-validating smart models for Laravel Framework 4's Eloquent ORM".  You can configure your Ardent models to include built-in validator rules, and Ardent will handle all the hard work for you.

Unfortunately, the `eloquent.saving` hook that Sluggable relies on to generate slugs doesn't get fired before Ardent does it's validation.  So, imagine your Ardent model looks like this:

```php
class Post extends Ardent {

	/**
	 * Ardent rules
	 */
	public static $rules = array(
		'title' => 'required',
		'slug'  => 'required|unique'
	);

	/**
	 * Sluggable config
	 */
	protected $sluggable = array(
		'build_from' => 'title',
		'save_to'    => 'slug',
	);

}
```

When you go to save the model, you'll get a validation error:

> Slug field required

There are three ways around this:

1. Don't specify rules for your slug attribute with Ardent.  Sluggable will handle the generation and unique checks for you.

2. Create a `beforeValidate` method in your model that generates the slug first:

```php
public function beforeValidate()
{
	$this->sluggify();
}
```

3. Manually generate the slug before saving:

```php
$post = new Post(...);

$post->sluggify();
$post->save();
```

The second option above is likely the most elegant.  If you have your own BaseModel class that extends Ardent, then just put the `beforeValidate()` method in there and you should be good to go!

