# SluggableScopeHelpers Trait

The `SluggableScopeHelpers` trait adds a query scope and other methods to make finding models with a 
matching slug as easy as:

```php
$post = Post::findBySlug($slugString);
$post = Post::findBySlugOrFail($slugString);
```

These two methods have the same signature and functionality as Eloquent's `find()` and `findOrFail()` methods
except that they use the slug field instead of the primary key.

The helper trait also adds a query scope to help limit searches to a particular slug:

```php
$post = Post::where('author_id', '=', 3)
            ->whereSlug($slug)
            ->get();
```

By default, the trait looks at your `sluggable()` method and uses the first slug that's defined in the configuration
array for the helper scopes and methods.  If your model has more than one slugged field, you will either need to
put the field to be used for scopes first, or define an additional property on your model which indicates which
slug is the "primary" one:

```php
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Sluggable;
    use SluggableScopeHelpers;
    
    protected $slugKeyName = 'alternate';
    
    /**
     * Sluggable configuration.
     *
     * @var array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
            'alternate' => [
                'source' => 'subtitle',
            ]
        ];
    }
}
```

In the above case, `Post::findBySlugOrFail($slug)` is the equivalent to `Post::where('alternate,'=',$slug)->firstOrFail()`.


- - -

Copyright (c) 2013 Colin Viebrock
