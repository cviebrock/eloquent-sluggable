# Todos

- [x] Write tests
- [ ] Better docblock and inline-commenting
- [ ] Make code style consistent
- [ ] Drop `develop` branch and just have `master` and tagged releases
- [ ] Add check that model uses softDelete trait when using `with_trashed` (see issue #47)


## Planned changes (possibly BC-breaking) for next major version

- [ ] switch default slugging method from `Str::slug` to an external package/class that can handle transliteration of other languages (e.g. https://github.com/cocur/slugify)
- [ ] convert `findBySlug` into a scope (as suggested by @unitedworks in #40)
- [ ] more configurable `unique` options (see issue #53)
