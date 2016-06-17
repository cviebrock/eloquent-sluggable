# Upgrading

## Upgrading from 3.x to 4.x

### Configuration Changes

The configuration array has changed slightly between versions:

* In your `app/config/sluggable.php` configuration file, remove the `save_to`  
  parameter as it is no longer used.  Rename `build_from` to `source`, and convert the other
  parameters from snake_case to lower camelCase (e.g. `include_trashed` -> `includeTrashed`).
* Your models no longer need to implement `Cviebrock\EloquentSluggable\SluggableInterface`.
* Your models should now use the trait `Cviebrock\EloquentSluggable\Sluggable` instead of 
  `Cviebrock\EloquentSluggable\SluggableTrait`, which no longer exists.
* Per-model configuration has been moved from a protect property into a protected method, and 
  the configuration array is now keyed with the attribute field where the slug is stored (i.e. the
  previous value of the `save_to` configuration.
  
#### Version 3.x Configuration Example:
  
```php
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements SluggableInterface
{
    use SluggableTrait;

    /**
     * Sluggable configuration.
     *
     * @var array
     */
    protected $sluggable = [
        'build_from'      => 'title',
        'save_to'         => 'slug',
        'separator'       => '-',
        'include_trashed' => true,
    ];
}
```

#### Converted Version 4.x Example:

```php
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Sluggable;

    /**
     * Sluggable configuration.
     *
     * @var array
     */
    public function sluggable() {
        return [
            'slug' => [
                'source'         => 'title',
                'separator'      => '-',
                'includeTrashed' => true,
            ]
        ];
    }
}
```

### Other Changes

#### Artisan Command

The `php artisan sluggable:table` command has been deprecated so you will need to make and run your own 
migrations if you need to add columns to your database tables to store slug values.

#### Route Model Binding

Route Model Binding has been removed from the package.  You are encouraged to handle this yourself
in the model's `getRouteKeyName` method, or in a `RootServiceProvider::boot` method as described in 
the [Laravel Documentation](https://laravel.com/docs/5.2/routing#route-model-binding).  

See [ROUTE-MODEL-BINDING.md] for details.

#### Query Scopes

Because the package now supports multiple slugs per model, the `findBySlug()` and other `findBy*`
methods have been removed from the package by default, as has the `whereSlug()` query scope.  You should 
just update your code to use standard Eloquent methods to find your models, specifying which 
fields to search by:

```php
// OLD
$posts = Post::whereSlug($input)->get();
$post = Post::findBySlug($input);
$post = Post::findBySlugOrFail($input);
$post = Post::findBySlugOrIdOrFail($input);

// NEW
$posts = Post::where('slug',$input)->get();
$post = Post::where('slug', $input)->first();
$post = Post::where('slug', $input)->firstOrFail();
$post = Post::where('slug', $input)->get() ?: Post::findOrFail((int)$input);
```

Alternatively, your model can use the `SluggableScopeHelpers` trait.  
See [SCOPE-HELPERS.md] for details.
