# Change Log


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

- `reserved` configuration option prevents generated slugs from being from a list of
  "reserved" names (e.g. colliding with routes, etc.) (#2, thanks @ceejayoz).


## 1.0.0 - 18-Jun-2013

- First non-beta release.
- `$sluggable` property of model switched back to static, maintains L3 compatability (thanks @orkhan).
- Updated type hinting in `Sluggable::make()` to better handle extended models (#1, thanks @altrim).


## 1.0.0-beta - 11-Jun-2013

- Initial beta release.
