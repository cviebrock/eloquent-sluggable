# Route Model Binding and Eloquent-Sluggable

Route Model Binding has been removed from the 4.x version of the core package.  However, implementing 
it yourself is very easy!


## Implicit Binding

Implicit binding is as easy as adding a `getRouteKeyName()` method to your model that returns the name
of the slug field:

```php
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Sluggable;
    
    public function sluggable() {
        return [
            'slug' => [
                'source' => 'title',
            ]
        ];
    }
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
    
}
```

From there, you can set up your routes as described in the Eloquent documentation:

```php
Route::get('api/posts/{post}', function (App\Post $post) {
    return $post->title;
});
```

In this example, since the Eloquent type-hinted `$post` variable defined on the route 
matches the {post} segment in the route's URI, Laravel will automatically inject the 
model instance that has a slug matching the corresponding value from the request URI.

Further, if you are using the [SluggableScopeHelpers](SCOPE-HELPERS.md) trait, you can bind
the default slug to the route parameter with:
 
```php
public function getRouteKeyName()
{
    return $this->getSlugKeyName();
}
```


## Explicit Binding

You can also use the `RouteServiceProvider::boot` method as described in the 
[Laravel Documentation](https://laravel.com/docs/5.2/routing#route-model-binding) to 
handle explicit route model binding.


## Laravel 5.3 Note

If you are using Laravel 5.3, please take note of the instructions in the
[upgrade guide](https://laravel.com/docs/5.3/upgrade#upgrade-5.3.0)
under _Middleware_ > _Binding Substitution Middleware_ for adding the correct middleware
and classes to your project to enable slug-based route model binding.
 
 
- - -

Copyright (c) 2013 Colin Viebrock
