# Route Model Binding and Eloquent-Sluggable

Route Model Binding is easy to implement with only minor configuration to your models.


## Implicit Binding

Implicit binding requires adding a `getRouteKeyName()` method to your model that returns the name
of the slug field:

```php
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Sluggable, SluggableScopeHelpers;
    
    public function sluggable(): array
    {
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
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
    
}
```

From there, you can set up your routes as described in the Eloquent documentation:

```php
Route::get('api/posts/{post}', function(App\Post $post): string {
    return $post->title;
});
```

In this example, since the Eloquent type-hinted `$post` variable defined on the route 
matches the {post} segment in the route's URI, Laravel will automatically inject the 
model instance that has a slug matching the corresponding value from the request URI.

Further, if you are using the [SluggableScopeHelpers](SCOPE-HELPERS.md) trait, you can bind
the default slug to the route parameter with:
 
```php
public function getRouteKeyName(): string
{
    return $this->getSlugKeyName();
}
```


## Explicit Binding

You can also use the `RouteServiceProvider::boot` method as described in the 
[Laravel Documentation](https://laravel.com/docs/routing#route-model-binding) to 
handle explicit route model binding.


- - -

Copyright (c) 2013 Colin Viebrock
