# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Changed
- Add more open version constraints to let install Raven in more projects.
  As a library, being too strict with dependencies is always bad.

## [0.1.0] - 2022-10-06
### Added
- Introduces a new object in the `CHStudio\Raven\Http\Factory` namespace named:
  `RequestUriParametersResolver`. It allows resolving URI parameters value using
  a `ValueResolverInterface` object.

## [0.0.0] - 2022-10-04

> **Welcome Raven !**
>
> This is the first release for this library, now we are able to test OpenAPI docs !

[Unreleased]: https://github.com/chstudio/raven/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/chstudio/raven/releases/tag/v0.1.0
[0.0.0]: https://github.com/chstudio/raven/releases/tag/v0.0.0
