# Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via pull requests via 
[Github](https://github.com/cviebrock/eloquent-sluggable).

1. Fork the project.
2. Create your bugfix/feature branch and write your (well-commented) code.
3. Ensure you follow our coding style:
    - Run `composer run style:check` to check.
    - Run `composer run style:fix` to automagically fix styling errors.
4. Run basic static analysis on your code with `composer run analyze` and fix any errors.
5. Create unit tests for your code:
	- Run `composer install --dev` in the root directory to install required testing packages.
	- Add your test classes/methods to the `/tests/` directory.
	- Run `composer run tests` and make sure everything passes (new and old tests).
6. Updated any documentation (e.g. in `README.md`), if appropriate.
7. Commit your changes (and your tests) and push to your branch.
8. Create a new pull request against this package's `master` branch.


## Pull Requests

- **Use the [PHP-CS-Fixer Coding Standard](https://cs.symfony.com/doc/ruleSets/PhpCsFixer.html).**
  The easiest way to apply the conventions is to run `composer run style:fix`.

- **Run static analysis with [phpstan](https://phpstan.org).**
  The easiest way to check is with `composer run analyze`.  Bonus points if you can bump up the
  analysis level in `phpstan.dist.neon`!

- **Add tests!**  Your pull request won't be accepted if it doesn't have tests.

- **Document any change in behaviour.**  Make sure the `README.md` and any other relevant 
  documentation are kept up-to-date.

- **Consider our release cycle.**  We try to follow [SemVer v2.0.0](http://semver.org/). 
  Randomly breaking public APIs is not an option.

- **Create feature branches.**  Don't ask us to pull from your master branch.

- **One pull request per feature.**  If you want to do more than one thing, send multiple pull requests.

- **Send coherent history.** - Make sure each individual commit in your pull request is meaningful. 
  If you had to make multiple intermediate commits while developing, please 
  [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages)
  before submitting.

- Don't worry about updating `CHANGELOG.md` or `.semver`.  The package administrator
  will handle updating those when new releases are created.
  

**Thank you!**
