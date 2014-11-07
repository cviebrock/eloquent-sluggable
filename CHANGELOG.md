# Change Log

## 3.0.0 - xx-xxx-2014

- Laravel 5.0 support
- Fix so that `max_length` option only applies to string slugs (#64 thanks @enzomaserati)


## 2.0.4 - 23-Sep-2014

- fixed softDelete behaviour and tests so Laravel 4.2 and earlier are supported (#56 thanks @hammat).
- fixed alias for `Illuminate\Support\Str` to prepare for Laravel 4.3/5.0 (#58 thanks @0xMatt).


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

- Fix issue where replicated models couldn't forceably be reslugged (#20 thanks @trideout).


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
