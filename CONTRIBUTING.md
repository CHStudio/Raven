# Contributing

 * Coding standard for the project is [PSR-12](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-12-extended-coding-style-guide.md)
 * Any contribution must provide tests for additional introduced conditions
 * Any un-confirmed issue needs a failing test case before being accepted
 * Pull requests must be sent from a new hotfix/feature branch, not from `main`.

## Installation

To install the project and run the tests, you need to clone it first:

```sh
git clone git@github.com:CHStudio/Raven.git
```

You will then need to run install local dependencies using composer.

```sh
composer update
```

If you don't have composer installed locally, you can  install it using
[the official procedure](https://getcomposer.org/doc/00-intro.md#locally)

### Phive and composer installation

You can also install it inside the project using [Phive](https://phar.io/). The
dependency list with version constraint is available: [.phive/phars.xml](.phive/phars.xml).

### Docker based development environment

We created a [Dockerfile](provisioning/php/Dockerfile) which define a valid
image to work on Raven. Some convenience scripts are present in the `bin` folder
which are shortcuts to inner container binaries: `php`, `phive`. All the
following commands must be executed from the host, not inside the container.

```sh
bin/php # will run the php version from the container
bin/phive # will run the phive version from the container
```

A common getting started scenario will be:

```sh
# Install external binaries in the tools folder.
bin/phive update

# Install library dependencies.
bin/php tools/composer update

# Run development tools
bin/php vendor/bin/phpunit
bin/php vendor/bin/phpstan
bin/php vendor/bin/php-cs-fixer
```

If you want to allow your system to detect and use those binaries automatically,
you need to update your `PATH` environment variable:

```sh
export PATH="./bin:$PATH"

# Then you can run directly the scripts which will use the current PHP version
# based on your new PATH.
vendor/bin/phpunit
vendor/bin/phpstan
vendor/bin/php-cs-fixer
tools/composer
```

## Testing

[PHPUnit](https://phpunit.de/) is our unit testing framework of choice. The
version to be used is the one installed as a dev- dependency via composer:

```sh
vendor/bin/phpunit
```

Please ensure all new features or conditions are covered by unit tests.

## Code sniffing

This library use [PHP-CS-Fixer](https://github.com/FriendsOfPhp/PHP-CS-Fixer) to
validate and ensure PSR-12 compliance. The version to be used is the one
installed as a dev- dependency via composer:

```sh
vendor/bin/php-cs-fixer fix
```

## Static analysis

This library uses [PHPStan](https://phpstan.org/) to perform static analysis
over the code. The version to be used is the one installed as a dev- dependency
via composer:

```sh
vendor/bin/phpstan
```
