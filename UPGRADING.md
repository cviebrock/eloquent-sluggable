# Upgrading

## Upgrading from 3.x to 4.x

### Configuration Changes

The configuration array has changed slightly between versions:

* In your `app/config/sluggable.php` configuration file, remove the `save_to` and `on_update` 
  parameters as they are no longer used.  Rename `build_from` to `source`, and convert the other
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

The `php artisan sluggable:table` command has been deprecated so you will need to make and run your own 
migrations if you need to add columns to your database tables to store slug values.

Route Model Binding has been removed from the package.  You are encouraged to handle this yourself
in the `RootServiceProvider::boot` method as described in the [Laravel Documentation](https://laravel.com/docs/5.2/routing#route-model-binding)
