# Todos

- [x] Write tests
- [x] Better docblock and inline-commenting
- [x] Make code style consistent
- [x] Drop `develop` branch and just have `master` and tagged releases
- [x] Add check that model uses softDelete trait when using `with_trashed` (see issue #47)


## Planned changes (possibly BC-breaking) for next major version

- [x] switch default slugging method from `Str::slug` to an external package/class that can handle transliteration of other languages (e.g. https://github.com/cocur/slugify)
    - [ ] provide interface into `cocur/slugify` to allow for custom rules, etc.
- [X] convert `findBySlug` into a scope (as suggested by @unitedworks in #40)
- [ ] more configurable `unique` options (see issue #53)
- [x] refactor, or remove, caching code (it wasn't really thought out well enough, IMO)
- [ ] add events, e.g. `eloquent.slug.created`, `eloquent.slug.changed`, etc. (as suggested in #96 and #101)
