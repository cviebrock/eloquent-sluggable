# Change Log

## 8.0.8 - 11-Jun-2021

- fix event tests and `registerModelEvent()` hook (#556, #561, thanks @standaniels)


## 8.0.7 - 19-May-2021

- fix issue with `SluggableObserver::SAVED` not always saving 
  the model (#558, #560, thanks @llewellyn-kevin)


## 8.0.5 - 28-Feb-2021

- started unique suffixes with "-2" instead of "-1" (#549, thanks @Tamim26061)
  - this can be adjusted via the `firstUniqueSuffix` config setting

 
## 8.0.4 - 20-Jan-2021

- bug fix for #543#issuecomment-763391948 (thanks @dluague)


## 8.0.3 - 19-Jan-2021

- add ability to slug models on Eloquent's `saved` event, rather than
  `saving`
  - this adds a few more SQL queries per save, but allows for the use of
    the primary key field in the `source` configuration (see #539 and #448)
  - default configuration remains the same, but might change in a future release
- added base `customizeSlugEngine` and `scopeWithUniqueSlugConstraints` methods
  to the trait, to enforce type hinting and return values
  - NOTE: if you were using these methods in your models, you may need to ensure
    the method signatures match now
  - see #544 for more information, including what to do if you had custom
    methods in another trait
- add `slugEngineOptions` configuration option (see #454, thanks @Adioz01)
- move automated testing from travis-ci to Github actions (#534, thanks @cbl)
- clean up some third-party tools and badges
- clean up docblocks, return hints, and PHPUnit method calls


## 8.0.2 - 29-Nov-2020

- support PHP 8.0 (#533, thanks @cbl)


## 8.0.1 - 28-Sep-2020

- fix when manually setting a slug to a "falsy" value (#527, thanks @huiyang)


## 8.0.0 - 10-Sep-2020

- Laravel 8 support


## 7.0.1 - 06-Apr-2020

- fix to help support translatable slugs (using either spatie or Astrotomic package) (#506, thanks @GeoSot) 


## 7.0.0 - 04-Mar-2020

- Laravel 7.0 support


## 6.0.3 - 09-Feb-2020

- bump [cocur/slugify](https://github.com/cocur/slugify) to `^4.0`


## 6.0.2 - 09-Oct-2019

- fix for PHP 7.4 beta (#486, thanks @KamaZzw)


## 6.0.1 - 13-Sep-2019

- fix for semantic versioning


## 6.0.0 - 03-Sep-2019

- Laravel 6.0 support (note the package version will now follow the Laravel version)


## 4.8.0 - 28-Feb-2019

- Laravel 5.8 support (#460, big thanks @tabuna)


## 4.7.0 - 24-Feb-2019

- Fix slug getting set to `null` if model is updated with no source column loaded (#450, thanks @mylgeorge)


## 4.6.0 - 04-Sep-2018

- Laravel 5.7 support


## 4.5.1 - 21-May-2018

- Bump versions of package dependencies


## 4.5.0 - 10-Feb-2018

- Laravel 5.6 support


## 4.4.1 - 04-Jan-2018

- Better exception message when calling `SlugService::createSlug` with an invalid attribute (#402, thanks @lptn)
- Prettier unit test output


## 4.4.0 - 12-Dec-2017

- Make sure truncated slugs (due to maxLength) don't end in a separator (#398)
- Add `maxLengthKeepWords` configuration option (#398)


## 4.3.0 - 31-Aug-2017

- Laravel 5.5 support, including auto-registration
- Bumped `cocur/slugify` to `^3.0`


## 4.2.5 - 31-Aug-2017

- Fixing composer requirements to support Laravel 5.4 only


## 4.2.4 - 04-Jul-2017

- Documentation change (#374, thanks @fico7489)


## 4.2.3 - 18-Apr-2017

- Switch to allow extending the class (#356, thanks @haddowg)
- Fix when adding suffixes to reserved slugs (#356, thanks @haddowg)


## 4.2.2 - 23-Mar-2017

- Better handling of numeric and boolean slug sources (#351, thanks @arturock)


## 4.2.1 - 01-Feb-2017

- Support Laravel 5.4 (#339, thanks @maddhatter)


## 4.1.2 - 09-Nov-2016

- Fix in `getExistingSlugs` when using global scopes (#327)
- Update `Cocur\Slugify` to `^2.3`.


## 4.1.1 - 12-Oct-2016

- Fix for slugs updating when they don't need to, when using `onUpdate` with `unique` (#317) 


## 4.1.0 - 14-Sep-2016

- The goal of the 4.1.x releases will be to focus on support in Laravel 5.3, only providing support for 5.1/5.2
  where it is easy and doesn't affect performance significantly.
- Drop support for PHP <5.6 and HHVM (no longer supported by Laravel 5.3); fixes test build process


## 4.0.4 - 13-Sep-2016

- Fix `SluggableScopeHelpers` to work when using the short configuration syntax (#314).


## 4.0.3 - 15-Jul-2016

- Added `$config` argument to `SlugService::createSlug` method for optionally overriding 
  the configuration for a statically generated slug (#286).


## 4.0.2 - 17-Jun-2016

- Added  `SluggableScopeHelpers` trait which restores some of the scoping and query
  functionality of the 3.x version of the package (#280, thanks @unstoppablecarl and @Keoghan).
- Added the `onUpdate` configuration option back to the package.
- Updated the documentation to show usage of the `SluggableScopeHelpers` trait, and
  how to use route model binding with slugs.


## 4.0.1 - 13-Jun-2016

- Fixed several bugs related to Laravel 5.1 and collections (#263, #274).


## 4.0.0 - 10-Jun-2016

- Fix for Laravel 5.1 (#263 thanks @roshangautam and @andregaldino).
- Update `Cocur\Slugify` to `^2.1` (#269 thanks @shadoWalker89).


## 4.0.0-beta - 01-Jun-2016

- Major revision
  - Model configuration is now handled in a `sluggable()` method.
    on the model instead of a property, and configuration options are now camelCase
  - Ability to generate more than one slug per model.
  - Removed all `findBy...()` scope/methods (can't really be used when a model
    has multiple slugs ... plus the code is easy enough to implement in the model).
  - Removed `onUpdate` configuration option.  If you want to re-generate a slug
    on update, then set the model's slug to `null` before saving.  Otherwise, existing
    slugs will never be overwritten.
  - `createSlug()` is no longer a static method on the model, but is a public method
    on the _SlugService_ class, with a different method signature (see docs).
  - Removed artisan command to add slug column to tables.  You will need to do this
    (pretty simple) task yourself now. 
  - Several bug fixes.
- See [UPGRADING.md](UPGRADING.md) for details.


## 3.1.4 - 03-Jan-2016

- Compatible with Laravel 5.2 (by removing calls to composer from migrate command)


## 3.1.3 - 07-Dec-2015

- Fix for PostgreSQL and `findBySlugOrId()` (#205 thanks @Jaspur)


## 3.1.2 - 07-Nov-2015

- Fix some namespacing issues in docblocks (#195)
- Streamline artisan migrate call (#191 thanks @ntzm)
- Fix bug when using magic getters (#188 thanks @ChrisReid)
- Add a static slug generator (#185 thanks @phroggyy)
- Lots of PSR-2 fixes


## 3.1.1 - 26-Oct-2015

- Fix missing class reference (#192)
- Clean up migration code (#191 thanks @natzim)
- Fix when using magic getters (#188 thanks @ChrisReid)


## 3.1.0 - 14-Oct-2015

- Convert code-base to PSR-2
- If the source is empty, then set the slug to `null` (#162 thanks @PallMallShow)
- Ability to use a model's relations in the `build_from` configuration (#171 thanks @blaxxi)
- Added `getSlugEngine()` method so that the Cocur\Slugify class can be configured
- Updated the migration stub for Laravel 5.1's PSR-2 changes (#174 thanks @39digits)
- Added `slugging` and `slugged` Eloquent model events
- Fix for `findBySlugOrId()` methods when the slug is numeric (#161 thanks @canvural)
- Add static method `Model::createSlug('some string')` (#185 thanks @phroggyy)


## 3.0.0 - 06-Jul-2015

- Don't increment unique suffix if slug is unchanged (#108 thanks @kkiernan)


## 3.0.0-beta - 12-Jun-2015

- Laravel 5.1 support (#141/#148 thanks @Keoghan, @Bouhnosaure)
- Removed `use_cache` option and support
- Use (Cocur\Slugify)[https://github.com/cocur/slugify] as default slugging method
- Fix for `include_trashed` option not working for models that inherit the SoftDeletes trait (#136 thanks @ramirezd42)
- Added `generateSuffix()` method so you could use different strategies other than integers for making incremental slugs (#129 thanks @EspadaV8)
- Various scope and lookup fixes (thanks @xire28)


## 3.0.0-alpha - 11-Feb-2015

- Laravel 5.0 support
- Remove Ardent support and tests
- Fix so that `max_length` option only applies to string slugs (#64 thanks @enzomaserati)


## 2.0.5 - 13-Nov-2014

- Fixed `findBySlug()` to return a model and `getBySlug()` to return a collection (#72 thanks @jaewun and @Jono20202)
- Fixed testbench version requirements (#87 thanks @hannesvdvreken)
- Fixed so that `max_length` option only applies to string slugs (#64 thanks @enzomaserati)
- Cleaned up some redundant code and documentation (thanks @hannesvdvreken, @Anahkiasen, @nimbol)


## 2.0.4 - 23-Sep-2014

- Fixed softDelete behaviour and tests so Laravel 4.2 and earlier are supported (#56 thanks @hammat).
- Fixed alias for `Illuminate\Support\Str` to prepare for Laravel 4.3/5.0 (#58 thanks @0xMatt).


## 2.0.3 - 17-Jul-2014

- Don't allow slugs to be empty (#44 thanks @lfbittencourt).


## 2.0.2 - 19-Jun-2014

- Add `getExistingSlugs()` method to trait (#36 thanks @neilcrookes).


## 2.0.1 - 13-May-2014

- Fix issue where manually setting the slug field would be overwritten when updating the sluggable fields (#32 thanks @D1kz).


## 2.0.0 - 27-Apr-2014

- See the [README](https://github.com/cviebrock/eloquent-sluggable/tree/master#upgrading) for all upgrading details.
- Now uses traits, so PHP >= 5.4 is required.
- Configuration and usage is _mostly_ backwards-compatible, although users of Ardent or anyone who force-builds slugs will need to make some changes.
- Use Laravel's cache to speed up unique slug generation (and prevent duplicates in heavy-usage cases).


## 1.0.8 - 20-Feb-2014

- Fix issue where replicated models couldn't forcibly be re-slugged (#20 thanks @trideout).


## 1.0.7 - 03-Dec-2013

- Really fix issue #15 -- "not in object context" errors.  Previous fix didn't work for PHP 5.3.x (thanks again @mayoz).


## 1.0.6 - 02-Dec-2013

- Update composer requirements so that the package installs nicely with the upcoming Laravel 4.1.
- Updated docs to show how to use package with [Ardent](http://github.com/laravelbook/ardent) models (thanks to @Flynsarmy for the pointers).


## 1.0.5 - 15-Nov-2013

- Fix issues where slugs would alternate between "slug" and "slug-1" when `on_update` and `unique` are set (#14, #16, thanks @mikembm, @JoeChilds).
- Make `isIncremented` method static to solve possible "not in object context" error (#15, thanks @mayoz).


## 1.0.4 - 05-Nov-2013

- Unit testing ... woot!  Building this revealed three new bugs:
	- Fixed bug where using the default `method` didn't take into account a custom `separator`.
	- Proper fix for issue #5.
	- `include_trashed` wasn't working because you can't read the protected `softDelete` property of the model.


## 1.0.3 - 04-Nov-2013

- Fixed PHP warnings about uninitialized variable (#10, thanks @JoeChilds).


## 1.0.2 - 03-Nov-2013

- Allow reverting to a "smaller" version of a similar slug (#5, thanks @alkin).
- Better collection filtering to prevent duplicate slugs on similar long slugs (#3, #6, thanks @torkiljohnsen, @brandonkboswell).
- `include_trashed` option to include soft-deleted models when checking for uniqueness (#8, thanks @slovenianGooner).
- Fixed "undefined variable reserved" error (#9, thanks @altrim).


## 1.0.1 - 02-Jul-2013

- `reserved` configuration option prevents generated slugs from being from a list of "reserved" names (e.g. colliding with routes, etc.) (#2, thanks @ceejayoz).


## 1.0.0 - 18-Jun-2013

- First non-beta release.
- `$sluggable` property of model switched back to static, maintains L3 compatability (thanks @orkhan).
- Updated type hinting in `Sluggable::make()` to better handle extended models (#1, thanks @altrim).


## 1.0.0-beta - 11-Jun-2013

- Initial beta release.
