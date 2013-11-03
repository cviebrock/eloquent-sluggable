# Change Log


## 1.0.2 - 03-Nov-2013

- allow reverting to a "smaller" version of a similar slug (#5, thanks @alkin).
- better collection filtering to prevent duplicate slugs on similar long slugs (#3, #6, thanks @torkiljohnsen, @brandonkboswell).
- `include_trashed` option to include soft-deleted models when checking for uniqueness (#8, thanks @slovenianGooner).
- fix ""undefined variable reserved" error (#9, thanks @altrim).

## 1.0.1 - 02-Jul-2013

- `reserved` configuration option prevents generated slugs from being from a list of
  "reserved" names (e.g. colliding with routes, etc.) (#2, thanks @ceejayoz).


## 1.0.0 - 18-Jun-2013

- First non-beta release.
- `$sluggable` property of model switched back to static, maintains L3 compatability (thanks @orkhan).
- Updated type hinting in `Sluggable::make()` to better handle extended models (#1, thanks @altrim).


## 1.0.0-beta - 11-Jun-2013

- Initial beta release.
